<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrialExpiradoMiddleware
{
    /**
     * Si el trial expiró, bloquea cualquier acción de escritura
     * (POST, PUT, PATCH, DELETE y requests Livewire que no sean GET).
     * Las peticiones GET/HEAD pasan siempre para permitir solo-lectura.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // God users no tienen restricciones de trial
        if ($user && $user->isGod()) {
            return $next($request);
        }

        if ($user && $user->trialExpired()) {
            $method = $request->method();

            // Permitir GET y HEAD (solo lectura)
            if (in_array($method, ['GET', 'HEAD'])) {
                return $next($request);
            }

            // Permitir rutas de pago y logout aunque sean POST
            $allowedRoutes = [
                'pago.checkout',
                'pago.mercadopago',
                'pago.paypal',
                'pago.success',
                'pago.failure',
                'pago.pending',
                'pago.confirmar_manual',
                'logout',
            ];

            if ($request->routeIs(...$allowedRoutes)) {
                return $next($request);
            }

            // Livewire: permitir solo si el componente es ConfiguracionGeneral.
            // Se lee el nombre del componente del snapshot (JSON firmado por Livewire)
            // para evitar que un cliente manipule la cabecera Referer.
            if ($request->is('livewire/update') || $request->is('livewire/upload-file')) {
                if ($this->livewireRequestIsConfiguracion($request)) {
                    return $next($request);
                }

                // Cualquier otro componente Livewire: mostrar vista de trial expirado
                return response(view('errors.trial-expirado'), 403);
            }

            // Cualquier otra escritura: mostrar vista de trial expirado
            return response(view('errors.trial-expirado'), 403);
        }

        return $next($request);
    }

    /**
     * Inspecciona el body del request Livewire para determinar si todos los
     * componentes involucrados son ConfiguracionGeneral.
     * No se usa el header Referer porque es controlable por el cliente.
     */
    private function livewireRequestIsConfiguracion(Request $request): bool
    {
        $components = $request->input('components', []);

        if (empty($components)) {
            return false;
        }

        foreach ($components as $component) {
            $snapshot = json_decode($component['snapshot'] ?? '{}', true);
            $name     = $snapshot['memo']['name'] ?? '';

            // Livewire convierte App\Livewire\Proyecto\ConfiguracionGeneral
            // al nombre kebab: 'proyecto.configuracion-general'
            if ($name !== 'proyecto.configuracion-general') {
                return false;
            }
        }

        return true;
    }
}
