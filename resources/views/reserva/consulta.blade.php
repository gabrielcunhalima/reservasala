@extends('layouts.app')

@section('title', 'Consulta de Reserva')

@section('conteudo')

<div class="teste mt-5 d-none">
    <div class="card col-md-8 col-lg-5 col-sm-12 mx-auto shadow-lg">
        <div class="card-header text-center">
            <h3><b>Consulta de Reserva</b></h3>
        </div>

        <div class="card-body bg-cinza2">
            @if(isset($reserva))
            <div class="mb-4">
                <h5 class="text-center"><b>Reserva #{{ $reserva->idReserva }}</b></h5>
                <p>Sala: <strong>{{ $reserva->sala->nomeSala }}</strong></p>
                <p>Solicitante: <strong>{{ $reserva->nome }}</strong></p>
                <p>Solicitado em: <strong>{{ \Carbon\Carbon::parse($reserva->solicitadoEm)->format('d/m/Y H:i') }}</strong></p>
                <p>Motivo da Reserva e Informações Adicionais:<strong> {{ $reserva->motivoReserva }}</strong></p>
                @if($reserva->observacao)
                <p><strong>Observações:</strong> {{ $reserva->observacao }}</p>
                @endif
            </div>
            
            @if(session('success') || isset($success))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') ?? $success }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            
            <div class="mb-4">
                <h5 class="pb-2">Dias Reservados:</h5>
                <ul class="list-group">
                    @foreach($reserva->datasReserva as $dataReserva)
                    <li class="list-group-item bg-cinza d-flex justify-content-between align-items-center">
                        <div>
                            {{ \Carbon\Carbon::parse($dataReserva->data)->locale('pt_BR')->translatedFormat('d/m/Y (l)') }}

                            @if($dataReserva->manha && !$dataReserva->tarde)
                            - <span class="badge bg-manha">Manhã</span>
                            @elseif(!$dataReserva->manha && $dataReserva->tarde)
                            - <span class="badge bg-tarde">Tarde</span>
                            @elseif($dataReserva->manha && $dataReserva->tarde)
                            - <span class="badge bg-diatodo">Dia Todo</span>
                            @endif
                        </div>
                        
                        @if($reserva->situacaoAprovada != 2)
                        <button type="button" class="btn btn-sm btn-danger cancelar-reserva" data-id="{{ $dataReserva->id }}" data-data="{{ \Carbon\Carbon::parse($dataReserva->data)->format('d/m/Y') }}" data-bs-toggle="modal" data-bs-target="#confirmaCancelamento">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </button>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="text-center py-3">
                @if($reserva->situacaoAprovada == 0)
                <div class="alert alert-warning">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h5>Reserva em Análise</h5>
                    <p>Sua reserva está sendo analisada.</p>
                </div>
                @elseif($reserva->situacaoAprovada == 1)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h5>Reserva Aprovada</h5>
                    <p>Sua reserva foi aprovada! Aguardamos você na data marcada.</p>
                </div>
                @elseif($reserva->situacaoAprovada == -1)
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h5>Reserva Rejeitada</h5>
                    @if($reserva->justificativa)
                    <p>Motivo: {{ $reserva->justificativa }}</p>
                    @endif
                </div>
                @endif
            </div>

            <div class="modal fade" id="confirmaCancelamento" tabindex="-1" aria-labelledby="confirmaCancelamentoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmaCancelamentoLabel">Confirmar Cancelamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <p>Você tem certeza que deseja cancelar a reserva do dia <span id="dataReserva"></span>?</p>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" id="hashCancelamento" name="hashCancelamento" required maxlength="5" placeholder="Código de Cancelamento">
                                <div class="form-text">Este código foi enviado para o seu email <b>quando você realizou a reserva.</b></div>
                            </div>
                            <p class="text-danger"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Não, manter reserva</button>
                            <form id="formCancelar" action="{{ route('reserva.cancelar.dia') }}" method="POST">
                                @csrf
                                <input type="hidden" name="data_reserva_id" id="dataReservaId">
                                <input type="hidden" name="reserva_id" value="{{ $reserva->idReserva }}">
                                <input type="hidden" name="cpf" value="{{ $reserva->cpf }}">
                                <input type="hidden" name="hashCancelamento" id="hashCancelamentoField">
                                <button type="submit" class="btn btn-danger">Sim, cancelar reserva</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('reserva.consulta') }}" class="btn btn-outline-secondary mx-2">
                    <i class="fas fa-search"></i> Nova Consulta
                </a>
                <a href="{{ route('home') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-home"></i> Página Inicial
                </a>
            </div>
            @else
            <form action="{{ route('reserva.consulta') }}" method="POST" id="consultaForm">
                @csrf

                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" required>
                </div>

                <div class="mb-4">
                    <label for="id_reserva" class="form-label">Número da Reserva</label>
                    <input type="text" class="form-control" id="id_reserva" name="id_reserva" required pattern="[0-9]*" inputmode="numeric" onkeypress="return somenteNumeros(event)">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('home') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-success ms-2">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
<div class="my-3">
    &nbsp;
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    function somenteNumeros(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('.cpf-mask').mask('000.000.000-00');

        const idReservaInput = document.getElementById('id_reserva');
        if (idReservaInput) {
            idReservaInput.addEventListener('paste', function(e) {
                let pastedText = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(pastedText)) {
                    e.preventDefault();
                }
            });
            
            idReservaInput.addEventListener('blur', function() {
                this.value = this.value.replace(/\D/g, '');
            });
        }

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
        
        const botoesCancelar = document.querySelectorAll('.cancelar-reserva');
        botoesCancelar.forEach(botao => {
            botao.addEventListener('click', function() {
                const dataReservaId = this.getAttribute('data-id');
                const dataReserva = this.getAttribute('data-data');
                
                document.getElementById('dataReservaId').value = dataReservaId;
                document.getElementById('dataReserva').textContent = dataReserva;
                
                document.getElementById('hashCancelamento').value = '';
            });
        });
        
        document.getElementById('formCancelar').addEventListener('submit', function(e) {
            e.preventDefault();
            const hashInput = document.getElementById('hashCancelamento');
            const hashField = document.getElementById('hashCancelamentoField');
            
            if (hashInput.value.trim() === '') {
                alert('Por favor, digite o código de cancelamento.');
                return false;
            }
            
            hashField.value = hashInput.value.trim();
            this.submit();
        });
    });
</script>
@endsection