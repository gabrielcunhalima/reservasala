@extends('layouts.app')

@section('title', 'Horários disponíveis')

@section('conteudo')
<style>
    .form-check-input.turno-checkbox {
        width: 1.2em;
        height: 1.2em;
        border: 1px solid #198754;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .form-check-label {
        cursor: pointer;
    }

    .form-check-inline {
        margin-right: 0;
    }

    .turno-info {
        display: block;
        color: #6c757d;
        margin-top: 2px;
    }
</style>
<div class="teste my-4 d-none col-md-8 col-sm-12 mx-auto">
    <div class="card">
        <div class="card-header">
            <h2>{{ $sala->nomeSala }}</h2>
            <p class="mb-0">Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}
                a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                @if(session('valor_total'))
                <br>{{ session('valor_total') }}
                @endif
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
            </div>
            @endif

            @if(count($diasDisponiveis) > 0)
            <form id="reservaMultiplaForm" action="{{ route('reserva.agendar') }}" method="POST">
                @csrf
                <input type="hidden" name="sala_id" value="{{ $sala->idSala }}">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="">
                            <tr>
                                <th>Data</th>
                                <th class="text-center">Manhã<br><span class="turno-info"> 08:00 - 12:00 </span></th>
                                <th class="text-center">Tarde<br><span class="turno-info"> 13:00 - 17:00</span></th>
                                <th class="text-center">Dia Todo<br><span class="turno-info"> 08:00 - 17:00</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diasDisponiveis as $data => $turnosDisponiveis)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($data)->locale('pt_BR')->translatedFormat('d/m/Y (l)') }}</td>

                                <td class="text-center">
                                    @php
                                    $turnoManha = collect($turnosDisponiveis)->where('IDTurno', 1)->first();
                                    @endphp

                                    @if($turnoManha && $turnoManha['Disponivel'])
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" name="reservas[{{ $data }}]" value="1" id="manha_{{ $loop->index }}" class="form-check-input turno-checkbox" data-dia="{{ $data }}" data-turno="1" onchange="validarSelecao('{{ $data }}')">
                                    </div>
                                    @else
                                    <span class="badge bg-danger">X</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @php
                                    $turnoTarde = collect($turnosDisponiveis)->where('IDTurno', 2)->first();
                                    @endphp

                                    @if($turnoTarde && $turnoTarde['Disponivel'])
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" name="reservas[{{ $data }}]" value="2" id="tarde_{{ $loop->index }}" class="form-check-input turno-checkbox" data-dia="{{ $data }}" data-turno="2" onchange="validarSelecao('{{ $data }}')">
                                    </div>
                                    @else
                                    <span class="badge bg-danger">X</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @php
                                    $turnoDiaTodo = collect($turnosDisponiveis)->where('IDTurno', 3)->first();
                                    @endphp

                                    @if($turnoDiaTodo && $turnoDiaTodo['Disponivel'])
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" name="reservas[{{ $data }}]" value="3" id="dia_todo_{{ $loop->index }}" class="form-check-input turno-checkbox dia-todo" data-dia="{{ $data }}" data-turno="3" onchange="validarSelecao('{{ $data }}')">
                                    </div>
                                    @else
                                    <span class="badge bg-danger">X</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('reserva.disponibilidade', $sala->idSala) }}" class="btn btn-outline-primary ">
                        <i class="fas fa-arrow-left"></i> Nova Consulta
                    </a>
                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="fas fa-calendar-check"></i> Concluir Reserva
                    </button>
                </div>
            </form>
            @else
            <div class="alert alert-warning">
                Nenhuma disponibilidade encontrada neste período.
            </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
    function validarSelecao(data) {
        const checkboxes = document.querySelectorAll(`.turno-checkbox[data-dia="${data}"]`);

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function(event) {
                checkboxes.forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const datas = [...new Set(Array.from(document.querySelectorAll('.turno-checkbox')).map(cb => cb.dataset.dia))];
        datas.forEach(data => validarSelecao(data));

        document.getElementById('reservaMultiplaForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.turno-checkbox:checked');

            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos um turno para reservar');
                return;
            }
        });

        // animação dos cards
        const cards = document.querySelectorAll('.teste.my-4');

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
@endsection