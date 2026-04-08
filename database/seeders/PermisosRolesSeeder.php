<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermisoRol;

class PermisosRolesSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'supervisor' => [
                'proyectos' => true,
                'recursos' => true,
                'usuarios' => true,
                'configuracion' => true,
                'estadisticas' => true,
                'mapa' => true,
                'bitacora' => true,
                'reporte_diario' => true,
                'computos' => true,
            ],
            'presupuestador' => [
                'proyectos' => true,
                'recursos' => true,
                'usuarios' => false,
                'configuracion' => false,
                'estadisticas' => false,
                'mapa' => false,
                'bitacora' => false,
                'reporte_diario' => false,
                'computos' => false,
            ],
            'jefe_obra' => [
                'proyectos' => true,
                'recursos' => false,
                'usuarios' => false,
                'configuracion' => false,
                'estadisticas' => true,
                'mapa' => true,
                'bitacora' => true,
                'reporte_diario' => true,
                'computos' => false,
            ],
        ];

        foreach ($permisos as $rol => $secciones) {
            foreach ($secciones as $seccion => $activo) {
                PermisoRol::updateOrCreate(
                    [
                        'rol' => $rol,
                        'seccion' => $seccion
                    ],
                    [
                        'activo' => $activo
                    ]
                );
            }
        }
    }
}