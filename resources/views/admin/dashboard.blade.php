@extends('layouts.app')

@section('title', 'Painel Administrativo')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header bg-admin text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Painel Administrativo
                </h2>
                <span class="badge bg-warning">
                    {{ $reservasPendentes->total() }} Reservas Pendentes
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sala</th>
                            <th>Solicitante</th>
                            <th>Data/Período</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservasPendentes as $reserva)
                        <tr>
                            <td>#{{ $reserva->idReserva }}</td>
                            <td>{{ $reserva->sala->nomeSala }}</td>
                            <td>
                                {{ $reserva->nome }}<br>
                                <small class="text-muted">{{ $reserva->email }}</small>
                            </td>
                            <td>
                                {{ $reserva->dataReserva->format('d/m/Y') }}<br>
                                <small class="text-muted">{{ $reserva->turno->Descricao }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('admin.reserva.aprovar', $reserva->IDReserva) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Aprovar
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejeitarModal{{ $reserva->IDReserva }}">
                                        <i class="fas fa-times"></i> Rejeitar
                                    </button>
                                </div>

                                <!-- Modal de Rejeição -->
                                <div class="modal fade" id="rejeitarModal{{ $reserva->IDReserva }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Rejeitar Reserva #{{ $reserva->IDReserva }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.reserva.rejeitar', $reserva->IDReserva) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Justificativa:</label>
                                                        <textarea class="form-control" name="justificativa" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Confirmar Rejeição</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    Nenhuma reserva pendente no momento.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reservasPendentes->links() }}
        </div>
    </div>
</div>
@endsection