<?php

namespace App\Livewire\Recurso;

use Livewire\Component;
use App\Models\Recurso;
use App\Models\PrecioHistorial;

class CrearRecurso extends Component
{
    public $nombre;
    public $codigo;
    public $tipo = 'material';
    public $unidad = 'un';
    public $precio_usd = 0;
    public $moneda = 'USD';
    public $region = 'Uruguay';
    public $vendedor;
    public $precio_estimativo = false;
    public $marca_modelo;
    public $observaciones;
    public $social_charges_percentage = 0;

    protected $rules = [
        'nombre'     => 'required|min:2',
        'tipo'       => 'required',
        'unidad'     => 'required',
        'precio_usd' => 'required|numeric|min:0',
        'social_charges_percentage' => 'nullable|numeric|min:0|max:100',
    ];

    public function guardar(): void
    {
        $this->validate();

        $recurso = Recurso::create([
            'user_id'                   => auth()->id(),
            'nombre'                    => $this->nombre,
            'codigo'                    => $this->codigo ?: null,
            'tipo'                      => $this->tipo,
            'unidad'                    => $this->unidad,
            'precio_usd'                => $this->precio_usd,
            'moneda'                    => $this->moneda,
            'region'                    => $this->region,
            'vendedor'                  => $this->vendedor,
            'precio_estimativo'         => $this->precio_estimativo,
            'marca_modelo'              => $this->marca_modelo,
            'observaciones'             => $this->observaciones,
            'social_charges_percentage' => $this->tipo === 'labor' ? $this->social_charges_percentage : 0,
        ]);

        // Registrar en historial de precios
        PrecioHistorial::create([
            'recurso_id' => $recurso->id,
            'precio_anterior' => null,
            'precio_nuevo' => $this->precio_usd,
            'razon' => 'Recurso creado',
        ]);

        $this->reset();
        $this->dispatch('recursoCreado');
        $this->dispatch('cerrarModalRecurso');
    }

    public function cancelar(): void
    {
        $this->dispatch('cerrarModalRecurso');
    }

    public function render()
    {
        return view('livewire.recurso.crear-recurso');
    }
}