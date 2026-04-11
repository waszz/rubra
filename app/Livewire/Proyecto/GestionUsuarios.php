<?php

namespace App\Livewire\Proyecto;

use App\Models\Invitacion;
use App\Models\PermisoRol;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class GestionUsuarios extends Component
{
    use WithPagination;

    // Tabs
    public string $tab = 'usuarios';
    

    // Búsqueda
    public string $busqueda = '';

    // Modal invitación
    public bool   $modalInvitar  = false;
    public string $invitar_email = '';
    public string $invitar_rol   = 'presupuestador';
    public $usuarioAEliminar = null;
public $mostrarModalEliminar = false;

    // Permisos
    public array $matriz     = [];
    public array $secciones  = [
        'proyectos'      => 'Proyectos',
        'recursos'       => 'Recursos',
        'usuarios'       => 'Usuarios',
        'configuracion'  => 'Configuración',
        'estadisticas'   => 'Estadísticas',
        'mapa'           => 'Mapa',
        'bitacora'       => 'Bitácora',
        'reporte_diario' => 'Reporte Diario',
        'computos'       => 'Cómputos',
    ];

    public array $roles = [
        'supervisor'      => 'Supervisor',
        'presupuestador'  => 'Presupuestador',
        'jefe_obra'       => 'Jefe de Obra',
        'administrativo'  => 'Administrativo',
    ];

    // Feedback
    public string $successMsg = '';
    public string $errorMsg   = '';

    protected array $rules = [
        'invitar_email' => 'required|email',
        'invitar_rol'   => 'required|in:supervisor,presupuestador,jefe_obra,administrativo',
];
    protected array $messages = [
        'invitar_email.required' => 'El correo es obligatorio.',
        'invitar_email.email'    => 'Ingresá un correo válido.',
        'invitar_email.unique'   => 'Este correo ya tiene una cuenta.',
        'invitar_rol.required'   => 'Seleccioná un rol.',
    ];

    public function mount(): void
    {
        $this->cargarMatriz();
    }

    public function cargarMatriz(): void
    {
        $bd = PermisoRol::matriz();

        foreach ($this->roles as $rolKey => $_) {
            foreach ($this->secciones as $secKey => $_) {
                $this->matriz[$rolKey][$secKey] = $bd[$rolKey][$secKey] ?? false;
            }
        }
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    // ── INVITACIÓN ────────────────────────────────────────────────────────
    public function abrirModalInvitar(): void
    {
        $this->reset('invitar_email', 'invitar_rol', 'errorMsg', 'successMsg');
        $this->invitar_rol  = 'presupuestador';
        $this->modalInvitar = true;
    }

    public function cerrarModal(): void
    {
        $this->modalInvitar = false;
        $this->resetValidation();
    }

public function invitar(): void
{
    $this->validate();

    // Límites por rol según plan
    $roleLimits = [
        'gratis' => ['supervisor' => 1, 'presupuestador' => 0, 'jefe_obra' => 0, 'administrativo' => 0],
        'basico' => ['supervisor' => 1, 'presupuestador' => 0, 'jefe_obra' => 2, 'administrativo' => 0],
        'profesional' => ['supervisor' => 1, 'presupuestador' => 3, 'jefe_obra' => 6, 'administrativo' => 0],
        'enterprise' => ['supervisor' => 2, 'presupuestador' => 6, 'jefe_obra' => 12, 'administrativo' => 5],
    ];

    $owner = auth()->user();
    $plan = $owner->plan ?? 'gratis';
    $planLimits = $roleLimits[$plan] ?? $roleLimits['gratis'];

    $role = $this->invitar_rol;
    $allowedForRole = $planLimits[$role] ?? 0;

    // Contar usuarios ya asignados al rol en los proyectos del invitador (distinto por usuario)
    $proyectosDelInvitador = Proyecto::where('user_id', $owner->id)->pluck('id');
    $acceptedCount = DB::table('proyecto_user')
        ->whereIn('proyecto_id', $proyectosDelInvitador)
        ->where('rol', $role)
        ->distinct()
        ->count('user_id');

    // Contar invitaciones vigentes para ese rol
    $pendingCount = Invitacion::where('invited_by', $owner->id)
        ->where('rol', $role)
        ->where('expires_at', '>', now())
        ->count();

    $totalForRole = $acceptedCount + $pendingCount;

    if ($totalForRole >= $allowedForRole) {
        $label = $this->roles[$role] ?? $role;
        $this->errorMsg = "Tu plan permite máximo {$allowedForRole} usuario(s) con rol {$label}. Ya has alcanzado el límite.";
        return;
    }

    // Verificar usuario existente por email
    $user = User::where('email', $this->invitar_email)->first();

    // Si ya existe y ya está en mis proyectos → error
    if ($user) {
        $yaEstaEnMisProyectos = $user->proyectos()
            ->whereIn('proyecto_id', $proyectosDelInvitador)
            ->exists();

        if ($yaEstaEnMisProyectos) {
            $this->errorMsg = 'Este usuario ya tiene acceso a tus proyectos.';
            return;
        }
    }

    // Verificar invitación vigente solo si NO existe como usuario
    if (!$user) {
        $existente = Invitacion::where('email', $this->invitar_email)
            ->where('expires_at', '>', now())
            ->first();

        if ($existente) {
            $this->errorMsg = 'Ya existe una invitación vigente para ese correo.';
            return;
        }
    }

    $token = Str::random(48);

    // Borrar invitaciones anteriores para ese email antes de crear una nueva
    Invitacion::where('email', $this->invitar_email)->delete();

    Invitacion::create([
        'email'      => $this->invitar_email,
        'rol'        => $this->invitar_rol,
        'token'      => $token,
        'expires_at' => now()->addDays(7),
        'invited_by' => $owner->id,
    ]);

    if ($user) {
        foreach ($proyectosDelInvitador as $proyectoId) {
            if (!$user->proyectos()->where('proyecto_id', $proyectoId)->exists()) {
                $user->proyectos()->attach($proyectoId, ['rol' => $this->invitar_rol]);
            }
        }
        $user->update(['invited_by' => $owner->id]);
    }

    Mail::to($this->invitar_email)->send(
        new \App\Mail\InvitacionUsuario($token, $this->invitar_rol, (bool) $user)
    );

    $this->modalInvitar = false;
    $this->successMsg   = $user
        ? "Acceso otorgado y notificación enviada a {$this->invitar_email}."
        : "Invitación de registro enviada a {$this->invitar_email}.";

    $this->js("setTimeout(() => \$wire.set('successMsg', ''), 3000)");
}
    // ── PERMISOS ──────────────────────────────────────────────────────────
    public function togglePermiso(string $rol, string $seccion): void
    {
        $actual = $this->matriz[$rol][$seccion] ?? false;
        $nuevo  = !$actual;

        $this->matriz[$rol][$seccion] = $nuevo;
        PermisoRol::setPermiso($rol, $seccion, $nuevo);
    }

public function confirmarEliminarUsuario($userId)
{
    $this->usuarioAEliminar = $userId;
    $this->mostrarModalEliminar = true;
}

public function cerrarModalEliminar()
{
    $this->usuarioAEliminar = null;
    $this->mostrarModalEliminar = false;
}

public function eliminarUsuarioConfirmado()
{
    if (!$this->usuarioAEliminar) return;

    $user = User::find($this->usuarioAEliminar);

    if (!$user) {
        $this->usuarioAEliminar = null;
        $this->mostrarModalEliminar = false;
        return;
    }

    // Quitar de todos los proyectos del invitador
    $proyectosDelInvitador = Proyecto::where('user_id', auth()->id())->pluck('id');
    $user->proyectos()->detach($proyectosDelInvitador);

    // Limpiar invited_by para que desaparezca de la lista
    if ($user->invited_by == auth()->id()) {
        $user->update(['invited_by' => null]);
    }

    $this->usuarioAEliminar = null;
    $this->mostrarModalEliminar = false;

    $this->successMsg = 'Usuario eliminado correctamente.';
    $this->js("setTimeout(() => \$wire.set('successMsg', ''), 3000)");
}
    // ── CAMBIAR ROL ───────────────────────────────────────────────────────
  public function cambiarRol(int $userId, string $nuevoRol): void
{
    if ($userId === auth()->id()) {
        $this->errorMsg = 'No podés cambiar tu propio rol.';
        return;
    }

    // Actualizar rol en la pivot para todos los proyectos del invitador
    $proyectosDelInvitador = Proyecto::where('user_id', auth()->id())->pluck('id');

    DB::table('proyecto_user')
        ->where('user_id', $userId)
        ->whereIn('proyecto_id', $proyectosDelInvitador)
        ->update(['rol' => $nuevoRol]);

    $this->successMsg = 'Rol actualizado correctamente.';
    $this->js("setTimeout(() => \$wire.set('successMsg', ''), 3000)");
}

    // ── RENDER (MODIFICADO) ────────────────────────────────────────────
    public function render()
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Límites por rol según plan (configurable aquí)
        $roleLimits = [
            'gratis' => ['supervisor' => 1, 'presupuestador' => 0, 'jefe_obra' => 0, 'administrativo' => 0],
            'basico' => ['supervisor' => 1, 'presupuestador' => 0, 'jefe_obra' => 2, 'administrativo' => 0],
            'profesional' => ['supervisor' => 1, 'presupuestador' => 3, 'jefe_obra' => 6, 'administrativo' => 0],
            'enterprise' => ['supervisor' => 2, 'presupuestador' => 6, 'jefe_obra' => 12, 'administrativo' => 5],
        ];

        $planLimits = $roleLimits[$user->plan] ?? $roleLimits['gratis'];

        // Contar usuarios por rol (aceptados en pivot) y pendientes (invitaciones vigentes)
        $proyectosDelInvitador = Proyecto::where('user_id', $userId)->pluck('id');
        $roleCounts = [];
        foreach (array_keys($this->roles) as $rolKey) {
            $accepted = DB::table('proyecto_user')
                ->whereIn('proyecto_id', $proyectosDelInvitador)
                ->where('rol', $rolKey)
                ->distinct()
                ->count('user_id');

            $pending = Invitacion::where('invited_by', $userId)
                ->where('rol', $rolKey)
                ->where('expires_at', '>', now())
                ->count();

            $roleCounts[$rolKey] = $accepted + $pending;
        }

        $usuarios = User::query()
            ->where(function ($q) use ($userId) {
                $q->where('id', $userId)
                  ->orWhere('invited_by', $userId);
            })
            ->when($this->busqueda, fn($q) =>
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->busqueda}%")
                       ->orWhere('email', 'like', "%{$this->busqueda}%");
                })
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.proyecto.gestion-usuarios', [
            'usuarios'              => $usuarios,
            'totalActivos'          => $usuarios->total(),
            'planLimits'            => $planLimits,
            'roleCounts'            => $roleCounts,
        ])->layout('layouts.app');
    }
}