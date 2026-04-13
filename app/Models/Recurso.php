<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recurso extends Model
{
    use HasFactory;

    protected $table = 'recursos';

  protected $fillable = [
    'nombre',
    'codigo',
    'tipo',
    'unidad',
    'precio_usd',
    'moneda',
    'region',
    'vendedor',
    'precio_estimativo',
    'marca_modelo',
    'observaciones',
    'social_charges_percentage',
];

    protected $casts = [
        'precio_usd' => 'float',
    ];

    public function items()
{
    return $this->hasMany(ComposicionItem::class, 'composicion_id');
}
public function proyectos()
{
    return $this->belongsToMany(\App\Models\Proyecto::class, 'proyecto_recursos');
}

public function precioHistorial()
{
    return $this->hasMany(PrecioHistorial::class)->orderByDesc('created_at');
}

}