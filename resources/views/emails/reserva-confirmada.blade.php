@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# Olá {{ $reserva->nome }},

Sua reserva em **{{ $reserva->sala->nomeSala }}** foi recebida e está **em análise**. Seguem os detalhes:

Número da Reserva: **{{ $reserva->idReserva }}**<br>
Sala: **{{ $reserva->sala->nomeSala }}**
&nbsp;

## Dias solicitados:
@foreach ($reserva->datasReserva as $dataReserva)
**{{ \Carbon\Carbon::parse($dataReserva->data)->format('d/m/Y') }}**
({{ ($dataReserva->diaTodo || ($dataReserva->manha && $dataReserva->tarde)) ? 'Dia Todo' : ($dataReserva->manha ? 'Manhã' : '') . ($dataReserva->tarde ? 'Tarde' : '') }})<br>
@endforeach

@component('mail::panel')
**Status:** Aguardando Aprovação
@endcomponent

Sua solicitação será analisada em breve. Você receberá um e-mail informando a aprovação ou rejeição da sua reserva.

**Sobre cancelamento**

Para futuros cancelamentos de reserva, você precisará do seguinte código de segurança:

**<span style="color:red;">Código de Cancelamento:</span> {{ $reserva->hashCancelamento }}**

Guarde este código em local seguro. Ele será solicitado para confirmar o cancelamento de qualquer dia de reserva.

@component('mail::button', ['url' => route('home'), 'color' => 'success'])
Acessar Reserva de Salas
@endcomponent

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }} - Fundação de Amparo à Pesquisa e Extensão Universitária.
@endcomponent
@endslot
@endcomponent