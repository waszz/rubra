<?php

namespace App\Models;

use App\Models\PermisoRol;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'invited_by',
        'plan',
        'trial_ends_at',
        'trial_used_at',
        'plan_expires_at',
        'plan_periodo',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'trial_ends_at'     => 'datetime',
            'trial_used_at'     => 'datetime',
            'plan_expires_at'   => 'datetime',
        ];
    }

  
  
    

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function isGod(): bool
    {
        return $this->role === 'god';
    }

    /**
     * Fecha efectiva de fin de trial.
     * Si trial_ends_at es null (usuarios registrados antes de la feature),
     * calcula 30 días desde created_at como fallback.
     */
    public function trialEndsAtEffective(): \Illuminate\Support\Carbon
    {
        return $this->trial_ends_at ?? $this->created_at->addMonth();
    }

    public function isOnTrial(): bool
    {
        // God users are never on trial
        if ($this->isGod()) {
            return false;
        }
        return $this->plan === 'gratis' && $this->trialEndsAtEffective()->isFuture();
    }

    public function trialExpired(): bool
    {
        // God users never have an expired trial
        if ($this->isGod()) {
            return false;
        }
        return $this->plan === 'gratis' && $this->trialEndsAtEffective()->isPast();
    }

    /**
     * El usuario puede realizar cambios (no está en modo solo-lectura).
     * Solo-lectura aplica cuando el plan es 'gratis' y el trial expiró.
     */
    public function canWrite(): bool
    {
        // God users can always write
        if ($this->isGod()) {
            return true;
        }
        return !$this->trialExpired();
    }

    public function trialDaysLeft(): int
    {
        // God users have unlimited trial days
        if ($this->isGod()) {
            return 999999;
        }
        if ($this->plan !== 'gratis') return 0;
        $ends = $this->trialEndsAtEffective();
        if ($ends->isPast()) return 0;
        return (int) now()->diffInDays($ends, false);
    }

    /**
     * Obtiene el límite de proyectos según el plan del usuario.
     */
    public function proyectosLimite(): int
    {
        // God users have unlimited projects
        if ($this->isGod()) {
            return 999999;
        }
        return match($this->plan) {
            'gratis'       => 3,
            'basico'       => 10,
            'profesional'  => 25,
            'enterprise'   => 100,
            default        => 1,
        };
    }

    public function planLabel(): string
    {
        return match($this->plan) {
            'gratis'       => 'Modo Prueba (Gratis)',
            'basico'       => 'Plan Básico',
            'profesional'  => 'Plan Profesional',
            'enterprise'   => 'Plan Enterprise',
            default        => ucfirst($this->plan),
        };
    }

    public function getIsSupervisorAttribute(): bool
    {
        return $this->role === 'supervisor';
    }

    public function puede($seccion): bool
    {
        // God (administrador supremo) puede hacer todo
        if ($this->isGod()) {
            return true;
        }

        $matriz = PermisoRol::matriz();
        return $matriz[$this->role][$seccion] ?? false;
    }

    /**
     * Verifica permisos para secciones compartidas usando el rol asignado en proyecto_user.
     * Para usuarios invitados (invited_by != null), usa el rol pivot en vez del global.
     */
    public function puedeCompartido($seccion): bool
    {
        if ($this->isGod()) {
            return true;
        }

        $matriz = PermisoRol::matriz();

        if ($this->invited_by) {
            $roles = \Illuminate\Support\Facades\DB::table('proyecto_user')
                ->where('user_id', $this->id)
                ->pluck('rol')
                ->unique();

            if ($roles->isNotEmpty()) {
                foreach ($roles as $rol) {
                    if ($matriz[$rol][$seccion] ?? false) {
                        return true;
                    }
                }
                return false;
            }
        }

        return $matriz[$this->role][$seccion] ?? false;
    }

    /**
     * Verifica si el usuario puede descargar la app (PWA).
     * Solo disponible en planes profesional y enterprise.
     */
    public function canDownloadApp(): bool
    {
        return in_array($this->plan, ['profesional', 'enterprise']);
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_user');
    }
}
