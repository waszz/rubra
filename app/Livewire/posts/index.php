<?php

namespace App\Livewire\Posts;

use Livewire\Component;

class Index extends Component
{
    // 1. Declaramos la variable para controlar el modal
    public $mostrarModal = false;

    // 2. Función para abrir el modal
    public function abrirModal()
    {
        $this->mostrarModal = true;
    }

    // 3. Función para cerrar el modal
    public function cerrarModal()
    {
        $this->mostrarModal = false;
    }

    public function render()
    {
        return view('livewire.posts.index')->layout('layouts.app');
    }
}