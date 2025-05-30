@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('conteudo')
<style>
    .card-link {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .card-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }
</style>
<div class="teste mt-4 d-none mb-3">
    <div class="card col-md-10 col-sm-12 mx-auto shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3><b>Painel Administrativo</b></h3>
            <div>
                @if(session('admin_nome'))
                <span class="me-3">Olá, {{ session('admin_nome') }}</span>
                @endif
            </div>
        </div>

        <div class="card-body bg-cinza2">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('admin.reservas.pendentes') }}" class="text-decoration-none">
                        <div class="card bg-secondary text-white h-100 card-link">
                            <div class="card-body text-center">
                                <h5 class="card-title">Reservas Pendentes</h5>
                                <h2 class="display-4">{{ $reservasPendentesCount }}</h2>
                            </div>
                            <div class="card-footer">
                                <span class="text-white">Avaliar Reservas</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('admin.reservas.aprovadas') }}" class="text-decoration-none">
                        <div class="card bg-success text-white h-100 card-link">
                            <div class="card-body text-center">
                                <h5 class="card-title">Reservas Aprovadas</h5>
                                <h2 class="display-4">{{ $reservasAprovadasCount }}</h2>
                            </div>
                            <div class="card-footer">
                                <span class="text-white">Ver Detalhes</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('admin.reservas.rejeitadas') }}" class="text-decoration-none">
                        <div class="card bg-danger text-white h-100 card-link">
                            <div class="card-body text-center">
                                <h5 class="card-title">Reservas Rejeitadas</h5>
                                <h2 class="display-4">{{ $reservasRejeitadasCount }}</h2>
                            </div>
                            <div class="card-footer">
                                <span class="text-white">Ver Detalhes</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-principal text-white">
                            <h5 class="mb-0">Reservas Recentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sala</th>
                                            <th>Solicitante</th>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reservasRecentes as $reserva)
                                        <tr>
                                            <td>{{ $reserva->idReserva }}</td>
                                            <td>{{ $reserva->nomeSala }}</td>
                                            <td>{{ $reserva->nome }}</td>
                                            <td>{{ \Carbon\Carbon::parse($reserva->dataReservaInicial)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($reserva->situacaoAprovada == 0)
                                                <span class="badge bg-secondary">Pendente</span>
                                                @elseif($reserva->situacaoAprovada == 1)
                                                <span class="badge bg-success">Aprovada</span>
                                                @else
                                                <span class="badge bg-danger">Rejeitada</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.reservas.visualizar', $reserva->idReserva) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhuma reserva encontrada</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end bg-cinza2 border-0">
            <a href="{{ route('admin.logout') }}" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.teste');

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