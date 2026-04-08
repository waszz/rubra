<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GodMiddleware
{
    /**
     * Permite acceso solo a usuarios con role 'god'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isGod()) {
            abort(403, 'Acceso denegado. Solo administradores pueden acceder.');
        }

        return $next($request);
    }
}
