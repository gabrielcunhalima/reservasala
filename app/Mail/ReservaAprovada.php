<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservaAprovada extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct($reserva)
    {
        $this->reserva = $reserva;
        
        // Se o reserva for um objeto DB e não um modelo eloquent
        if (!isset($this->reserva->datasReserva)) {
            // Buscar as datas da reserva
            $datasReserva = \Illuminate\Support\Facades\DB::table('DataReserva')
                ->where('idReserva', $this->reserva->idReserva)
                ->get();
                
            // Buscar detalhes da sala
            $sala = \Illuminate\Support\Facades\DB::table('Salas')
                ->where('idSala', $this->reserva->idSala)
                ->first();
                
            // Adicionar propriedades ao objeto
            $this->reserva->datasReserva = $datasReserva;
            $this->reserva->sala = $sala;
        }
    }

    public function build()
    {
        return $this->subject('Reserva Aprovada - ' . config('app.name'))
                    ->markdown('emails.reserva-aprovada');
    }
}