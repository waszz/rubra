<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioObra extends Model
{
    protected $table = 'diario_obras';

    protected $fillable = [
        'proyecto_id',
        'proyecto_recurso_id',
        'user_id',
        'fecha',
        'avance_fisico',
        'cantidad_hoy',
        'mano_de_obra',
        'horas_hoy',
        'costo_hoy',
        'notas',
        'foto_path',
    ];

    protected $casts = [
        'fecha'         => 'date',
        'avance_fisico' => 'float',
        'cantidad_hoy'  => 'float',
        'mano_de_obra'  => 'integer',
        'horas_hoy'     => 'float',
        'costo_hoy'     => 'float',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // RELACIÓN CORREGIDA
    public function recurso()
    {
        return $this->belongsTo(ProyectoRecurso::class, 'proyecto_recurso_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}