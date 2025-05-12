@extends('layouts.app')
@section('title', $tituloPagina)

@section('conteudo')

<style>
    .card-clicked {
        animation: throwUpAnimation 0.5s ease-out forwards;
        z-index: 100;
        position: relative;
    }

    @keyframes throwUpAnimation {
        0% {
            transform: translateY(0);
            opacity: 1;
        }

        20% {
            transform: translateY(-40px) scale(1.00);
            opacity: 0.9;
        }

        100% {
            transform: translateY(-120px) scale(1.00);
            opacity: 0;
        }
    }

    .title-hidden {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .title-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
<div class="row my-4 d-flex justify-content-between">
    <div class="text-start col-auto">
        <span class="text-white">Dispõe dos valores de locação de salas</span><br>
        <a href="{{ asset('arquivos/portaria.pdf') }}" class="btn btn-outline-light btn-sm" target="_blank">
            Portaria N. 002/DE/2024, DE 11 DE ABRIL DE 2024
        </a>
    </div>
    <div class="text-end col-auto">
        <a href="{{ route('reserva.consulta') }}" class="btn btn-outline-light btn-lg">
            <i class="fas fa-search"></i> Já realizou a reserva?
        </a>
    </div>
</div>
<div class="row g-4 mb-4">
    @foreach($salas as $sala)
    <div class="col-md-4 col-sm-12 link-hover d-none">
        <div class="card h-100 border-0 shadow-lg">
            <div class="p-3">
                <h2 class="card-title text-center mb-0 text-verde">{{ $sala->nomeSala }}</h2>
            </div>
            <div class="card-body pt-0">
                <div class="sala-imagem-container pb-3">
                    <img src="{{ asset('storage/imagens/' . $sala->imagem . ".png") }}" alt="Imagem da {{ $sala->nomeSala }}" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                </div>
                <ul class="list-unstyled">
                    <li><strong>Capacidade:</strong> {{ $sala->capacidade }} pessoas</li>
                    <li><strong>Local:</strong> {{ $sala->localizacao }}</li>
                </ul>
            </div>
            <div class="card-footer bg-cinza-gradiente border-0">
                <div class="d-flex justify-content-around mt-2 mb-3">
                    <a href="{{ route('sala.show', $sala->idSala) }}" class="btn btn-outline-secondary">Detalhes</a>
                    <a href="{{ route('reserva.disponibilidade', $sala->idSala) }}" class="btn btn-success"> Verificar Disponibilidade</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<h1 class="text-center text-white d-none my-5">Selecione uma sala para reservar</h1>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.col-md-4.col-sm-12.link-hover');
        const pageTitle = document.querySelector('h1.text-center.text-white');

        if (pageTitle) {
            pageTitle.classList.add('title-hidden');
            pageTitle.classList.remove('d-none');
        }

        cards.forEach(card => {
            card.classList.add('d-none');
        });

        function animateCards() {
            let lastCardDelay = 0;

            cards.forEach((card, index) => {
                const delay = 150 * index;
                lastCardDelay = delay;

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

                }, delay);
            });

            if (pageTitle) {
                const titleDelay = lastCardDelay + 400;

                setTimeout(() => {
                    pageTitle.classList.add('title-visible');
                }, titleDelay);
            }
        }

        setTimeout(animateCards, 200);

    });
</script>
@endsection