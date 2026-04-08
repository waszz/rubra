<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionGeneral extends Model
{
    protected $table = 'configuracion_general';

   protected $fillable = [
    'user_id',
    'nombre_empresa',
    'rut',
    'logo_url',
    'pagina_web',
    'redes_sociales',
    'telefonos',
    'correo',
    'latitud',
    'longitud',
];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
    ];

    /**
     * Siempre hay un único registro — singleton pattern.
     */
   public static function instancia(): static
{
    return static::firstOrCreate(['user_id' => auth()->id()]);
}
}