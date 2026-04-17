<?php

namespace App\Livewire\Recurso;

use App\Models\Recurso;
use App\Models\Proyecto;
use Livewire\Component;

class RecursosCompartidos extends Component
{
    public string $buscar = '';
    public string $filtroTipo = '';
    public string $vista = 'grid';
    public int $perPage = 20;

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->puedeCompartido('recursos_compartidos')) {
            abort(403, 'No tienes permiso para esta sección.');
        }
    }

    public function updatingBuscar(): void { $this->perPage = 20; }
    public function updatingFiltroTipo(): void { $this->perPage = 20; }
    public function loadMore(): void { $this->perPage += 20; }

    public function render()
    {
        $user = auth()->user();

        // Proyectos que me compartieron (donde soy invitado, no dueño)
        $sharedProjectIds = Proyecto::whereHas('usuarios', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->whereNot('user_id', $user->id)
            ->pluck('id');

        // Recursos que se usan en esos proyectos (vía proyecto_recursos)
        $recursoIds = \App\Models\ProyectoRecurso::whereIn('proyecto_id', $sharedProjectIds)
            ->whereNotNull('recurso_id')
            ->pluck('recurso_id')
            ->unique();

        // Owner IDs para mostrar quién compartió
        $ownerIds = Proyecto::whereIn('id', $sharedProjectIds)->pluck('user_id')->unique()->filter();

        $query = Recurso::with('items.recursoBase')
            ->whereIn('id', $recursoIds)
            ->when($this->buscar, fn($q) => $q->where('nombre', 'like', '%'.$this->buscar.'%'))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->orderBy('nombre');

        $total    = $query->count();
        $recursos = $query->take($this->perPage)->get();
        $hasMore  = $total > $recursos->count();

        $owners = \App\Models\User::whereIn('id', $ownerIds)->get(['id', 'name']);

        return view('livewire.recurso.recursos-compartidos', [
            'recursos' => $recursos,
            'total'    => $total,
            'hasMore'  => $hasMore,
            'owners'   => $owners,
        ])->layout('layouts.app');
    }
}
