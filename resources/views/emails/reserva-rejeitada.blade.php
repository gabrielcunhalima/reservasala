@component('mail::message')
# Reserva Rejeitada

Sua reserva para **{{ $reserva->sala->nomeSala }}** no dia 
**{{ $reserva->dataReserva->format('d/m/Y') }}** foi rejeitada.

**Motivo:**  
{{ $reserva->justificativa }}

@component('mail::button', ['url' => route('reserva.disponibilidade', $reserva->sala->idSala)])
Nova Tentativa
@endcomponent

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent