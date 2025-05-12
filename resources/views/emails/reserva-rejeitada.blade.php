@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# Olá {{ $reserva->nome }},

Informamos que sua reserva em **{{ $reserva->sala->nomeSala }}** foi **rejeitada**. Seguem os detalhes:

Número da Reserva: **{{ $reserva->idReserva }}**<br>
Sala: **{{ $reserva->sala->nomeSala }}**
&nbsp;

## Dias solicitados:
@foreach ($reserva->datasReserva as $dataReserva)
**{{ \Carbon\Carbon::parse($dataReserva->data)->format('d/m/Y') }}**
({{ ($dataReserva->diaTodo || ($dataReserva->manha && $dataReserva->tarde)) ? 'Dia Todo' : ($dataReserva->manha ? 'Manhã' : '') . ($dataReserva->tarde ? 'Tarde' : '') }})<br>
@endforeach


@component('mail::panel')
**Status:** <span style="color:red;">Rejeitada</span>
@endcomponent


**Motivo da rejeição:**
{{ $reserva->justificativa }}

Caso ainda tenha interesse em reservar esta sala, você pode fazer uma nova solicitação escolhendo outras datas ou horários disponíveis.

@component('mail::button', ['url' => route('reserva.disponibilidade', $reserva->sala->idSala), 'color' => 'success'])
Fazer Nova Reserva
@endcomponent

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }} - Fundação de Amparo à Pesquisa e Extensão Universitária.
@endcomponent
@endslot
@endcomponent