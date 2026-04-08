<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComposicionItem extends Model
{
    protected $fillable = [
        'composicion_id',
        'recurso_id',
        'nombre',
        'cantidad',
    ];

    public function composicion()
    {
        return $this->belongsTo(Recurso::class, 'composicion_id');
    }

    public function recursoBase()
    {
        return $this->belongsTo(Recurso::class, 'recurso_id'); // ← por id, no por nombre
    }

    public function getPrecioTotalAttribute(): float
    {
        $recurso = $this->recursoBase;
        if (!$recurso) return 0;

        $precio = $recurso->precio_usd;

        if (in_array($recurso->tipo, ['labor', 'mano_obra'])) {
            $precio = $precio * (1 + ($recurso->social_charges_percentage ?? 72) / 100);
        }

        return $this->cantidad * $precio;
    }
}