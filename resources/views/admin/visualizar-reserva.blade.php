@extends('layouts.app')

@section('title', 'Detalhes da Reserva')

@section('conteudo')
<div class="teste mt-4 d-none mb-5">
    <div class="card col-md-10 col-sm-12 mx-auto shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3><b>Detalhes da Reserva #{{ $reserva->idReserva }}</b></h3>
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

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-principal text-white">
                            <h5 class="mb-0">Informações da Reserva</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Sala:</strong> {{ $reserva->nomeSala }}</p>
                            <p><strong>Solicitado em:</strong> {{ \Carbon\Carbon::parse($reserva->solicitadoEm)->format('d/m/Y H:i') }}</p>
                            <p><strong>Status:</strong>
                                @if($reserva->situacaoAprovada == 0)
                                <span class="badge bg-warning">Pendente</span>
                                @elseif($reserva->situacaoAprovada == 1)
                                <span class="badge bg-success">Aprovada</span>
                                @else
                                <span class="badge bg-danger">Rejeitada</span>
                                @endif
                            </p>
                            <p><strong>Forma de Pagamento:</strong>
                                @switch($reserva->formaPgto)
                                @case(1) Boleto Bancário @break
                                @case(2) PIX @break
                                @case(3) Transferência @break
                                @case(4) Depósito @break
                                @default Não informado
                                @endswitch
                            </p>
                            <p><strong>Valor Total:</strong> R$ {{ number_format($reserva->valor ?? 0, 2, ',', '.') }}<br><span class="text-muted">(Incluído Taxa de Limpeza)</span></p>
                            <p><strong>Motivo da Reserva e Informações Adicionais:</strong><br> {{ $reserva->motivoReserva }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-principal text-white">
                            <h5 class="mb-0">Informações do Solicitante</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nome:</strong> {{ $reserva->nome }}</p>
                            <p><strong>E-mail:</strong> {{ $reserva->email }}</p>
                            <p><strong>Telefone:</strong> {{ $reserva->telefone }}</p>
                            <p><strong>CPF:</strong> {{ $reserva->cpf }}</p>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <p><strong>Funcionário FAPEU:</strong> {{ $reserva->funcFapeu ? 'Sim' : 'Não' }}</p>
                                </div>
                                @if($reserva->funcFapeu)
                                <div class="col-md-6 col-sm-12">
                                    <p><strong>Matrícula:</strong> {{ $reserva->matricula }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <p><strong>Possui Projeto:</strong> {{ $reserva->possuiProjeto ? 'Sim' : 'Não' }}</p>
                                </div>
                                @if($reserva->possuiProjeto)
                                <div class="col-md-6 col-sm-12">
                                    <p><strong>Código do Projeto:</strong> {{ $reserva->codProjeto }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-principal text-white">
                    <h5 class="mb-0">Datas Reservadas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Período</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datasReserva as $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data->data)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($data->diaTodo == 1)
                                        <span class="badge bg-diatodo">Dia Todo</span>
                                        @elseif($data->manha == 1)
                                        <span class="badge bg-manha">Manhã</span>
                                        @elseif($data->tarde == 1)
                                        <span class="badge bg-tarde">Tarde</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data->diaTodo == 1)
                                        R$ {{ number_format($reserva->valorIntegral, 2, ',', '.') }}
                                        @else
                                        R$ {{ number_format($reserva->valorMeioPeriodo, 2, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($reserva->situacaoAprovada == 0)
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Aprovar Reserva</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.reservas.aprovar', $reserva->idReserva) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="observacao" class="form-label">Observações:</label>
                                    <textarea class="form-control" id="observacao" name="observacao" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="formaPgto" class="form-label">Forma de Pagamento:</label>
                                    <select class="form-select" id="formaPgto" name="formaPgto" required>
                                        <option value="" disabled selected>Selecione uma opção</option>
                                        <option value="1">Boleto Bancário</option>
                                        <option value="2">PIX</option>
                                        <option value="3">Transferência de Projeto</option>
                                        <option value="4">Depósito</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Confirmar Aprovação
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Rejeitar Reserva</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.reservas.rejeitar', $reserva->idReserva) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="justificativa" class="form-label">Justificativa para rejeição:</label>
                                    <textarea class="form-control" id="justificativa" name="justificativa" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times-circle"></i> Confirmar Rejeição
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-{{ $reserva->situacaoAprovada == 1 ? 'success' : 'danger' }}">
                <i class="fas fa-{{ $reserva->situacaoAprovada == 1 ? 'check' : 'times' }}-circle"></i>
                Esta reserva foi {{ $reserva->situacaoAprovada == 1 ? 'aprovada' : 'rejeitada' }} em {{ \Carbon\Carbon::parse($reserva->dataAnalise)->format('d/m/Y H:i') }}.
                @if($reserva->situacaoAprovada == 2 && $reserva->justificativa)
                <p><strong>Justificativa:</strong> {{ $reserva->justificativa }}</p>
                @endif
                @if($reserva->situacaoAprovada == 1 && $reserva->observacao)
                <p><strong>Observações:</strong> {{ $reserva->observacao }}</p>
                @endif
            </div>
            @endif
        </div>

        <div class="d-flex justify-content-end card-footer">
            <a href="{{ route('admin.reservas.pendentes') }}" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> Voltar
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