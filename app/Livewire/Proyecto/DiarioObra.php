<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Proyecto;
use App\Models\ProyectoRecurso;
use App\Models\DiarioObra as DiarioObraModel;
use App\Livewire\Concerns\AutorizaProyecto;
use Carbon\Carbon;

class DiarioObra extends Component
{
    use WithFileUploads, AutorizaProyecto;

    public Proyecto $proyecto;

    public $rubros = [];

    // Modal principal
    public $mostrarModal = false;
    public $rubroId = null;
    public $rubroNombre = '';
    public $rubroUnidad = '';

    // Modal detalle
    public $mostrarDetalle = false;
    public $detalleRegistro = null;

    // Form
    public $fecha = '';
    public $avanceFisico = 0;
    public $cantidadHoy = 0;
    public $costoHoy = 0;
    public $notas = '';
    public $foto = null;

    // Historial
    public $historial = [];

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto = $proyecto;
        $this->fecha = Carbon::today()->format('Y-m-d');
        $this->cargarRubros();
    }

    /**
     * 📌 DETALLE REGISTRO
     */
    public function verDetalle($id)
    {
        $this->detalleRegistro = DiarioObraModel::find($id);

        if (!$this->detalleRegistro) return;

        $this->mostrarDetalle = true;
    }

    /**
     * 📌 CARGAR RUBROS
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
            ->get()
            ->map(function ($r) use ($lastAvances) {
                return [
                    'id' => $r->id,
                    'nombre' => $r->nombre,
                    'unidad' => $r->unidad,
                    'avance' => $lastAvances[$r->id] ?? 0,
                ];
            })
            ->toArray();
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
            ->get();
    }
}

  public function abrirModal($rubroId)
{
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

    // reset form
    $this->cantidadHoy = 0;
    $this->costoHoy = 0;
    $this->notas = '';
    $this->foto = null;

    // Llamamos al nuevo método
    $this->cargarHistorial();

    $this->mostrarModal = true;
}

    /**
     * 💾 GUARDAR (UPSERT)
     */
   public function guardarReporte()
{
    $this->validate([
        'fecha' => 'required|date',
        'avanceFisico' => 'required|numeric|min:0|max:100',
        'cantidadHoy' => 'required|numeric|min:0',
        'costoHoy' => 'required|numeric|min:0',
        'notas' => 'nullable|string|max:1000',
        'foto' => 'nullable|image|max:4096',
    ]);

    $fotoPath = $this->foto
        ? $this->foto->store('diario', 'public')
        : null;

    // Cambiamos a create() para generar una nueva fila siempre
    DiarioObraModel::create([
        'proyecto_id'         => $this->proyecto->id,
        'proyecto_recurso_id' => $this->rubroId,
        'fecha'               => $this->fecha,
        'avance_fisico'       => $this->avanceFisico,
        'cantidad_hoy'        => $this->cantidadHoy,
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
        'costoHoy',
        'notas',
        'foto',
        'historial'
    ]);

    // Mantenemos la fecha de hoy para el siguiente reporte
    $this->fecha = \Carbon\Carbon::today()->format('Y-m-d');

    $this->cargarRubros();
}

    public function render()
    {
        return view('livewire.proyecto.diario-obra')
            ->layout('layouts.app');
    }
}