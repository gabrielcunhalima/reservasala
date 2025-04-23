@component('mail::message')
# Reserva Aprovada!

Sua reserva para **{{ $reserva->sala->nomeSala }}** no dia 
**{{ $reserva->dataReserva->format('d/m/Y') }}** foi aprovada.

**Detalhes:**
- Período: {{ $reserva->turno->descricao }}
- Código: {{ $reserva->idReserva }}

@component('mail::button', ['url' => route('home')])
Acessar Sistema
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent