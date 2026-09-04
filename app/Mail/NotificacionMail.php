<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $titulo;
    public string $mensaje;

    public function __construct(string $titulo, string $mensaje)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->subject($this->titulo)
                    ->view('emails.notificacion')
                    ->with(['titulo' => $this->titulo, 'mensaje' => $this->mensaje]);
    }
}

