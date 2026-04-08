<?php

namespace App\Livewire\Recurso;

use Livewire\Component;
use App\Models\Recurso;

class CrearComposicion extends Component
{
    public string $nombre = '';
    public string $unidad = 'un';
    public array  $items  = [];

    // ── Modal selector de recursos ────────────────────────────
    public bool   $modalSelector  = false;
    public string $buscarSelector = '';
    public string $filtroTipo     = '';

    protected $rules = [
        'nombre' => 'required|min:2',
        'unidad' => 'required',
    ];

    public function abrirSelector(): void
    {
        $this->buscarSelector = '';
        $this->filtroTipo     = '';
        $this->modalSelector  = true;
    }

    public function cerrarSelector(): void
    {
        $this->modalSelector  = false;
        $this->buscarSelector = '';
        $this->filtroTipo     = '';
    }

public function agregarItemConCantidad(int $recursoId, float $cantidad = 1): void
{
    $recurso = Recurso::findOrFail($recursoId);

    foreach ($this->items as &$item) {
        if ($item['recurso_id'] === $recursoId) {
            $item['cantidad'] = $cantidad; // actualiza si ya existe
            return;
        }
    }

    $this->items[] = [
        'recurso_id' => $recurso->id,
        'nombre'     => $recurso->nombre,
        'unidad'     => $recurso->unidad,
        'precio_usd' => $recurso->precio_usd,
        'cantidad'   => $cantidad,
    ];
}

    public function quitarItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function guardar(): void
{
    $this->validate();

    $composicion = Recurso::create([
        'nombre'     => $this->nombre,
        'tipo'       => 'composition',
        'unidad'     => $this->unidad,
        'precio_usd' => 0,
    ]);

    foreach ($this->items as $item) {
        $composicion->items()->create([
            'nombre'     => $item['nombre'],
            'cantidad'   => (float) $item['cantidad'],
            'recurso_id' => $item['recurso_id'],
        ]);
    }

    // Recalcular precio total
    $composicion->load('items.recursoBase');
    $total = $composicion->items->sum(fn($i) => $i->precio_total);
    $composicion->update(['precio_usd' => $total]);

    $this->reset();
    $this->dispatch('composicionCreada');
    $this->dispatch('cerrarModalComposicion');
}

    public function cancelar(): void
    {
        $this->dispatch('cerrarModalComposicion');
    }

   public function getRecursosFiltradosProperty()
{
    return Recurso::when($this->buscarSelector, fn($q) =>
            $q->where('nombre', 'like', '%' . $this->buscarSelector . '%'))
        ->when($this->filtroTipo, fn($q) =>
            $q->where('tipo', $this->filtroTipo))
        ->whereIn('tipo', ['material', 'labor', 'equipment', 'composition']) // ← agregás composition
        ->orderBy('nombre')
        ->get();
}
    public function render()
    {
        return view('livewire.recurso.crear-composicion', [
            'recursosFiltrados' => $this->recursosFiltrados,
        ]);
    }
}