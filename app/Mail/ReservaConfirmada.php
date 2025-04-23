<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
        $this->reserva->load('datasReserva');
    }

    public function build()
    {
        return $this->subject('Confirmação de Reserva - ' . config('app.name'))
                    ->markdown('emails.reserva-confirmada');
    }
}