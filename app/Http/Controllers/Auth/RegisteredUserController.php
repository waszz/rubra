<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitacion;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $proyectoToken = $request->query('proyecto_token');
        
        // Validar que el token sea válido si se proporciona
        if ($proyectoToken) {
            $invitacion = Invitacion::where('token', $proyectoToken)
                ->where('expires_at', '>', now())
                ->whereNotNull('proyecto_id')
                ->first();
                
            if (!$invitacion) {
                return back()->with('error', 'El enlace de invitación no es válido o ha expirado.');
            }
        }
        
        return view('auth.register', ['proyectoToken' => $proyectoToken ?? null]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'token' => ['nullable', 'string'],
            'proyecto_token' => ['nullable', 'string'],
            'acepta_terminos' => ['accepted'],
        ]);

        // Buscar si existe una cuenta eliminada (soft delete) con este email
        $deletedUser = User::withTrashed()
            ->where('email', $request->email)
            ->whereNotNull('deleted_at')
            ->first();

        if ($deletedUser) {
            $deletedUser->restore();
            $deletedUser->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'trial_used_at' => $deletedUser->trial_used_at,
                'email_verified_at' => $deletedUser->email_verified_at ?? now(),
            ]);
            
            Auth::login($deletedUser);
            return $this->getRedirectPath($request);
        }

        // Verificar que el email no exista en cuentas NO eliminadas
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->withErrors(['email' => 'El email ya está registrado.']);
        }

        $role = 'supervisor';
        $invitedBy = null;

        // Si viene con token de invitación global
        if ($request->filled('token')) {
            $inv = Invitacion::where('token', $request->token)
                ->where('expires_at', '>', now())
                ->first();

            if ($inv) {
                $role = $inv->rol;
                $invitedBy = $inv->invited_by;
            }
        }

        // Verificar si este email ya usó trial alguna vez
        $usedTrialBefore = User::withTrashed()
            ->where('email', $request->email)
            ->whereNotNull('trial_used_at')
            ->exists();

        $userData = [
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $role,
            'invited_by'    => $invitedBy,
            'plan'          => 'gratis',
            'email_verified_at' => now(),
        ];

        if (!$usedTrialBefore) {
            $userData['trial_ends_at'] = now()->addMonth();
            $userData['trial_used_at'] = now();
        }

        $user = User::create($userData);

        if (isset($inv)) {
            $inv->delete();
        }

        Auth::login($user);

        return $this->getRedirectPath($request);
    }

    /**
     * Determina a dónde redirigir después del registro
     */
    protected function getRedirectPath(Request $request): RedirectResponse
    {
        if ($request->filled('proyecto_token')) {
            // Redirigir a una ruta GET que confirme automáticamente la invitación
            return redirect()->route('invitacion.proyecto.auto-confirmar', ['token' => $request->proyecto_token]);
        }
        
        return redirect(route('dashboard'));
    }
}
