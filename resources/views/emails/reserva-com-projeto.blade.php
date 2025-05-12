@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# Nova Reserva com Projeto

Uma nova reserva com projeto foi registrada no sistema:

**Número da Reserva:** {{ $reserva->idReserva }}<br>
**Sala:** {{ $reserva->sala->nomeSala }}<br>
**Solicitante:** {{ $reserva->nome }}<br>
**Email:** {{ $reserva->email }}<br>
**Telefone:** {{ $reserva->telefone }}<br>
**CPF:** {{ $reserva->cpf }}<br>
**Valor Total:** R$ {{ number_format($reserva->valor, 2, ',', '.') }}<br>
**Motivo da Reserva:** {{ $reserva->motivoReserva }}

## Informações do Projeto
**Código do Projeto:** {{ $reserva->codProjeto }}

## Dias reservados:
@foreach ($reserva->datasReserva as $dataReserva)
**{{ \Carbon\Carbon::parse($dataReserva->data)->format('d/m/Y') }}**
({{ ($dataReserva->diaTodo || ($dataReserva->manha && $dataReserva->tarde)) ? 'Dia Todo' : ($dataReserva->manha ? 'Manhã' : '') . ($dataReserva->tarde ? 'Tarde' : '') }})<br>
@endforeach

@component('mail::button', ['url' => route('admin.reservas.visualizar', $reserva->idReserva), 'color' => 'primary'])
Visualizar Reserva
@endcomponent

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }} - Fundação de Amparo à Pesquisa e Extensão Universitária.
@endcomponent
@endslot
@endcomponent