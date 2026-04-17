<?php

namespace App\Http\Middleware;

use App\Models\PermisoRol;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{

public function handle(Request $request, Closure $next, $seccion): Response
{
    if (!auth()->check()) {
        abort(403, 'No autenticado.');
    }

    $user = auth()->user();

    // Admin y God siempre pasan
    if ($user->role === 'admin' || $user->role === 'god') {
        return $next($request);
    }

    // Si la ruta tiene un proyecto, chequear rol en la pivot
    $proyecto = $request->route('proyecto') ?? $request->route('proyectoId');

    if ($proyecto) {
        $rolEnProyecto = DB::table('proyecto_user')
            ->where('user_id', $user->id)
            ->where('proyecto_id', $proyecto instanceof \App\Models\Proyecto ? $proyecto->id : $proyecto)
            ->value('rol');

        // Si tiene rol en la pivot, usar ese en vez del global
        $rolEfectivo = $rolEnProyecto ?? $user->role;
    } else {
        // Secciones de contenido compartido: invitados usan su rol pivot
        $seccionesCompartidas = ['recursos_compartidos'];
        if ($user->invited_by && in_array($seccion, $seccionesCompartidas)) {
            $roles = DB::table('proyecto_user')
                ->where('user_id', $user->id)
                ->pluck('rol')
                ->unique();

            if ($roles->isNotEmpty()) {
                $matriz = PermisoRol::matriz();
                foreach ($roles as $rol) {
                    if ($matriz[$rol][$seccion] ?? false) {
                        return $next($request);
                    }
                }
                abort(403, 'No tienes permiso para esta sección.');
            }
        }
        $rolEfectivo = $user->role;
    }

    $matriz = PermisoRol::matriz();

    if (($matriz[$rolEfectivo][$seccion] ?? false)) {
        return $next($request);
    }

    abort(403, 'No tienes permiso para esta sección.');
}
}
