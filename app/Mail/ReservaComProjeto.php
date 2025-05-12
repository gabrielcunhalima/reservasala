<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservaComProjeto extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
        $this->reserva->load('datasReserva', 'sala');
    }

    public function build()
    {
        return $this->subject('Nova Reserva com Projeto - ' . config('app.name'))
                    ->markdown('emails.reserva-com-projeto');
    }
}