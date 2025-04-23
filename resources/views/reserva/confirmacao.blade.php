@extends('layouts.app')

@section('title', 'Confirmação de Reserva')

@section('conteudo')
<div class="teste my-4 d-none col-md-6 col-sm-12 mx-auto">
    <div class="card">
        <div class="card-header">
            <h2>Reserva Confirmada</h2>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <!-- @if(session('valor_total'))
                        <br>{{ session('valor_total') }}
                    @endif -->
                    <br><br>
                    <h5>Número da Reserva:</h5>

                        @foreach(session('reservas_ids') as $id)
                            <h1>{{ $id }}</h1>
                        @endforeach

                </div>
            @endif
            
            <p>Um e-mail de confirmação foi enviado para o endereço fornecido.</p>
            
            <a href="{{ route('home') }}" class="btn btn-primary mx-auto">
                <i class="fas fa-home"></i> Voltar para a página inicial
            </a>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.teste.my-4');
    
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