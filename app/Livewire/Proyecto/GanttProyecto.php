<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;
use App\Models\ProyectoRecurso;
use Carbon\Carbon;

class GanttProyecto extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;

    public $fechaInicioProyecto;
    public $semanas = [];
    public $rubros  = [];

    // Modal editar fechas
    public $mostrarModalFechas = false;
    public $editFechaId        = null;
    public $editFechaInicio    = '';
    public $editFechaFin       = '';
    public $editNombre         = '';

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
            ->with(['hijos', 'hijos.dependeDe'])
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

        // Aplanar rubros para el Gantt (categorías + sub-rubros)
        $filas = [];
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
            ];

            foreach ($rubro->hijos as $hijo) {
                $filas[] = [
                    'id'              => $hijo->id,
                    'nombre'          => $hijo->nombre,
                    'nivel'           => 1,
                    'fecha_inicio'    => $hijo->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin'       => $hijo->fecha_fin?->format('Y-m-d'),
                    'es_categoria'    => false,
                    'depends_on_id'   => $hijo->depends_on_id,
                    'depends_on_nombre' => $hijo->dependeDe?->nombre,
                ];
            }
        }

        $this->rubros = $filas;
    }

    public function abrirModalFechas($id)
    {
        $nodo = ProyectoRecurso::find($id);
        if (!$nodo) return;

        $this->editFechaId     = $id;
        $this->editNombre      = $nodo->nombre;
        $this->editFechaInicio = $nodo->fecha_inicio?->format('Y-m-d') ?? '';
        $this->editFechaFin    = $nodo->fecha_fin?->format('Y-m-d') ?? '';
        $this->mostrarModalFechas = true;
    }

    public function guardarFechas()
{
    $this->validate([
        'editFechaInicio' => 'required|date',
        'editFechaFin'    => 'required|date|after_or_equal:editFechaInicio',
    ]);

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

    $this->reset(['mostrarModalFechas', 'editFechaId', 'editNombre', 'editFechaInicio', 'editFechaFin']);
    $this->cargarGantt();
}

    public function render()
    {
        return view('livewire.proyecto.gantt-proyecto')
            ->layout('layouts.app');
    }
}