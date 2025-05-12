@extends('layouts.app')

@section('title', 'Verificar Disponibilidade')

@section('conteudo')
<style>
    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
</style>
<div class="teste d-none" style="margin-top: 10vh;">
    <div class="card col-lg-4 col-md-6 col-sm-12 offset-4 shadow-lg mx-auto">
        <div class="card-header text-center">
            <h3><b>{{ $sala->nomeSala }}</b></h3>
        </div>
        <div class="card-body bg-cinza2">
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            
            <form action="{{ route('reserva.verificar') }}" method="POST" id="disponibilidadeForm">
                @csrf
                <input type="hidden" name="sala_id" value="{{ $sala->idSala }}">

                <div class="mb-3">
                    <label for="data_inicio" class="form-label">Data Inicial</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" placeholder="dd/mm/aaaa" required>
                </div>

                <div class="mb-5">
                    <label for="data_fim" class="form-label">Data Final</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                </div>

                <div class="justify-content-end d-flex">
                    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Voltar</a>
                    <button type="submit" class="btn btn-success ms-2">Verificar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hoje = new Date();
    const amanha = new Date(hoje);
    amanha.setDate(hoje.getDate() + 1);
    const depoisDeAmanha = new Date(hoje);
    depoisDeAmanha.setDate(hoje.getDate() + 2);
    
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    const dataInicioInput = document.getElementById('data_inicio');
    const dataFimInput = document.getElementById('data_fim');
    
    dataInicioInput.min = formatDate(depoisDeAmanha);
    dataFimInput.min = formatDate(depoisDeAmanha);
    
    dataInicioInput.value = formatDate(depoisDeAmanha);
    
    dataInicioInput.addEventListener('change', function() {
        const dataInicio = new Date(this.value);
        dataFimInput.min = this.value;
        
        if (new Date(dataFimInput.value) < dataInicio) {
            dataFimInput.value = this.value;
        }
    });

    document.getElementById('disponibilidadeForm').addEventListener('submit', function(e) {
        const dataInicio = new Date(dataInicioInput.value);
        const dataFim = new Date(dataFimInput.value);
        
        if (dataFim < dataInicio) {
            e.preventDefault();
            alert('A data final deve ser maior ou igual à data inicial');
            return;
        }
        
        if (dataInicio <= amanha) {
            e.preventDefault();
            alert('Não é possível fazer reservas para hoje ou amanhã. Por favor, selecione datas futuras.');
            return;
        }
    });

    const cards = document.querySelectorAll('.teste');
    
    cards.forEach(card => {
        card.classList.add('d-none');
    });

    function animateCards() {
        cards.forEach((card, index) => {
            setTimeout(() => {
                // remove a classe d-none
                card.classList.remove('d-none');
                card.classList.add('initial-animation');
                card.offsetHeight;

                // animacao de aparecer
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
@endsection