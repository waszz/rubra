<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioHistorial extends Model
{
    protected $table = 'precio_historiales';

    protected $fillable = [
        'recurso_id',
        'precio_anterior',
        'precio_nuevo',
        'razon',
    ];

    protected $casts = [
        'precio_anterior' => 'float',
        'precio_nuevo' => 'float',
        'created_at' => 'datetime',
    ];

    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }
}
