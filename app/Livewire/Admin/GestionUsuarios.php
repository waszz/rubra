<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GestionUsuarios extends Component
{
    use WithPagination;

    // Filtros de listado
    public string $search      = '';
    public string $filterPlan  = '';
    public string $filterRol   = '';
    public string $filterEstado = 'activos';

    // Modal de edición
    public bool $modalEditar   = false;
    public ?int $editId        = null;
    public string $editNombre  = '';
    public string $editEmail   = '';
    public string $editPlan    = '';
    public string $editRol     = '';
    public string $editPassword = '';
    public int $extensionDias  = 30;

    // Modal proyectos
    public bool $modalProyectos = false;
    public array $proyectosUsuario = [];
    public string $nombreUsuarioProyectos = '';

    // Mensajes flash internos
    public string $mensaje = '';
    public string $mensajeTipo = 'success';

    // Reset paginación al cambiar filtros
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedFilterPlan(): void  { $this->resetPage(); }
    public function updatedFilterRol(): void   { $this->resetPage(); }
    public function updatedFilterEstado(): void { $this->resetPage(); }

    // ─── Abrir / cerrar modales ───────────────────────────────────────────────

    public function abrirEditar(int $id): void
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $this->editId       = $usuario->id;
        $this->editNombre   = $usuario->name;
        $this->editEmail    = $usuario->email;
        $this->editPlan     = $usuario->plan;
        $this->editRol      = $usuario->role;
        $this->editPassword = '';
        $this->extensionDias = 30;
        $this->mensaje      = '';
        $this->modalEditar  = true;
    }

    public function cerrarEditar(): void
    {
        $this->modalEditar = false;
        $this->editId      = null;
    }

    public function abrirProyectos(int $id): void
    {
        $usuario = User::withTrashed()->with('proyectos')->findOrFail($id);
        $this->nombreUsuarioProyectos = $usuario->name;
        $this->proyectosUsuario = $usuario->proyectos
            ->map(fn($p) => [
                'id'      => $p->id,
                'nombre'  => $p->nombre_proyecto,
                'estado'  => $p->estado_obra ?? 'sin estado',
                'cliente' => $p->cliente ?? '-',
                'fecha'   => $p->fecha_inicio ?? '-',
            ])->toArray();
        $this->modalProyectos = true;
    }

    public function cerrarProyectos(): void
    {
        $this->modalProyectos = false;
        $this->proyectosUsuario = [];
    }

    // ─── Guardar edición ─────────────────────────────────────────────────────

    public function guardarUsuario(): void
    {
        $this->validate([
            'editNombre' => 'required|string|max:255',
            'editEmail'  => 'required|email|max:255',
            'editPlan'   => 'required|in:gratis,basico,profesional,enterprise',
            'editRol'    => 'required|in:user,admin,god,supervisor',
        ]);

        $usuario = User::withTrashed()->findOrFail($this->editId);
        $usuario->name  = $this->editNombre;
        $usuario->email = $this->editEmail;
        $usuario->plan  = $this->editPlan;
        $usuario->role  = $this->editRol;
        $usuario->save();

        $this->flash('Usuario actualizado correctamente.');
    }

    public function cambiarContrasena(): void
    {
        $this->validate(['editPassword' => 'required|string|min:8']);

        $usuario = User::withTrashed()->findOrFail($this->editId);
        $usuario->password = Hash::make($this->editPassword);
        $usuario->save();

        $this->editPassword = '';
        $this->flash('Contraseña cambiada correctamente.');
    }

    // ─── Extender trial ───────────────────────────────────────────────────────

    public function extenderTrial(): void
    {
        $usuario = User::withTrashed()->findOrFail($this->editId);
        $base = $usuario->trial_ends_at && $usuario->trial_ends_at->isFuture()
            ? $usuario->trial_ends_at
            : now();
        $usuario->trial_ends_at = $base->addDays($this->extensionDias);
        $usuario->save();

        $this->flash("Trial extendido {$this->extensionDias} días.");
    }

    // ─── Forzar plan ─────────────────────────────────────────────────────────

    public function forzarPlan(): void
    {
        $usuario = User::withTrashed()->findOrFail($this->editId);
        $usuario->plan = $this->editPlan;
        // Si no es gratis, limpia expiración de plan para que sea indefinido
        if ($this->editPlan !== 'gratis') {
            $usuario->plan_expires_at = null;
        }
        $usuario->save();

        $this->flash("Plan forzado a {$this->editPlan} (sin pago).");
    }

    // ─── Eliminar / restaurar ─────────────────────────────────────────────────

    public function eliminarUsuario(int $id): void
    {
        $usuario = User::findOrFail($id);
        if ($usuario->isGod()) {
            $this->flash('No se puede eliminar un usuario God.', 'error');
            return;
        }
        $usuario->delete();
        $this->flash('Usuario eliminado (soft delete).');
    }

    public function restaurarUsuario(int $id): void
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->restore();
        $this->flash('Usuario restaurado correctamente.');
    }

    // ─── Impersonar ───────────────────────────────────────────────────────────

    public function impersonarUsuario(int $id)
    {
        $usuario = User::withTrashed()->findOrFail($id);

        session([
            'impersonating_from'   => Auth::id(),
            'impersonated_user_id' => $id,
        ]);

        Auth::login($usuario);

        return redirect()->route('dashboard');
    }

    // ─── Helper flash ─────────────────────────────────────────────────────────

    private function flash(string $msg, string $tipo = 'success'): void
    {
        $this->mensaje     = $msg;
        $this->mensajeTipo = $tipo;
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $query = User::withTrashed();

        // Búsqueda por nombre o email
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name',  'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filtros
        if ($this->filterPlan)  $query->where('plan',  $this->filterPlan);
        if ($this->filterRol)   $query->where('role',  $this->filterRol);

        if ($this->filterEstado === 'activos') {
            $query->whereNull('deleted_at');
        } elseif ($this->filterEstado === 'eliminados') {
            $query->whereNotNull('deleted_at');
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'activos'    => User::whereNull('deleted_at')->count(),
            'eliminados' => User::whereNotNull('deleted_at')->count(),
            'total'      => User::withTrashed()->count(),
            'god'        => User::where('role', 'god')->count(),
        ];

        return view('livewire.admin.gestion-usuarios', compact('usuarios', 'stats'))
            ->layout('layouts.app');
    }
}
