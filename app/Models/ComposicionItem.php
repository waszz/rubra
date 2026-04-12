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

        // La carga social se muestra como referencia pero NO se suma al costo del APU.
        return $this->cantidad * ($recurso->precio_usd ?? 0);
    }
}