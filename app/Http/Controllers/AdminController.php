<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use App\Mail\ReservaAprovada;
use App\Mail\ReservaRejeitada;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $reservasPendentes = Reserva::with(['sala', 'turno'])
            ->where('SituacaoAprovada', 0)
            ->orderBy('dataReserva', 'asc')
            ->paginate(10);

        return view('admin.dashboard', compact('reservasPendentes'));
    }

    public function aprovarReserva($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update([
            'SituacaoAprovada' => 1,
            'DataAnalise' => now()
        ]);

        Mail::to($reserva->email)
            ->queue(new ReservaAprovada($reserva));

        return back()->with('success', 'Reserva aprovada com sucesso!');
    }

    public function rejeitarReserva(Request $request, $id)
    {
        $request->validate(['justificativa' => 'required|string|max:500']);

        $reserva = Reserva::findOrFail($id);
        $reserva->update([
            'SituacaoAprovada' => 2,
            'DataAnalise' => now(),
            'Justificativa' => $request->justificativa
        ]);

        Mail::to($reserva->email)
            ->queue(new ReservaRejeitada($reserva));

        return back()->with('success', 'Reserva rejeitada com sucesso!');
    }
}