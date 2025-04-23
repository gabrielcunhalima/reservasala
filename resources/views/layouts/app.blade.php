@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAPEU | @yield('title')</title>


    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('imagens/fapeu_ico.ico') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- jQuery (carregar antes do Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Acessibilidade -->
    <!-- <script src="https://cdn.userway.org/widget.js" data-account="YinJfS8smr"></script> -->
</head>

<body class="d-flex flex-column min-vh-100">

<header class="bg-principal my-4">
    <div class="container">
        <div class="row my-2">
            <div class="justify-content-center text-center">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('imagens/bannerreserva.png') }}" alt="Logo" class="img-fluid" style="max-height: 12vh;">
                </a>
            </div>
        </div>
    </div>
</header>

    <main class="container">@yield('conteudo')</main>

    <footer class="text-center">
        <div class="p-3 bg-light"><a class="text-verde" href="https://fapeu.com.br/"> © {{ Carbon::parse('now')->locale('pt_BR')->translatedFormat('Y') }} Fundação de Amparo à Pesquisa e Extensão Universitária</a></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>