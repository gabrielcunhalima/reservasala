@extends('layouts.app')

@section('title', 'Login Administrativo')

@section('conteudo')
<div class="teste mt-4 d-none">
    <div class="card col-md-4 col-sm-12 mx-auto shadow-lg">
        <div class="card-header text-center">
            <h3><b>Painel Administrativo</b></h3>
        </div>

        <div class="card-body bg-cinza2">
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('home') }}" class="btn btn-danger me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </div>
            </form>
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