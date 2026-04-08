<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
    
        $user = Auth::user();
    
        // 🔒 Verifica si el correo está verificado
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
    
       
        // Redirección según rol
if ($user->is_admin || $user->isSupervisor) {
    return redirect()->route('dashboard'); // acceso a "Administración"
}
    
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Si estás impersonando a otro usuario, vuelve a la cuenta original
        if (session('impersonating_from')) {
            $originalUserId = session('impersonating_from');
            session()->forget(['impersonating_from', 'impersonated_user_id']);
            
            $originalUser = \App\Models\User::find($originalUserId);
            if ($originalUser) {
                Auth::login($originalUser);
                return redirect()->route('panel');
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
