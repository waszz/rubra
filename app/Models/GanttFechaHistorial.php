<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GanttFechaHistorial extends Model
{
    protected $table = 'gantt_fecha_historiales';

    protected $fillable = [
        'proyecto_recurso_id',
        'user_id',
        'accion',
        'fecha_inicio_anterior',
        'fecha_fin_anterior',
        'fecha_inicio_nueva',
        'fecha_fin_nueva',
        'trabajadores_anterior',
        'trabajadores_nueva',
    ];

    protected $casts = [
        'fecha_inicio_anterior' => 'date',
        'fecha_fin_anterior'    => 'date',
        'fecha_inicio_nueva'    => 'date',
        'fecha_fin_nueva'       => 'date',
    ];

    public function proyectoRecurso(): BelongsTo
    {
        return $this->belongsTo(ProyectoRecurso::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
