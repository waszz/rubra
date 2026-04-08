<?php

namespace App\Livewire\Concerns;

use App\Models\Proyecto;

trait AutorizaProyecto
{
    /**
     * Verifica que el usuario autenticado tenga acceso al proyecto.
     * Abort 403 si no es propietario ni miembro ni god/admin.
     */
    protected function autorizarAcceso(Proyecto $proyecto): void
    {
        $user = auth()->user();

        // God y admin siempre tienen acceso
        if ($user->isGod() || $user->role === 'admin') {
            return;
        }

        // Propietario directo del proyecto
        if ($proyecto->user_id === $user->id) {
            return;
        }

        // Usuario invitado con registro en la tabla pivot proyecto_user
        $esMiembro = $proyecto->usuarios()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($esMiembro, 403, 'No tenés acceso a este proyecto.');
    }
}
