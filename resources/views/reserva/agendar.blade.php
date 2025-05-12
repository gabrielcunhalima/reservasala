@extends('layouts.app')

@section('title', 'Agendar Reserva')

@section('conteudo')

<div class="container my-4">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ $sala->nomeSala }} - Reservar</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('reserva.store') }}" method="POST" id="formAgendamento">
                @csrf
                <input type="hidden" name="sala_id" value="{{ $sala->idSala }}">

                <div class="">
                    <h5>Dias Selecionados:</h5>
                    <ul class="list-group">
                        @foreach($diasSelecionados as $dia)
                        <li class="list-group-item bg-success-subtle border-0" style="margin-bottom:3px;">
                            {{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->translatedFormat('d/m/Y (l)') }} -
                            {{ $dia['turno']->descricao }} ({{ $dia['turno']->horario }})
                            <span class="float-end">
                                R$ {{ number_format(($dia['turno']->idTurno == 3) ? $sala->valorIntegral : $sala->valorMeioPeriodo, 2, ',', '.') }}
                            </span>
                        </li>
                        <input type="hidden" name="reservas[{{ $dia['data'] }}]" value="{{ $dia['turno']->idTurno }}">
                        @endforeach
                        <li class="list-group-item bg-success-subtle border-0 rounded-bottom" style="margin-bottom:3px;">
                            Taxa de Limpeza ({{ count($diasSelecionados) }} {{ count($diasSelecionados) > 1 ? 'diárias' : 'diária' }})
                            <span class="float-end"> R$ {{ number_format($totalTaxaLimpeza, 2, ',', '.') }}</span>
                        </li>
                    </ul>
                    <ul class="list-group">
                        <li class="my-3 list-group-item bg-success-subtle border-0 rounded">
                            <strong>Valor Total: <span class="float-end">R$ {{ number_format($valorTotal, 2, ',', '.') }}</span></strong>
                        </li>
                    </ul>
                </div>

                <div class="row g-3">
                    <div class="col-md-4 col-sm-12">
                        <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                        <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" required>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <label for="telefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control phone-mask" id="telefone" name="telefone" required>
                    </div>

                    <!-- <div class="col-md-4">
                        <label for="FormaPgto" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select class="form-select" id="FormaPgto" name="FormaPgto" required>
                            <option value="">Selecione...</option>
                            <option value="1">Boleto Bancário</option>
                            <option value="2">PIX</option>
                            <option value="3">Transferência</option>
                            <option value="4">Depósito</option>
                        </select>
                    </div> -->

                    <!-- tem que ter esse FormaPgto, não está passando sem -->

                    <input type="hidden" name="FormaPgto" value="1">

                    <div class="col-md-4 col-sm-12 mt-3">
                        <div class="col-auto">
                            <label for="FuncFapeu" class="form-label">É funcionário da FAPEU (Sede)? <span class="text-danger">*</span></label>
                            <select class="form-select" id="FuncFapeu" name="FuncFapeu" required>
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="col-auto mt-2" id="matriculaField" style="display: none;">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="matricula" name="matricula">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="">
                            <label for="PossuiProjeto" class="form-label">Possui projeto? <span class="text-danger">*</span></label>
                            <select class="form-select" id="PossuiProjeto" name="PossuiProjeto" required>
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="mt-2" id="projetoField" style="display: none;">
                            <label for="CodProjeto" class="form-label">Código do Projeto</label>
                            <input type="text" class="form-control" id="CodProjeto" name="CodProjeto">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="MotivoReserva" class="form-label">
                            Motivo da Reserva e Informações Adicionais <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="MotivoReserva" name="MotivoReserva" rows="3" placeholder="Ex.: Evento haverá coffee break, necessário mesas adicionais, etc." required></textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <span class="text-danger">*</span> <small>Campos obrigatórios</small>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button onclick="window.history.back();" type="button" class="btn btn-danger me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Confirmar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.cpf-mask').mask('000.000.000-00');
        $('.phone-mask').mask('(00) 00000-0000');

        // ocultar campo matrícula
        document.getElementById('FuncFapeu').addEventListener('change', function() {
            const matriculaField = document.getElementById('matriculaField');
            matriculaField.style.display = this.value == '1' ? 'block' : 'none';
            document.getElementById('matricula').required = this.value == '1';
        });

        // ocultar campo código do projeto
        document.getElementById('PossuiProjeto').addEventListener('change', function() {
            const projetoField = document.getElementById('projetoField');
            projetoField.style.display = this.value == '1' ? 'block' : 'none';
            document.getElementById('CodProjeto').required = this.value == '1';
        });

        const form = document.getElementById('formAgendamento');
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection