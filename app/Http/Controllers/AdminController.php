<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sala;
use App\Models\Reserva;
use App\Models\DataReserva;
use App\Models\Turno;
use App\Mail\ReservaConfirmada;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\ReservaAprovada;
use App\Mail\ReservaRejeitada;


class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $name = $request->input('name');
        $senha = $request->input('password');

        $admin = DB::table('Admins')->where('name', $name)->first();

        if ($admin && password_verify($senha, $admin->password)) {
            session(['admin_logado' => true, 'admin_nome' => $admin->name]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Nome ou senha incorretos!');
    }

    public function dashboard()
    {
        $reservasPendentesCount = DB::table('Reserva')->where('situacaoAprovada', 0)->count();
        $reservasAprovadasCount = DB::table('Reserva')->where('situacaoAprovada', 1)->count();
        $reservasRejeitadasCount = DB::table('Reserva')->where('situacaoAprovada', 2)->count();
        $salasCount = DB::table('Salas')->where('ativo', 1)->count();

        $reservasRecentes = DB::table('Reserva')
            ->join('Salas', 'Reserva.idSala', '=', 'Salas.idSala')
            ->orderBy('Reserva.solicitadoEm', 'desc')
            ->limit(5)
            ->select('Reserva.*', 'Salas.nomeSala')
            ->get();

        return view('admin.dashboard', [
            'reservasPendentesCount' => $reservasPendentesCount,
            'reservasAprovadasCount' => $reservasAprovadasCount,
            'reservasRejeitadasCount' => $reservasRejeitadasCount,
            'salasCount' => $salasCount,
            'reservasRecentes' => $reservasRecentes
        ]);
    }

    public function logout()
    {
        session()->forget(['admin_logado', 'admin_nome']);
        return redirect()->route('admin.login');
    }

    public function reservasPendentes()
    {
        $reservasPendentes = DB::table('Reserva')
            ->join('Salas', 'Reserva.idSala', '=', 'Salas.idSala')
            ->where('situacaoAprovada', 0)
            ->orderBy('solicitadoEm', 'desc')
            ->select('Reserva.*', 'Salas.nomeSala')
            ->get();

        return view('admin.reservas-pendentes', [
            'reservas' => $reservasPendentes
        ]);
    }

    public function visualizarReserva($id)
    {
        $reserva = DB::table('Reserva')
            ->join('Salas', 'Reserva.idSala', '=', 'Salas.idSala')
            ->where('idReserva', $id)
            ->select('Reserva.*', 'Salas.nomeSala', 'Salas.valorMeioPeriodo', 'Salas.valorIntegral')
            ->first();

        $sala = DB::table('Salas')->where('idSala', $reserva->idSala)->first();

        if (!$reserva) {
            return back()->with('error', 'Reserva não encontrada.');
        }

        $datasReserva = DB::table('DataReserva')
            ->where('idReserva', $id)
            ->orderBy('data', 'asc')
            ->get();

        return view('admin.visualizar-reserva', [
            'reserva' => $reserva,
            'datasReserva' => $datasReserva,
            'sala' => $sala
        ]);
    }

    public function aprovarReserva(Request $request, $id)
    {
        $request->validate([
            'formaPgto' => 'required|integer|between:1,4',
            'observacao' => 'nullable|string|max:500'
        ]);

        $reserva = DB::table('Reserva')->where('idReserva', $id)->first();

        if (!$reserva) {
            return back()->with('error', 'Reserva não encontrada.');
        }

        DB::table('Reserva')
            ->where('idReserva', $id)
            ->update([
                'situacaoAprovada' => 1,
                'dataAnalise' => Carbon::now()->toDateTimeString(),
                'formaPgto' => $request->formaPgto,
                'observacao' => $request->observacao
            ]);

        try {
            $reservaAtualizada = DB::table('Reserva')->where('idReserva', $id)->first();

            Mail::to($reservaAtualizada->email)->send(new ReservaAprovada($reservaAtualizada));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email: ' . $e->getMessage());
        }

        return back()->with('success', 'Reserva #' . $id . ' aprovada com sucesso!');
    }

    public function rejeitarReserva(Request $request, $id)
    {
        $request->validate([
            'justificativa' => 'required|string|max:500'
        ]);

        $reserva = DB::table('Reserva')->where('idReserva', $id)->first();

        if (!$reserva) {
            return back()->with('error', 'Reserva não encontrada.');
        }

        DB::beginTransaction();
        try {
            DB::table('DataReserva')
                ->where('idReserva', $id)
                ->delete();

            DB::table('Reserva')
                ->where('idReserva', $id)
                ->update([
                    'situacaoAprovada' => 2,
                    'dataAnalise' => Carbon::now()->toDateTimeString(),
                    'justificativa' => $request->justificativa
                ]);

            $reservaAtualizada = DB::table('Reserva')->where('idReserva', $id)->first();

            try {
                Mail::to($reservaAtualizada->email)->send(new ReservaRejeitada($reservaAtualizada));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Reserva #' . $id . ' rejeitada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao rejeitar reserva: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao rejeitar a reserva: ' . $e->getMessage());
        }
    }

    public function reservasAprovadas()
    {
        $reservasAprovadas = DB::table('Reserva')
            ->join('Salas', 'Reserva.idSala', '=', 'Salas.idSala')
            ->where('situacaoAprovada', 1)
            ->orderBy('dataAnalise', 'desc')
            ->select('Reserva.*', 'Salas.nomeSala')
            ->get();

        return view('admin.reservas-aprovadas', [
            'reservas' => $reservasAprovadas,
            'titulo' => 'Reservas Aprovadas'
        ]);
    }

    public function reservasRejeitadas()
    {
        $reservasRejeitadas = DB::table('Reserva')
            ->join('Salas', 'Reserva.idSala', '=', 'Salas.idSala')
            ->where('situacaoAprovada', 2)
            ->orderBy('dataAnalise', 'desc')
            ->select('Reserva.*', 'Salas.nomeSala')
            ->get();

        return view('admin.reservas-rejeitadas', [
            'reservas' => $reservasRejeitadas,
            'titulo' => 'Reservas Rejeitadas'
        ]);
    }
}
