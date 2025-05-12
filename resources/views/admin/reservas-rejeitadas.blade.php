@extends('layouts.app')

@section('title', 'Reservas Rejeitadas')

@section('conteudo')
<div class="teste mt-4 d-none">
    <div class="card col-md-10 col-sm-12 mx-auto shadow-lg">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h3><b>Reservas Rejeitadas</b></h3>
        </div>

        <div class="card-body bg-cinza2">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if(count($reservas) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>ID</th>
                            <th>Sala</th>
                            <th>Solicitante</th>
                            <th>Data Inicial</th>
                            <th>Data Final</th>
                            <th>Rejeitada em</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->idReserva }}</td>
                            <td>{{ $reserva->nomeSala }}</td>
                            <td>{{ $reserva->nome }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->dataReservaInicial)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->dataReservaFinal)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->dataAnalise)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.reservas.visualizar', $reserva->idReserva) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Não há reservas rejeitadas no momento.
            </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> Voltar ao Painel
                </a>
            </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.teste.mt-4');
    
    cards.forEach(card => {
        card.classList.add('d-none');
    });

    function animateCards() {
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.remove('d-none');
                card.classList.add('initial-animation');
                card.offsetHeight;

                setTimeout(() => {
                    card.classList.add('show-card');
                    
                    setTimeout(() => {
                        card.classList.remove('initial-animation');
                        card.classList.remove('show-card');
                    }, 700); 
                }, 10);
            }, 150 * index);
        });
    }
    
    setTimeout(animateCards, 200);
});
</script>
@endsection