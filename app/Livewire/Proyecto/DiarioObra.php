<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use App\Models\ComposicionItem;
use App\Models\DiarioObra as DiarioObraModel;
use App\Livewire\Concerns\AutorizaProyecto;
use Carbon\Carbon;

class DiarioObra extends Component
{
    use WithFileUploads, AutorizaProyecto;

    public Proyecto $proyecto;

    public $rubros = [];
    public $rubroExpandidoId = null;

    // Modal principal
    public $mostrarModal = false;
    public $rubroId = null;
    public $rubroNombre = '';
    public $rubroUnidad = '';

    // Modal detalle
    public $mostrarDetalle = false;
    public ?int $detalleRegistroId = null;

    // Modal confirmar eliminación
    public $mostrarConfirmar = false;
    public $eliminarId = null;

    // Form
    public $fecha = '';
    public $avanceFisico = 0;
    public $cantidadHoy = 0;
    public int $manoDeObra = 0;
    public float $horasHoy = 0;
    public $costoHoy = 0;
    public $notas = '';
    public $foto = null;

    public bool $modoLectura = false;

    // Límites del rubro activo
    public float $limiteM2 = 0;
    public float $limiteCosto = 0;
    public float $acumuladoM2 = 0;
    public float $acumuladoCosto = 0;

    // Planificado desde Gantt
    public float $horasPlanificadas = 0.0;
    public int   $trabajadoresPlanificados = 0;

    // Acumulado real de mano de obra
    public int   $acumuladoManoDeObra = 0;
    public float $acumuladoHoras = 0.0;

    // Historial
    public $historial = [];

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        if (!in_array($proyecto->estado_obra, ['ejecucion', 'pausado', 'finalizado'])) {
            abort(403, 'El Diario de Obra solo está disponible cuando el proyecto está en ejecución.');
        }
        $this->proyecto = $proyecto;
        $this->modoLectura = in_array($proyecto->estado_obra, ['pausado', 'finalizado']);
        $this->fecha = Carbon::today()->format('Y-m-d');
        $this->cargarRubros();
    }

    /**
     * 📌 DETALLE REGISTRO
     */
    public function verDetalle($id)
    {
        $registro = DiarioObraModel::find($id);
        if (!$registro) return;

        $this->detalleRegistroId = $registro->id;
        $this->mostrarDetalle = true;
    }

    /**
     * 📌 ELIMINAR REGISTRO
     */
    public function confirmarEliminar($id)
    {
        $this->eliminarId = $id;
        $this->mostrarConfirmar = true;
    }

    public function eliminarRegistro()
    {
        $registro = DiarioObraModel::find($this->eliminarId);
        if (!$registro || $registro->proyecto_id !== $this->proyecto->id) {
            $this->mostrarConfirmar = false;
            return;
        }

        if ($registro->foto_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($registro->foto_path);
        }

        $registro->delete();

        $this->mostrarConfirmar = false;
        $this->eliminarId = null;
        $this->mostrarDetalle = false;
        $this->detalleRegistroId = null;
        $this->cargarHistorial();
    }

    /**
     * 📌 EXPANDIR / COLAPSAR RUBRO
     */
    public function toggleRubro($id)
    {
        $this->rubroExpandidoId = $this->rubroExpandidoId === $id ? null : $id;
    }

    /**
     * 📌 CARGAR RUBROS (con subrubros y sus avances)
     */
    private function cargarRubros()
    {
        $lastAvances = DiarioObraModel::select('proyecto_recurso_id')
            ->selectRaw('MAX(avance_fisico) as avance')
            ->where('proyecto_id', $this->proyecto->id)
            ->groupBy('proyecto_recurso_id')
            ->pluck('avance', 'proyecto_recurso_id');

        $this->rubros = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id')
            ->with(['hijos' => fn($q) => $q->orderBy('orden')])
            ->orderBy('orden')
            ->get()
            ->map(function ($r) use ($lastAvances) {
                // Subrubros = children that are containers (have children themselves, or have no price = not a leaf)
                $subrubros = $r->hijos
                    ->filter(fn($sub) => is_null($sub->recurso_id) && ($sub->hijos->isNotEmpty() || ($sub->precio_usd ?? 0) == 0))
                    ->map(fn($sub) => [
                        'id'     => $sub->id,
                        'nombre' => $sub->nombre,
                        'unidad' => $sub->unidad,
                        'avance' => $lastAvances[$sub->id] ?? 0,
                    ])->values()->toArray();

                // Avance del rubro padre: promedio de subrubros si los tiene, sino su propio avance
                $avancePadre = count($subrubros) > 0
                    ? round(collect($subrubros)->avg('avance'), 1)
                    : ($lastAvances[$r->id] ?? 0);

                return [
                    'id'        => $r->id,
                    'nombre'    => $r->nombre,
                    'unidad'    => $r->unidad,
                    'avance'    => $avancePadre,
                    'subrubros' => $subrubros,
                ];
            })
            ->toArray();
    }

    public function incrementarManoDeObra(): void
    {
        $this->manoDeObra++;
    }

    public function decrementarManoDeObra(): void
    {
        $this->manoDeObra = max(0, $this->manoDeObra - 1);
    }

    public function updatedCantidadHoy()
    {
        $cantidad = (float) $this->cantidadHoy;
        if ($cantidad <= 0) {
            $this->avanceFisico = min(100, round($this->acumuladoM2 / $this->limiteM2 * 100, 1));
        } elseif ($this->limiteM2 > 0) {
            $this->avanceFisico = min(100, round(($this->acumuladoM2 + $cantidad) / $this->limiteM2 * 100, 1));
        }
    }

    public function updatedAvanceFisico()
    {
        $this->cargarHistorial();
    }
private function cargarHistorial()
{
    if ($this->rubroId) {
        $this->historial = DiarioObraModel::where('proyecto_id', $this->proyecto->id)
            ->where('proyecto_recurso_id', $this->rubroId)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get()
            ->map(fn($h) => [
                'id'            => $h->id,
                'fecha'         => $h->fecha->format('d/m/Y'),
                'avance_fisico' => $h->avance_fisico,
                'notas'         => $h->notas,
            ])
            ->toArray();
    }
}

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->cargarRubros();
    }

  public function abrirModal($rubroId)
{
    if ($this->modoLectura) return;
    $rubro = ProyectoRecurso::find($rubroId);
    if (!$rubro) return;

    $this->rubroId = $rubroId;
    $this->rubroNombre = $rubro->nombre;
    $this->rubroUnidad = $rubro->unidad;

    // último avance
    $this->avanceFisico = DiarioObraModel::where('proyecto_id', $this->proyecto->id)
        ->where('proyecto_recurso_id', $rubroId)
        ->orderByDesc('fecha')
        ->orderByDesc('id')
        ->value('avance_fisico') ?? 0;

    // Límite de cantidad: campo cantidad del propio subrubro
    $this->limiteM2 = (float) ($rubro->cantidad ?? $this->proyecto->metros_cuadrados ?? 0);

    // Límite de costo: presupuesto total del rubro (suma recursiva de sus hijos)
    $rubro->load(['hijos.hijos.hijos.hijos.hijos']);
    $this->limiteCosto = $this->calcularPresupuestoRubro($rubro);

    // Acumulado ya registrado para este rubro
    $this->acumuladoM2    = (float) DiarioObraModel::where('proyecto_id', $this->proyecto->id)
        ->where('proyecto_recurso_id', $rubroId)
        ->sum('cantidad_hoy');
    $this->acumuladoCosto = (float) DiarioObraModel::where('proyecto_id', $this->proyecto->id)
        ->where('proyecto_recurso_id', $rubroId)
        ->sum('costo_hoy');

    // Planificado desde Gantt
    $this->horasPlanificadas        = $this->calcularHorasSubrubro($rubro);
    $this->trabajadoresPlanificados = max(0, (int)($rubro->trabajadores ?? 0));

    // Acumulado real de mano de obra
    $this->acumuladoManoDeObra = (int) DiarioObraModel::where('proyecto_id', $this->proyecto->id)
        ->where('proyecto_recurso_id', $rubroId)
        ->sum('mano_de_obra');
    $this->acumuladoHoras = (float) DiarioObraModel::where('proyecto_id', $this->proyecto->id)
        ->where('proyecto_recurso_id', $rubroId)
        ->sum('horas_hoy');

    // reset form
    $this->cantidadHoy = 0;
    $this->manoDeObra = 0;
    $this->horasHoy = 0;
    $this->costoHoy = 0;
    $this->notas = '';
    $this->foto = null;

    // Llamamos al nuevo método
    $this->cargarHistorial();

    $this->mostrarModal = true;
}

    /**
     * Calcula horas de mano de obra planificadas para un subrubro
     * (misma lógica que GanttProyecto::calcularHorasSubrubro).
     */
    private function calcularHorasSubrubro(ProyectoRecurso $subrubro, float $factorAcumulado = 1.0): float
    {
        $cant  = (float) ($subrubro->cantidad ?? 1) * $factorAcumulado;
        $horas = 0.0;

        $hijos = $subrubro->relationLoaded('hijos')
            ? $subrubro->hijos
            : $subrubro->hijos()->with('recurso')->get();

        foreach ($hijos as $child) {
            $rec = $child->recurso;

            if (!$rec) {
                $horas += $this->calcularHorasSubrubro($child, $cant);
                continue;
            }

            if (in_array($rec->tipo, ['labor', 'mano_obra'])) {
                $horas += (float) ($child->cantidad ?? 0) * $cant;
            } elseif ($rec->tipo === 'composition') {
                $items = ComposicionItem::where('composicion_id', $rec->id)
                    ->with('recursoBase')
                    ->get();
                foreach ($items as $item) {
                    $base = $item->recursoBase;
                    if ($base && in_array($base->tipo, ['labor', 'mano_obra'])) {
                        $horas += (float) ($item->cantidad ?? 0) * (float) ($child->cantidad ?? 1) * $cant;
                    }
                }
            }
        }

        return round($horas, 2);
    }

    /**
     * Calcula el presupuesto total de un rubro sumando recursivamente los precio_usd de sus hojas.
     */
    private function calcularPresupuestoRubro(ProyectoRecurso $nodo): float
    {
        if (!is_null($nodo->recurso_id)) {
            return (float)($nodo->precio_usd ?? 0) * (float)($nodo->cantidad ?? 1);
        }
        // Imported leaf without catalog match: no children, has a price
        $hijos = $nodo->hijos ?? collect([]);
        if ($hijos->isEmpty() && ($nodo->precio_usd ?? 0) > 0) {
            return (float)$nodo->precio_usd * (float)($nodo->cantidad ?? 1);
        }
        $total = 0.0;
        foreach ($hijos as $hijo) {
            $total += $this->calcularPresupuestoRubro($hijo) * (float)($nodo->cantidad ?? 1);
        }
        return $total;
    }

    /**
     * 💾 GUARDAR (UPSERT)
     */
   public function guardarReporte()
{
    if ($this->modoLectura) return;
    $maxCantidad = $this->limiteM2 > 0 ? max(0, $this->limiteM2 - $this->acumuladoM2) : PHP_INT_MAX;

    $this->validate([
        'fecha'        => 'required|date',
        'avanceFisico' => 'nullable|numeric|min:0|max:100',
        'cantidadHoy'  => ['required', 'numeric', 'min:0', "max:{$maxCantidad}"],
        'manoDeObra'   => ['required', 'integer', 'min:0'],
        'horasHoy'     => ['required', 'numeric', 'min:0'],
        'costoHoy'     => ['required', 'numeric'],
        'notas'        => 'nullable|string|max:1000',
        'foto'         => 'nullable|image|max:4096',
    ], [
        'cantidadHoy.max' => "La cantidad supera el límite del proyecto ({$this->limiteM2} m²). Disponible: " . number_format($maxCantidad, 2) . ' m².',
    ]);

    $fotoPath = $this->foto
        ? $this->foto->store('diario', 'public')
        : null;

    // Cambiamos a create() para generar una nueva fila siempre
    DiarioObraModel::create([
        'proyecto_id'         => $this->proyecto->id,
        'proyecto_recurso_id' => $this->rubroId,
        'user_id'             => auth()->id(),
        'fecha'               => $this->fecha,
        'avance_fisico'       => (float) ($this->avanceFisico ?? 0),
        'cantidad_hoy'        => $this->cantidadHoy,
        'mano_de_obra'        => $this->manoDeObra,
        'horas_hoy'           => $this->horasHoy,
        'costo_hoy'           => $this->costoHoy,
        'notas'               => $this->notas,
        'foto_path'           => $fotoPath,
    ]);

    $this->reset([
        'mostrarModal',
        'rubroId',
        'rubroNombre',
        'rubroUnidad',
        'avanceFisico',
        'cantidadHoy',
        'manoDeObra',
        'horasHoy',
        'costoHoy',
        'notas',
        'foto',
        'historial',
        'limiteM2',
        'limiteCosto',
        'acumuladoM2',
        'acumuladoCosto',
        'horasPlanificadas',
        'trabajadoresPlanificados',
        'acumuladoManoDeObra',
        'acumuladoHoras',
    ]);

    // Mantenemos la fecha de hoy para el siguiente reporte
    $this->fecha = \Carbon\Carbon::today()->format('Y-m-d');

    $this->cargarRubros();
}

    public function render()
    {
        return view('livewire.proyecto.diario-obra', [
            'detalleRegistro' => $this->detalleRegistroId
                ? DiarioObraModel::find($this->detalleRegistroId)
                : null,
        ])->layout('layouts.app');
    }
}