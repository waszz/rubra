<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;
use App\Models\ProyectoRecurso;
use App\Models\ComposicionItem;
use Carbon\Carbon;

class GanttProyecto extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;

    public $fechaInicioProyecto;
    public $semanas = [];
    public $dias    = [];
    public $rubros  = [];

    // Vista y configuración de días laborales
    public string $vistaGantt     = 'semanas';
    public bool   $trabajaSabado  = false;
    public bool   $trabajaDomingo = false;

    // Modal editar fechas
    public $mostrarModalFechas  = false;
    public $editFechaId         = null;
    public $editFechaInicio     = '';
    public $editFechaFin        = '';
    public $editNombre          = '';
    public float $editHorasTotales   = 0.0;
    public int   $editDiasLaborables = 0;

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto = $proyecto;
        $this->cargarGantt();
    }

    private function cargarGantt()
    {
        $inicio = $this->proyecto->fecha_inicio
            ? Carbon::parse($this->proyecto->fecha_inicio)
            : Carbon::now()->startOfWeek();

        $this->fechaInicioProyecto = $inicio->format('Y-m-d');

        // Cargar todos los rubros raíz con sus hijos
        $rubros = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->with([
                'hijos',
                'hijos.dependeDe',
                'hijos.dependientes',
                'hijos.hijos',
                'hijos.hijos.recurso',
            ])
            ->get();

        // Calcular rango total del proyecto
        $todasLasFechas = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNotNull('fecha_fin')
            ->pluck('fecha_fin')
            ->map(fn($f) => Carbon::parse($f));

        // El mínimo es el 31 de diciembre del año de inicio del proyecto
        $finMinimo = $inicio->copy()->endOfYear();

        $fechaFin = $todasLasFechas->count()
            ? $todasLasFechas->max()->max($finMinimo)
            : $finMinimo;

        // Generar semanas entre inicio y fin
        $semanas      = [];
        $cursor       = $inicio->copy()->startOfWeek();
        $fin          = Carbon::parse($fechaFin)->endOfWeek();
        $totalSemanas = 0;

        while ($cursor->lte($fin)) {
            $semanas[] = [
                'label' => 'S' . $cursor->weekOfYear,
                'mes'   => $cursor->translatedFormat('M'),
                'inicio'=> $cursor->format('Y-m-d'),
                'fin'   => $cursor->copy()->endOfWeek()->format('Y-m-d'),
            ];
            $cursor->addWeek();
            $totalSemanas++;
        }

        $this->semanas = $semanas;

        // Generar array de días (vista por día)
        $diasArr = [];
        $cursorD = $inicio->copy();
        $finD    = Carbon::parse($fechaFin);
        while ($cursorD->lte($finD)) {
            $diasArr[] = [
                'fecha' => $cursorD->format('Y-m-d'),
                'label' => $cursorD->format('j'),
                'dow'   => $cursorD->dayOfWeek,   // 0=Dom, 6=Sab
                'mes'   => $cursorD->translatedFormat('M Y'),
            ];
            $cursorD->addDay();
        }
        $this->dias = $diasArr;

        // Aplanar rubros para el Gantt (categorías + sub-rubros)
        $filas = [];
        // Mapa id → fecha_fin calculada (para propagar a dependientes)
        $fechaFinCalculada = [];

        foreach ($rubros as $rubro) {
            $filas[] = [
                'id'              => $rubro->id,
                'nombre'          => $rubro->nombre,
                'nivel'           => 0,
                'fecha_inicio'    => $rubro->fecha_inicio?->format('Y-m-d'),
                'fecha_fin'       => $rubro->fecha_fin?->format('Y-m-d'),
                'es_categoria'    => true,
                'depends_on_id'   => null,
                'depends_on_nombre' => null,
                'dependientes'    => [],
                'horas_totales'   => null,
            ];

            foreach ($rubro->hijos as $hijo) {
                $horasTotales    = $this->calcularHorasSubrubro($hijo);
                $fechaInicioHijo = $hijo->fecha_inicio?->format('Y-m-d');

                // Si depende de otro subrubro, la fecha inicio es la fecha fin
                // calculada del predecesor + 1 día laborable
                if ($hijo->depends_on_id && isset($fechaFinCalculada[$hijo->depends_on_id])) {
                    $fechaInicioHijo = Carbon::parse($fechaFinCalculada[$hijo->depends_on_id])
                        ->addDay()
                        ->format('Y-m-d');
                    // Snap al siguiente día laborable
                    $fechaInicioHijo = $this->snapToNextDiaLaboral($fechaInicioHijo);
                }

                // Calcular fecha_fin:
                // - Si tiene fecha_fin guardada en BD → respetarla siempre
                // - Si NO tiene fecha_fin pero tiene horas y fecha_inicio → auto-calcular
                $fechaFinGuardada = $hijo->fecha_fin?->format('Y-m-d');
                if ($fechaFinGuardada) {
                    $fechaFinHijo = $fechaFinGuardada;
                } elseif ($horasTotales > 0 && $fechaInicioHijo) {
                    $fechaFinHijo = $this->calcularFechaFinPorHoras($fechaInicioHijo, $horasTotales);
                } else {
                    $fechaFinHijo = null;
                }

                // Guardar en el mapa para que dependientes lo usen
                if ($fechaFinHijo) {
                    $fechaFinCalculada[$hijo->id] = $fechaFinHijo;
                }

                $filas[] = [
                    'id'              => $hijo->id,
                    'nombre'          => $hijo->nombre,
                    'nivel'           => 1,
                    'fecha_inicio'    => $fechaInicioHijo,
                    'fecha_fin'       => $fechaFinHijo,
                    'es_categoria'    => false,
                    'depends_on_id'     => $hijo->depends_on_id,
                    'depends_on_nombre' => $hijo->dependeDe?->nombre,
                    'dependientes'      => $hijo->dependientes->pluck('nombre')->toArray(),
                    'horas_totales'     => $horasTotales,
                ];
            }
        }

        $this->rubros = $filas;
    }

    /**
     * Calcula horas de mano de obra totales de un subrubro.
     * Fórmula: suma(cantidad_labor_item × cantidad_subrubro)
     * - Recursos directos tipo labor/mano_obra: recurso_hijo.cantidad × subrubro.cantidad
     * - Composiciones APU: sum(item.cantidad × subrubro.cantidad) para items tipo labor
     */
    private function calcularHorasSubrubro($subrubro): float
    {
        $cantSubrubro = (float) ($subrubro->cantidad ?? 1);
        $horas = 0.0;

        foreach ($subrubro->hijos as $recursoPR) {
            $rec = $recursoPR->recurso;
            if (!$rec) continue;

            if (in_array($rec->tipo, ['labor', 'mano_obra'])) {
                $horas += (float) ($recursoPR->cantidad ?? 0) * $cantSubrubro;

            } elseif ($rec->tipo === 'composition') {
                $items = ComposicionItem::where('composicion_id', $rec->id)
                    ->with('recursoBase')
                    ->get();
                foreach ($items as $item) {
                    $base = $item->recursoBase;
                    if ($base && in_array($base->tipo, ['labor', 'mano_obra'])) {
                        $horas += (float) ($item->cantidad ?? 0) * (float) ($recursoPR->cantidad ?? 1) * $cantSubrubro;
                    }
                }
            }
        }

        return round($horas, 2);
    }

    public function abrirModalFechas($id)
    {
        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $this->editFechaId     = $id;
        $this->editNombre      = $nodo->nombre;
        $this->editFechaInicio = $nodo->fecha_inicio?->format('Y-m-d') ?? '';
        $this->editFechaFin    = $nodo->fecha_fin?->format('Y-m-d') ?? '';

        // Cargar horas totales de mano de obra para este subrubro
        $filaRubro = collect($this->rubros)->firstWhere('id', $id);
        $this->editHorasTotales = $filaRubro ? (float)($filaRubro['horas_totales'] ?? 0) : 0.0;

        // Contar días laborables iniciales
        if ($this->editFechaInicio && $this->editFechaFin) {
            $this->editDiasLaborables = $this->contarDiasLaborables($this->editFechaInicio, $this->editFechaFin);
        }

        $this->mostrarModalFechas = true;
    }

    /**
     * Cuando cambia la fecha de inicio, snappear al próximo día laborable
     * y recalcular fecha fin a partir de las horas totales.
     */
    public function updatedEditFechaInicio()
    {
        if (!$this->editFechaInicio) return;

        // Snap a próximo día laborable si cayó en fin de semana no laboral
        $this->editFechaInicio = $this->snapToNextDiaLaboral($this->editFechaInicio);

        if ($this->editHorasTotales > 0) {
            $this->editFechaFin = $this->calcularFechaFinPorHoras($this->editFechaInicio, $this->editHorasTotales);
        }

        if ($this->editFechaInicio && $this->editFechaFin) {
            $this->editDiasLaborables = $this->contarDiasLaborables($this->editFechaInicio, $this->editFechaFin);
        }
    }

    public function toggleSabado()
    {
        $this->trabajaSabado = !$this->trabajaSabado;
        // Recalcular barras del gantt con nueva config
        $this->cargarGantt();
        // Si el modal está abierto, también actualizar fecha fin del modal
        if ($this->mostrarModalFechas && $this->editFechaInicio) {
            $this->editFechaInicio = $this->snapToNextDiaLaboral($this->editFechaInicio);
            if ($this->editHorasTotales > 0) {
                $this->editFechaFin = $this->calcularFechaFinPorHoras($this->editFechaInicio, $this->editHorasTotales);
            }
            if ($this->editFechaFin) {
                $this->editDiasLaborables = $this->contarDiasLaborables($this->editFechaInicio, $this->editFechaFin);
            }
        }
    }

    public function toggleDomingo()
    {
        $this->trabajaDomingo = !$this->trabajaDomingo;
        // Recalcular barras del gantt con nueva config
        $this->cargarGantt();
        // Si el modal está abierto, también actualizar fecha fin del modal
        if ($this->mostrarModalFechas && $this->editFechaInicio) {
            $this->editFechaInicio = $this->snapToNextDiaLaboral($this->editFechaInicio);
            if ($this->editHorasTotales > 0) {
                $this->editFechaFin = $this->calcularFechaFinPorHoras($this->editFechaInicio, $this->editHorasTotales);
            }
            if ($this->editFechaFin) {
                $this->editDiasLaborables = $this->contarDiasLaborables($this->editFechaInicio, $this->editFechaFin);
            }
        }
    }

    /**
     * Avanza la fecha al primer día laborable igual o posterior.
     */
    private function snapToNextDiaLaboral(string $fecha): string
    {
        $cursor = Carbon::parse($fecha);
        // Máximo 7 intentos para evitar loop infinito si ambos están desactivados
        for ($i = 0; $i < 7; $i++) {
            $dow = $cursor->dayOfWeek;
            if ($dow === 6 && !$this->trabajaSabado) { $cursor->addDay(); continue; }
            if ($dow === 0 && !$this->trabajaDomingo) { $cursor->addDay(); continue; }
            break;
        }
        return $cursor->format('Y-m-d');
    }

    /**
     * Cuando el usuario cambia manualmente la fecha fin, solo recontamos días.
     */
    public function updatedEditFechaFin()
    {
        if ($this->editFechaInicio && $this->editFechaFin) {
            $this->editDiasLaborables = $this->contarDiasLaborables($this->editFechaInicio, $this->editFechaFin);
        }
    }

    /**
     * Cuenta los días laborables (inclusive) entre dos fechas.
     */
    private function contarDiasLaborables(string $inicio, string $fin): int
    {
        $cursor = Carbon::parse($inicio);
        $finC   = Carbon::parse($fin);
        $count  = 0;
        while ($cursor->lte($finC)) {
            $dow = $cursor->dayOfWeek;
            $esLaboral = true;
            if ($dow === 6 && !$this->trabajaSabado)  $esLaboral = false;
            if ($dow === 0 && !$this->trabajaDomingo) $esLaboral = false;
            if ($esLaboral) $count++;
            $cursor->addDay();
        }
        return $count;
    }

    /**
     * Calcula la fecha fin partiendo de fecha_inicio sumando N días laborables.
     * N = ceil(horas / 8), saltando sábados y domingos según la config.
     */
    private function calcularFechaFinPorHoras(string $fechaInicio, float $horas): string
    {
        $diasNecesarios = (int) ceil($horas / 8);
        if ($diasNecesarios <= 0) return $fechaInicio;

        $cursor       = Carbon::parse($fechaInicio);
        $diasContados = 0;

        while ($diasContados < $diasNecesarios) {
            $dow = $cursor->dayOfWeek;
            $esLaboral = true;
            if ($dow === 6 && !$this->trabajaSabado)  $esLaboral = false;
            if ($dow === 0 && !$this->trabajaDomingo) $esLaboral = false;

            if ($esLaboral) {
                $diasContados++;
            }

            if ($diasContados < $diasNecesarios) {
                $cursor->addDay();
            }
        }

        return $cursor->format('Y-m-d');
    }

    public function eliminarFechas()
    {
        $nodo = ProyectoRecurso::find($this->editFechaId);
        if (!$nodo) return;

        $nodo->update(['fecha_inicio' => null, 'fecha_fin' => null]);

        $this->reset(['mostrarModalFechas', 'editFechaId', 'editNombre', 'editFechaInicio', 'editFechaFin', 'editHorasTotales', 'editDiasLaborables']);
        $this->cargarGantt();
    }

    public function guardarFechas()
{
    $this->validate([
        'editFechaInicio' => 'required|date',
        'editFechaFin'    => 'required|date|after_or_equal:editFechaInicio',
    ]);

    // Validar que no sea anterior al inicio del proyecto
    if ($this->proyecto->fecha_inicio) {
        $minDate = Carbon::parse($this->proyecto->fecha_inicio);
        if (Carbon::parse($this->editFechaInicio)->lt($minDate)) {
            $this->addError('editFechaInicio', 'No puede ser anterior al inicio del proyecto (' . $minDate->format('d/m/Y') . ').');
            return;
        }
    }

    $nodo = ProyectoRecurso::find($this->editFechaId);
    if (!$nodo) return;

    // Si tiene padre, validar que no supere sus fechas
    if ($nodo->parent_id) {
        $padre = ProyectoRecurso::find($nodo->parent_id);

        if ($padre && $padre->fecha_inicio && $padre->fecha_fin) {
            $inicioHijo = Carbon::parse($this->editFechaInicio);
            $finHijo    = Carbon::parse($this->editFechaFin);
            $inicioPadre = Carbon::parse($padre->fecha_inicio);
            $finPadre    = Carbon::parse($padre->fecha_fin);

            if ($inicioHijo->lt($inicioPadre)) {
                $this->addError('editFechaInicio', 
                    'La fecha de inicio no puede ser anterior al rubro padre (' . $inicioPadre->format('d/m/Y') . ').');
                return;
            }

            if ($finHijo->gt($finPadre)) {
                $this->addError('editFechaFin', 
                    'La fecha de fin no puede superar al rubro padre (' . $finPadre->format('d/m/Y') . ').');
                return;
            }
        }
    }

    // Si es padre, validar que sus hijos queden dentro
    if (!$nodo->parent_id) {
        $inicioNuevo = Carbon::parse($this->editFechaInicio);
        $finNuevo    = Carbon::parse($this->editFechaFin);

        $hijosConflicto = $nodo->hijos()
            ->where(function($q) use ($inicioNuevo, $finNuevo) {
                $q->where('fecha_inicio', '<', $inicioNuevo)
                  ->orWhere('fecha_fin', '>', $finNuevo);
            })
            ->pluck('nombre');

        if ($hijosConflicto->count()) {
            $this->addError('editFechaFin', 
                'Los siguientes sub-rubros superan este rango: ' . $hijosConflicto->join(', ') . '.');
            return;
        }
    }

    $nodo->update([
        'fecha_inicio' => $this->editFechaInicio,
        'fecha_fin'    => $this->editFechaFin,
    ]);

    // Propagar inicio a dependientes: su fecha_inicio = fecha_fin del nodo + 1 día
    $nuevaFechaFin = Carbon::parse($this->editFechaFin);
    $dependientes = ProyectoRecurso::where('depends_on_id', $nodo->id)->get();
    foreach ($dependientes as $dep) {
        $inicioNuevo = $nuevaFechaFin->copy()->addDay();
        // Mantener la duración original si tenía fechas previas
        if ($dep->fecha_inicio && $dep->fecha_fin) {
            $duracion = Carbon::parse($dep->fecha_inicio)->diffInDays(Carbon::parse($dep->fecha_fin));
            $dep->update([
                'fecha_inicio' => $inicioNuevo->format('Y-m-d'),
                'fecha_fin'    => $inicioNuevo->copy()->addDays($duracion)->format('Y-m-d'),
            ]);
        } else {
            $dep->update(['fecha_inicio' => $inicioNuevo->format('Y-m-d')]);
        }
    }

    $this->reset(['mostrarModalFechas', 'editFechaId', 'editNombre', 'editFechaInicio', 'editFechaFin', 'editHorasTotales', 'editDiasLaborables']);
    $this->cargarGantt();
}

    public function moverBarra(int $id, string $nuevaFechaInicio, string $nuevaFechaFin): void
    {
        try {
            $inicio = Carbon::parse($nuevaFechaInicio);
            $fin    = Carbon::parse($nuevaFechaFin);
        } catch (\Exception $e) {
            return;
        }

        if ($fin->lt($inicio)) return;

        // Respetar fecha mínima del proyecto
        if ($this->proyecto->fecha_inicio) {
            $minDate = Carbon::parse($this->proyecto->fecha_inicio);
            if ($inicio->lt($minDate)) {
                $duracion = $inicio->diffInDays($fin);
                $inicio   = $minDate->copy();
                $fin      = $inicio->copy()->addDays($duracion);
            }
        }

        // Snap inicio al próximo día laborable y preservar duración
        $duracion = Carbon::parse($nuevaFechaInicio)->diffInDays(Carbon::parse($nuevaFechaFin));
        $inicio   = Carbon::parse($this->snapToNextDiaLaboral($inicio));
        $fin      = $inicio->copy()->addDays($duracion);

        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $nodo->update([
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'fecha_fin'    => $fin->format('Y-m-d'),
        ]);

        // Propagar a dependientes (igual que guardarFechas)
        $dependientes = ProyectoRecurso::where('depends_on_id', $id)->get();
        foreach ($dependientes as $dep) {
            $inicioNuevo = $fin->copy()->addDay();
            if ($dep->fecha_inicio && $dep->fecha_fin) {
                $durDep = Carbon::parse($dep->fecha_inicio)->diffInDays(Carbon::parse($dep->fecha_fin));
                $dep->update([
                    'fecha_inicio' => $inicioNuevo->format('Y-m-d'),
                    'fecha_fin'    => $inicioNuevo->copy()->addDays($durDep)->format('Y-m-d'),
                ]);
            } else {
                $dep->update(['fecha_inicio' => $inicioNuevo->format('Y-m-d')]);
            }
        }

        $this->cargarGantt();
    }

    public function render()
    {
        return view('livewire.proyecto.gantt-proyecto')
            ->layout('layouts.app');
    }
}