@extends('layouts.app')

@section('title', $tituloPagina)

@section('conteudo')
<style>
    .card {
        margin-top: 60px !important;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin: 0 auto;
    }

    .card-img-top {
        object-fit: cover;
    }

    .card-body {
        padding: 25px;
    }

    .card-title {
        font-weight: 700;
        margin-bottom: 15px;
    }

    .card-text {
        color: rgb(75, 82, 88);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .card-footer {
        background-color: white;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 25px;
    }

    .badge {
        font-weight: 500;
        padding: 7px 12px;
        border-radius: 30px;
        margin-right: 8px;
    }

    .btn-outline-secondary {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }
</style>

<div class="teste mb-5 d-none">
    <div class="card col-md-6 col-sm-12">
        <div class="card-header text-center bg-white border-0 pt-4 px-4">
            <h2 class="card-title text-verde">{{ $sala->nomeSala }}</h2>
        </div>

        <div class="card-body rounded py-0">
            <div class="row">
                <div class="col-12">
                    @if($sala->imagem)
                    <img src="{{ asset('storage/imagens/' . $sala->imagem . '.png') }}" alt="Imagem da {{ $sala->nomeSala }}" class="img-fluid rounded" style="width: 100%; height: 250px; object-fit: cover;">
                    @endif
                </div>
                <div class="col-12 my-3">
                    <h5 class="text-primary">Descrição</h5>
                    <p class="card-text">{!! $sala->descricao !!}</p>

                    <h5 class="text-primary mt-4">Detalhes</h5>
                    <p class="mb-4">
                        <strong>Capacidade: </strong>{{ $sala->capacidade }} pessoas<br>
                        <strong>Localização: </strong>{{ $sala->localizacao }}
                    </p>
                </div>

            </div>
        </div>

        <div class="card-footer bg-cinza-gradiente border-0 py-3 d-flex justify-content-end">
            <a href="{{ route('home') }}" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('reserva.disponibilidade', $sala->idSala) }}" class="btn btn-success ms-2"> Verificar Disponibilidade</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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