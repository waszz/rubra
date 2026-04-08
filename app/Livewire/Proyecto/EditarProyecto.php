<?php

namespace App\Livewire\Proyecto;

use Livewire\Component;
use App\Models\Proyecto;
use App\Livewire\Concerns\AutorizaProyecto;

class EditarProyecto extends Component
{
    use AutorizaProyecto;

    public Proyecto $proyecto;

    public $nombre_proyecto;
    public $descripcion;
    public $notas;
    public $cliente;
    public $ubicacion;
    public $metros_cuadrados;
    public $beneficio;
    public $impuestos;
    public $fecha_inicio;
    public $mercado;
    public $moneda_base;
    public $horas_jornal;
    public $estado;

    protected $rules = [
        'nombre_proyecto'  => 'required|min:3',
        'metros_cuadrados' => 'required|numeric|min:0',
        'fecha_inicio'     => 'nullable|date',
        'descripcion'      => 'nullable|string',
        'notas'            => 'nullable|string',
        'cliente'          => 'nullable|string',
        'ubicacion'        => 'nullable|string',
        'beneficio'        => 'nullable|numeric|min:0|max:100',
        'impuestos'        => 'nullable|numeric|min:0|max:100',
        'mercado'          => 'nullable|string',
        'moneda_base'      => 'required|string',
        'horas_jornal'     => 'required|numeric|min:1',
        'estado'           => 'required|string',
    ];

    public function mount(Proyecto $proyecto)
    {
        $this->autorizarAcceso($proyecto);
        $this->proyecto        = $proyecto;
        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        // Refrescar el modelo desde la BD para obtener datos actuales
        $this->proyecto->refresh();
        
        $this->nombre_proyecto = $this->proyecto->nombre_proyecto;
        $this->descripcion     = $this->proyecto->descripcion;
        $this->notas           = $this->proyecto->notas;
        $this->cliente         = $this->proyecto->cliente;
        $this->ubicacion       = $this->proyecto->ubicacion;
        $this->metros_cuadrados = $this->proyecto->metros_cuadrados;
        $this->beneficio       = $this->proyecto->beneficio;
        $this->impuestos       = $this->proyecto->impuestos;
        $this->fecha_inicio    = $this->proyecto->fecha_inicio?->format('Y-m-d');
        $this->mercado         = $this->proyecto->mercado;
        $this->moneda_base     = $this->proyecto->moneda_base ?? 'USD';
        $this->horas_jornal    = $this->proyecto->horas_jornal ?? 8;
        $this->estado          = $this->proyecto->estado_obra ?? 'en_revision';
    }

    public function guardar()
    {
        $this->validate();

        $this->proyecto->update([
            'nombre_proyecto'  => $this->nombre_proyecto,
            'descripcion'      => $this->descripcion,
            'notas'            => $this->notas,
            'cliente'          => $this->cliente,
            'ubicacion'        => $this->ubicacion,
            'metros_cuadrados' => $this->metros_cuadrados,
            'beneficio'        => $this->beneficio,
            'impuestos'        => $this->impuestos,
            'fecha_inicio'     => $this->fecha_inicio,
            'mercado'          => $this->mercado,
            'moneda_base'      => $this->moneda_base,
            'horas_jornal'     => $this->horas_jornal,
            'estado_obra'      => $this->estado,
        ]);

        $this->dispatch('proyectoActualizado');
    }

    public function render()
    {
        return view('livewire.proyecto.editar-proyecto');
    }
}