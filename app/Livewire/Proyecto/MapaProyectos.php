<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;

class MapaProyectos extends Component
{
    public $filtroEstado = 'todos';
    public $filtroProyecto = '';

    public function getProyectosFiltradosProperty()
    {
        $user = auth()->user();

        $query = Proyecto::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('usuarios', function ($q2) use ($user) {
                  $q2->where('users.id', $user->id);
              });
        });

        if ($this->filtroEstado !== 'todos') {
            $query->where('estado_obra', $this->filtroEstado);
        }

        if ($this->filtroProyecto) {
            $query->where('nombre_proyecto', 'like', '%' . $this->filtroProyecto . '%');
        }

        return $query->whereNotNull('ubicacion_lat')
                    ->whereNotNull('ubicacion_lng')
                    ->get();
    }

    public function render()
    {
        return view('livewire.proyecto.mapa-proyectos', [
            'proyectosFiltrados' => $this->proyectosFiltrados,
        ])->layout('layouts.app');
    }
}
