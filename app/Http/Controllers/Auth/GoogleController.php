<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtiene la información del usuario de Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // IMPORTANTE: Buscar si existe una cuenta eliminada (soft delete) con este email y restaurarla
            $deletedUser = User::withTrashed()
                ->where('email', $googleUser->email)
                ->whereNotNull('deleted_at')
                ->first();

            if ($deletedUser) {
                // Si existe cuenta eliminada, restaurarla
                $deletedUser->restore();
                $deletedUser->update([
                    'google_id' => $googleUser->id,
                    'email_verified_at' => $deletedUser->email_verified_at ?? now(), // ✅ Asegurar que esté verificado
                ]);
                Auth::login($deletedUser);
                return redirect()->intended('dashboard');
            }

            // Buscar si el usuario ya existe (no eliminado)
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Si existe pero no tenía google_id, lo actualizamos
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Auth::login($user);
            } else {
                // 🔐 Verificar si este email ya usó trial alguna vez (incluso en cuenta borrada)
                $usedTrialBefore = User::withTrashed()
                    ->where('email', $googleUser->email)
                    ->whereNotNull('trial_used_at')
                    ->exists();

                // Preparar datos del usuario
                $newUserData = [
                    'name'              => $googleUser->name,
                    'email'             => $googleUser->email,
                    'google_id'         => $googleUser->id,
                    'password'          => bcrypt(Str::random(40)),
                    'role'              => 'supervisor',
                    'email_verified_at' => now(),
                ];

                // Si NO usó trial antes: asignar período de prueba
                if (!$usedTrialBefore) {
                    $newUserData['trial_ends_at'] = now()->addMonth();
                    $newUserData['trial_used_at'] = now(); // Marca que está usando su trial AHORA
                }
                // Si SÍ usó trial: NO asignar trial_ends_at ni trial_used_at

                $newUser = User::create($newUserData);
                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google');
        }
    }
}