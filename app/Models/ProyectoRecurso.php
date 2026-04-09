<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoRecurso extends Model
{
    protected $table = 'proyecto_recursos';

  protected $fillable = [
    'proyecto_id',
    'parent_id',      
    'recurso_id',
    'nombre',
    'unidad',
    'cantidad',
    'precio_usd',
    'costo_real',
    'categoria',
    'orden',
    'fecha_inicio', 
    'fecha_fin',    
];

protected $casts = [
    'fecha_inicio' => 'date',
    'fecha_fin'    => 'date',
    'costo_real'   => 'float',
];


    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function hijos()
{
    return $this->hasMany(ProyectoRecurso::class, 'parent_id');
}

public function padre()
{
    return $this->belongsTo(ProyectoRecurso::class, 'parent_id');
}


}