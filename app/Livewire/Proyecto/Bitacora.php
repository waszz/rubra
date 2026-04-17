<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;
use App\Models\DiarioObra;
use App\Models\ProyectoRecurso;
use Barryvdh\DomPDF\Facade\Pdf;

class Bitacora extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;
    
    // Propiedades para los filtros
    public $searchFecha = '';
    public $searchRubro = '';
    public bool $modoProyecto = true;

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto = $proyecto;
    }

    public function exportarPDF()
{
    // Obtenemos los mismos registros con los filtros actuales
    $query = DiarioObra::with('recurso', 'user')
        ->where('proyecto_id', $this->proyecto->id);

    if ($this->searchFecha) {
        $query->whereDate('fecha', $this->searchFecha);
    }

    if ($this->searchRubro) {
        $query->where('proyecto_recurso_id', $this->searchRubro);
    }

    $registros = $query->orderBy('created_at', 'desc')->get();

    // Convertir fotos a base64 para DomPDF (no puede cargar URLs del servidor)
    $registros->each(function ($reg) {
        $reg->foto_base64 = null;
        if ($reg->foto_path) {
            $disco = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disco->exists($reg->foto_path)) {
                $mime = $disco->mimeType($reg->foto_path);
                $datos = $disco->get($reg->foto_path);
                $reg->foto_base64 = 'data:' . $mime . ';base64,' . base64_encode($datos);
            }
        }
    });

    // Cargamos una vista dedicada para el PDF
    $pdf = Pdf::loadView('pdf.bitacora', [
        'proyecto' => $this->proyecto,
        'registros' => $registros,
        'fecha_exportacion' => now()->format('d/m/Y H:i')
    ]);

    // Retornamos el archivo para descargar
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, 'bitacora-' . $this->proyecto->id . '.pdf');
}

    public function render()
    {
        // Iniciamos la consulta
        $query = DiarioObra::with('recurso', 'user')
            ->where('proyecto_id', $this->proyecto->id);

        // Filtro por Fecha (si existe valor)
        if ($this->searchFecha) {
            $query->whereDate('fecha', $this->searchFecha);
        }

        // Filtro por Rubro (si existe valor)
        if ($this->searchRubro) {
            $query->where('proyecto_recurso_id', $this->searchRubro);
        }

        $registros = $query->orderBy('created_at', 'desc')->get();

        // Obtenemos los rubros del proyecto para el Select del filtro
        $rubrosDisponibles = ProyectoRecurso::where('proyecto_id', $this->proyecto->id)
            ->whereNull('parent_id') // Solo rubros principales
            ->get();

        return view('livewire.proyecto.bitacora', [
            'registros' => $registros,
            'rubrosDisponibles' => $rubrosDisponibles
        ])->layout('layouts.app');
    }

    // Método para limpiar filtros rápidamente
    public function limpiarFiltros()
    {
        $this->reset(['searchFecha', 'searchRubro']);
    }
}