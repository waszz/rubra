<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'user_id',
        'nombre_proyecto',       // era 'nombre'
        'descripcion',
        'notas',                 // nuevo
        'cliente',
        'ubicacion',
        'ubicacion_lat',         // nuevo
        'ubicacion_lng',         // nuevo
        'mercado',               // nuevo
        'moneda_base',           // nuevo
        'horas_jornal',          // nuevo
        'metros_cuadrados',      // nuevo
        'impuestos',             // nuevo
        'beneficio',             // nuevo (era solo en el componente)
        'presupuesto_total',
        'ganancia_estimada',     // nuevo
        'fecha_inicio',          // nuevo
        'estado_obra',           // era 'estado'
        'estado_autorizacion',   // nuevo
        'plantilla_base',        // nuevo
        'carga_social',
    ];

    protected $casts = [
        'presupuesto_total'  => 'float',
        'ganancia_estimada'  => 'float',
        'metros_cuadrados'   => 'float',
        'impuestos'          => 'float',
        'beneficio'          => 'float',
        'horas_jornal'       => 'integer',
        'ubicacion_lat'      => 'float',
        'ubicacion_lng'      => 'float',
        'fecha_inicio'       => 'date',
        'carga_social'       => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diarioObras()
    {
        return $this->hasMany(DiarioObra::class);
    }

    public function proyectoRecursos()
    {
        return $this->hasMany(ProyectoRecurso::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'proyecto_user');
    }
}