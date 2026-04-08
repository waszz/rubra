<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitacionUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $rol;
    public $userExists;

    public function __construct($token, $rol, $userExists = false)
    {
        $this->token = $token;
        $this->rol = $rol;
        $this->userExists = $userExists;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitación a la plataforma',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}