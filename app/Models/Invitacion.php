<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitacion extends Model
{
    protected $table = 'invitaciones';

    protected $fillable = [
        'email',
        'rol',
        'token',
        'expires_at',
        'invited_by',
        'proyecto_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function invitadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function estaVigente(): bool
    {
        return $this->expires_at->isFuture();
    }
}