@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# Olá {{ $reserva->nome }},

Temos boas notícias! Sua reserva em **{{ $reserva->sala->nomeSala }}** foi **aprovada**. Seguem os detalhes:

Número da Reserva: **{{ $reserva->idReserva }}**<br>
Sala: **{{ $reserva->sala->nomeSala }}**
&nbsp;

## Dias reservados:
@foreach ($reserva->datasReserva as $dataReserva)
**{{ \Carbon\Carbon::parse($dataReserva->data)->format('d/m/Y') }}**
({{ ($dataReserva->diaTodo || ($dataReserva->manha && $dataReserva->tarde)) ? 'Dia Todo' : ($dataReserva->manha ? 'Manhã' : '') . ($dataReserva->tarde ? 'Tarde' : '') }})<br>
@endforeach

@component('mail::panel', ['color' => 'success'])
**Status:** <span style="color:green;">Rejeitada</span>
@endcomponent

Informamos que a sua solicitação de reserva foi analisada e aprovada. Você poderá utilizar o espaço nas datas e horários reservados.

## Instruções de Pagamento

@if($reserva->formaPgto == 1)
Enviamos em anexo o boleto bancário para pagamento. Por favor, efetue o pagamento até a data de vencimento.
@elseif($reserva->formaPgto == 2)
Para sua conveniência, disponibilizamos o pagamento via PIX. Clique no link abaixo para gerar o código PIX:

@component('mail::button', ['url' => route('pagamento.pix', ['id' => $reserva->idReserva]), 'color' => 'success'])
Gerar PIX para Pagamento
@endcomponent

@elseif($reserva->formaPgto == 3)
Quais dados serão inseridos aqui? Para transferencia por projeto.
@elseif($reserva->formaPgto == 4)
Por favor, realize um depósito para a conta abaixo:
- Banco: Banco do Brasil
- Agência: XXXX-X
- Conta: XXXXX-X
- CNPJ: XX.XXX.XXX/0001-XX
- Titular: Fundação de Amparo à Pesquisa e Extensão Universitária
@endif

@if($reserva->observacao)
## Observações Adicionais
{{ $reserva->observacao }}
@endif

Em caso de necessidade de cancelamento, utilize o código de cancelamento enviado no e-mail de confirmação.

Código de Cancelamento: **{{ $reserva->hashCancelamento }}**

@component('mail::button', ['url' => route('home'), 'color' => 'primary'])
Acessar Reserva de Salas
@endcomponent

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }} - Fundação de Amparo à Pesquisa e Extensão Universitária.
@endcomponent
@endslot
@endcomponent