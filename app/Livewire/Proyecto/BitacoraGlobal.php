<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Models\DiarioObra;
use App\Models\ProyectoRecurso;

class BitacoraGlobal extends Component
{
    public $proyectoId   = null;
    public $searchFecha  = '';
    public $searchRubro  = '';
    public bool $modoProyecto = false;

    public function mount($proyectoId = null): void
    {
        $this->modoProyecto = $proyectoId !== null;
        $this->proyectoId   = $proyectoId ?? optional(
            Proyecto::where('user_id', auth()->id())->first()
        )->id;
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['searchFecha', 'searchRubro']);
    }

    public function render()
    {
        $proyectos = Proyecto::where('user_id', auth()->id())
            ->orderBy('nombre_proyecto')
            ->get();

        $proyecto = $proyectos->firstWhere('id', $this->proyectoId);

        $registros         = collect();
        $rubrosDisponibles = collect();

        if ($proyecto) {
            $query = DiarioObra::with('recurso')
                ->where('proyecto_id', $proyecto->id);

            if ($this->searchFecha) {
                $query->whereDate('fecha', $this->searchFecha);
            }

            if ($this->searchRubro) {
                $query->where('proyecto_recurso_id', $this->searchRubro);
            }

            $registros = $query->orderBy('created_at', 'desc')->get();

            $rubrosDisponibles = ProyectoRecurso::where('proyecto_id', $proyecto->id)
                ->whereNull('parent_id')
                ->get();
        }

        return view('livewire.proyecto.bitacora-global', [
            'proyectos'         => $proyectos,
            'proyecto'          => $proyecto,
            'registros'         => $registros,
            'rubrosDisponibles' => $rubrosDisponibles,
        ])->layout('layouts.app');
    }
}