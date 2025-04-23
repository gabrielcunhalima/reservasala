@extends('layouts.app')

@section('title', 'Agendar Reserva Múltipla')

@section('conteudo')
<div class="container my-4">
    <div class="card">
        <div class="card-header ">
            <h2 class="mb-0">{{ $sala->nomeSala }} - Reservar</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('reserva.store.multi') }}" method="POST" id="formAgendamento">
                @csrf
                <input type="hidden" name="sala_id" value="{{ $sala->idSala }}">
                
                <div class="mb-3">
                    <h5>Dias Selecionados:</h5>
                    <ul class="list-group mb-3">
                        @foreach($diasSelecionados as $dia)
                        <li class="list-group-item bg-cinza">
                            {{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->translatedFormat('d/m/Y (l)') }} - 
                            {{ $dia['turno']->descricao }} ({{ $dia['turno']->horario }})
                        </li>
                        <input type="hidden" name="reservas[{{ $dia['data'] }}]" value="{{ $dia['turno']->idTurno }}">
                        <input type="hidden" name="dias[]" value="{{ $dia['data'] }}">
                        @endforeach
                    </ul>
                    
                    <!-- @php
                        $valorTotal = 0;
                        foreach($diasSelecionados as $dia) {
                            $valorTotal += ($dia['turno']->IDTurno == 3) ? $sala->ValorIntegral : $sala->ValorMeioPeriodo;
                        }
                    @endphp
                    
                    <div class="alert alert-info">
                        <strong>Valor Total:</strong> R$ {{ number_format($valorTotal, 2, ',', '.') }}
                        <input type="hidden" name="Valor" value="{{ $valorTotal }}">
                    </div> -->
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="cpf" class="form-label">CPF *</label>
                        <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="telefone" class="form-label">Telefone *</label>
                        <input type="tel" class="form-control phone-mask" id="telefone" name="telefone" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="FuncFapeu" class="form-label">É funcionário da FAPEU? *</label>
                        <select class="form-select" id="FuncFapeu" name="FuncFapeu" required>
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6" id="matriculaField" style="display: none;">
                        <label for="matricula" class="form-label">Matrícula</label>
                        <input type="text" class="form-control" id="matricula" name="matricula">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="PossuiProjeto" class="form-label">Possui projeto? *</label>
                        <select class="form-select" id="PossuiProjeto" name="PossuiProjeto" required>
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6" id="projetoField" style="display: none;">
                        <label for="CodProjeto" class="form-label">Código do Projeto</label>
                        <input type="text" class="form-control" id="CodProjeto" name="CodProjeto">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="FormaPgto" class="form-label">Forma de Pagamento *</label>
                        <select class="form-select" id="FormaPgto" name="FormaPgto" required>
                            <option value="">Selecione...</option>
                            <option value="1">Boleto Bancário</option>
                            <option value="2">PIiiiiiiiiX</option>
                            <option value="3">Transferência</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label for="MotivoReserva" class="form-label">Motivo da Reserva e Informações Adicionais *</label>
                        <textarea class="form-control" id="MotivoReserva" name="MotivoReserva" rows="3" required></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('reserva.disponibilidade', ['id' => $sala->idSala]) }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Confirmar Reservas
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
    // Máscaras para CPF e Telefone
    $('.cpf-mask').mask('000.000.000-00');
    $('.phone-mask').mask('(00) 00000-0000');

    // Mostrar/ocultar campo matrícula
    document.getElementById('FuncFapeu').addEventListener('change', function() {
        const matriculaField = document.getElementById('matriculaField');
        matriculaField.style.display = this.value == '1' ? 'block' : 'none';
        document.getElementById('matricula').required = this.value == '1';
    });

    // Mostrar/ocultar campo código do projeto
    document.getElementById('PossuiProjeto').addEventListener('change', function() {
        const projetoField = document.getElementById('projetoField');
        projetoField.style.display = this.value == '1' ? 'block' : 'none';
        document.getElementById('CodProjeto').required = this.value == '1';
    });

    // Validação do formulário
    const form = document.getElementById('formAgendamento');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>
@endsection