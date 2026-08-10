<?php

namespace App\Mail;

use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaGeradoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Alerta $alerta)
    {
    }

    public function build()
    {
        return $this->subject('AgroTwin — Novo alerta: ' . $this->alerta->sensor->ds_nome)
            ->view('emails.alerta-gerado');
    }
}
