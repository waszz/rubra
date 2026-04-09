<?php


use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Admin\PanelExportController;
use App\Http\Controllers\EstadisticasExportController;
use App\Livewire\Proyecto\Bitacora;
use App\Livewire\Proyecto\ConfiguracionGeneral;
use App\Livewire\Proyecto\GestionUsuarios as ProyectoGestionUsuarios;
use App\Livewire\Proyecto\PresupuestoDetallado;
use App\Livewire\Admin\Panel;
use App\Livewire\Admin\GestionUsuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── HOME (Landing Page) ──────────────────────────────────────────────
Route::get('/', function () {
    return view('home.index');
})->name('home');

// ── PÁGINAS LEGALES ──────────────────────────────────────────────────
Route::get('/terminos-y-condiciones', function () {
    return view('legal.terminos');
})->name('legal.terminos');

Route::get('/privacidad', function () {
    return view('legal.privacidad');
})->name('legal.privacidad');

Route::get('/cookies', function () {
    return view('legal.cookies');
})->name('legal.cookies');

// ── WEBHOOK MERCADOPAGO (sin auth) ────────────────────────────────────
Route::post('/webhooks/mercadopago', [PagoController::class, 'webhookMercadopago'])
    ->name('webhooks.mercadopago');

// ── PANEL DE ADMINISTRACIÓN (solo para role: god) ──────────────────────
Route::middleware(['auth', 'god'])->group(function () {
    Route::get('/panel', Panel::class)
        ->name('panel');
    
    Route::get('/panel/usuarios', GestionUsuarios::class)
        ->name('admin.usuarios');

    Route::get('/panel/export/pdf', [PanelExportController::class, 'exportPdf'])
        ->name('panel.export.pdf');

    Route::get('/panel/export/excel', [PanelExportController::class, 'exportExcel'])
        ->name('panel.export.excel');
});

Route::middleware(['auth', 'trial.expirado'])->group(function () {

    // ── PLANES Y PAGOS ────────────────────────────────────────────────
    Route::get('/planes/{plan}/checkout', [PagoController::class, 'checkout'])->name('pago.checkout');
    Route::post('/planes/{plan}/mercadopago', [PagoController::class, 'mercadopago'])->name('pago.mercadopago');
    Route::post('/planes/{plan}/paypal', [PagoController::class, 'paypal'])->name('pago.paypal');
    Route::get('/planes/{plan}/success', [PagoController::class, 'success'])->name('pago.success');
    Route::get('/planes/{plan}/failure', [PagoController::class, 'failure'])->name('pago.failure');
    Route::get('/planes/{plan}/pending', [PagoController::class, 'pending'])->name('pago.pending');
    Route::get('/planes/{plan}/confirmar-manual', [PagoController::class, 'confirmarManual'])->name('pago.confirmar_manual');

    Route::get('/dashboard', \App\Livewire\Proyecto\MostrarProyectos::class)
        ->name('dashboard')
        ->middleware('role:proyectos');

    Route::get('/recursos', \App\Livewire\Recurso\MostrarRecursos::class)
        ->name('recursos.index')
        ->middleware('role:recursos');

    Route::get('/recursos/crear', \App\Livewire\Recurso\CrearRecurso::class)
        ->name('recursos.create')
        ->middleware('role:recursos');

    Route::get('/proyectos/{proyecto}/gantt', \App\Livewire\Proyecto\GanttProyecto::class)
        ->name('proyectos.gantt')
        ->middleware('role:mapa');

    Route::get('/proyectos/{proyecto}/diario', \App\Livewire\Proyecto\DiarioObra::class)
        ->name('proyectos.diario')
        ->middleware('role:reporte_diario');

    Route::get('/proyectos/{proyecto}/bitacora', Bitacora::class)
        ->name('proyectos.bitacora')
        ->middleware('role:bitacora');

    Route::get('/bitacora/{proyectoId?}', \App\Livewire\Proyecto\BitacoraGlobal::class)
        ->name('bitacora.global')
        ->middleware('role:bitacora');

    Route::get('/estadisticas/{proyectoId?}', \App\Livewire\Proyecto\EstadisticasProyecto::class)
        ->name('estadisticas')
        ->middleware('role:estadisticas');

    Route::get('/estadisticas/{proyectoId}/export/excel', [EstadisticasExportController::class, 'excel'])
        ->name('estadisticas.export.excel')
        ->middleware('role:estadisticas');

    Route::get('/estadisticas/{proyectoId}/export/pdf', [EstadisticasExportController::class, 'pdf'])
        ->name('estadisticas.export.pdf')
        ->middleware('role:estadisticas');

    Route::get('/mapa-proyectos', \App\Livewire\Proyecto\MapaProyectos::class)
        ->name('mapa.proyectos')
        ->middleware('role:proyectos');

    Route::get('/usuarios', ProyectoGestionUsuarios::class)
        ->name('usuarios')
        ->middleware('role:usuarios');

 Route::get('/invitacion/aceptar', function (Request $request) {
    $invitacion = \App\Models\Invitacion::where('token', $request->token)
        ->where('expires_at', '>', now())
        ->firstOrFail();

    $user = \App\Models\User::findOrFail($invitacion->user_id);

    // Agregar a los proyectos del invitador
    $proyectos = \App\Models\Proyecto::where('user_id', $invitacion->invited_by)->pluck('id');
    foreach ($proyectos as $pid) {
        if (!$user->proyectos()->where('proyecto_id', $pid)->exists()) {
            $user->proyectos()->attach($pid, ['rol' => $user->role]);
        }
    }

    // Actualizar invited_by solo si no tiene uno ya
    if (!$user->invited_by) {
        $user->update(['invited_by' => $invitacion->invited_by]);
    }

    // Borrar TODAS las invitaciones de ese email (aceptada + pendientes)
    \App\Models\Invitacion::where('email', $user->email)->delete();

    return redirect('/dashboard')->with('success', '¡Acceso aceptado correctamente!');
})->middleware('auth')->name('invitacion.aceptar');

Route::get('/invitacion/proyecto/{token}', function (Request $request, $token) {
    // Buscar invitación válida para este proyecto
    $invitacion = \App\Models\Invitacion::where('token', $token)
        ->where('expires_at', '>', now())
        ->whereNotNull('proyecto_id')
        ->firstOrFail();

    // Si está autenticado, ir directo a confirmar (usa el rol de la invitación)
    if (auth()->check()) {
        return redirect()->route('invitacion.proyecto.auto-confirmar', ['token' => $token]);
    }

    // Si no está autenticado, redirigir a login/registro
    return redirect()->route('register', ['proyecto_token' => $token]);
})->name('invitacion.proyecto');

Route::get('/invitacion/proyecto/{token}/auto-confirmar', function (Request $request, $token) {
    // Validar que la invitación exista y sea válida
    $invitacion = \App\Models\Invitacion::where('token', $token)
        ->where('expires_at', '>', now())
        ->whereNotNull('proyecto_id')
        ->firstOrFail();

    $user = auth()->user();
    $proyecto = $invitacion->proyecto;
    $rol = $invitacion->rol;

    // Agregar usuario al proyecto con el rol de la invitación
    if (!$user->proyectos()->where('proyecto_id', $proyecto->id)->exists()) {
        $user->proyectos()->attach($proyecto->id, ['rol' => $rol]);
    }

    // Actualizar invited_by si no tiene uno
    if (!$user->invited_by) {
        $user->update(['invited_by' => $invitacion->invited_by]);
    }

    // Borrar esta invitación (ya fue aceptada)
    $invitacion->delete();

    return redirect()->route('proyectos.presupuesto', $proyecto->id)
        ->with('success', '¡Te has unido al proyecto como ' . ucfirst($rol) . '!');
})->middleware('auth')->name('invitacion.proyecto.auto-confirmar');

Route::post('/invitacion/proyecto/{token}/confirmar', function (Request $request, $token) {
    // Buscar invitación válida
    $invitacion = \App\Models\Invitacion::where('token', $token)
        ->where('expires_at', '>', now())
        ->whereNotNull('proyecto_id')
        ->firstOrFail();

    $user = auth()->user();
    $proyecto = $invitacion->proyecto;
    $rol = $invitacion->rol; // El rol ya fue elegido al generar el link

    // Agregar usuario al proyecto con el rol de la invitación
    if (!$user->proyectos()->where('proyecto_id', $proyecto->id)->exists()) {
        $user->proyectos()->attach($proyecto->id, ['rol' => $rol]);
    }

    // Actualizar invited_by si no tiene uno
    if (!$user->invited_by) {
        $user->update(['invited_by' => $invitacion->invited_by]);
    }

    // Borrar esta invitación (ya fue aceptada)
    $invitacion->delete();

    return redirect()->route('proyectos.presupuesto', $proyecto->id)
        ->with('success', '¡Te has unido al proyecto como ' . ucfirst($rol) . '!');
})->middleware('auth')->name('invitacion.proyecto.confirmar');

Route::post('/proyectos/salir-multiple', function (Request $request) {
    $ids = $request->input('proyectos', []);
    
    if (!empty($ids)) {
        $user = auth()->user();
        
        // Quitar de los proyectos seleccionados
        $user->proyectos()->detach($ids);
        
        // Ver si quedó en algún proyecto del mismo invitador
        $invitador = $user->invited_by;
        
        if ($invitador) {
            $proyectosDelInvitador = \App\Models\Proyecto::where('user_id', $invitador)->pluck('id');
            
            $quedaEnAlguno = $user->proyectos()
                ->whereIn('proyecto_id', $proyectosDelInvitador)
                ->exists();
            
            // Si ya no está en ningún proyecto del invitador, limpiar invited_by
            if (!$quedaEnAlguno) {
                $user->update(['invited_by' => null]);
            }
        }
    }
    
    return redirect()->route('dashboard')->with('success', 'Saliste de los proyectos seleccionados.');
})->middleware('auth')->name('proyectos.salir.multiple');
});

// ── CONFIGURACIÓN (accesible aunque trial esté expirado) ──────────────
Route::middleware('auth')->group(function () {
    Route::get('/configuracion', ConfiguracionGeneral::class)
        ->name('configuracion')
        ->middleware('role:configuracion');
});


Route::get('/proyectos/{proyecto}/presupuesto', PresupuestoDetallado::class)
    ->name('proyectos.presupuesto')
    ->middleware('auth');

// Rutas legacy de /planillas deshabilitadas




Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


require __DIR__.'/auth.php';