<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
    protected $table = 'permisos_roles';

    protected $fillable = [
        'rol',
        'seccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Devuelve la matriz completa como array indexado [rol][seccion] => bool
     */
    public static function matriz(): array
    {
        $registros = static::all();
        $matriz = [];
        foreach ($registros as $r) {
            $matriz[$r->rol][$r->seccion] = $r->activo;
        }
        return $matriz;
    }

    /**
     * Guarda o actualiza un permiso individual.
     */
    public static function setPermiso(string $rol, string $seccion, bool $activo): void
    {
        static::updateOrCreate(
            ['rol' => $rol, 'seccion' => $seccion],
            ['activo' => $activo]
        );
    }
}