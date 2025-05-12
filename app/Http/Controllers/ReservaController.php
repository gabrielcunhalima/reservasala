<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sala;
use App\Models\Reserva;
use App\Models\DataReserva;
use App\Models\Turno;
use App\Mail\ReservaConfirmada;
use App\Mail\ReservaComProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    public function verificarDisponibilidade(Request $request)
    {
        $request->validate([
            'sala_id' => 'required|exists:Salas,idSala',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);

        $salaId = $request->sala_id;
        $dataInicio = $request->data_inicio;
        $dataFim = $request->data_fim;

        $hoje = Carbon::today();
        $amanha = Carbon::tomorrow();
        $dataInicioFormatada = Carbon::parse($dataInicio);

        if ($dataInicioFormatada->lte($amanha)) {
            return redirect()->route('reserva.disponibilidade', ['id' => $salaId])
                ->with('error', 'Não é possível fazer reservas para hoje ou amanhã. Por favor, selecione datas futuras.');
        }

        if ($dataInicioFormatada->lte($hoje)) {
            return redirect()->route('reserva.disponibilidade', ['id' => $salaId])
                ->with('error', 'Não é possível fazer reservas para hoje ou amanhã. Por favor, selecione datas futuras.');
        }

        return view('reserva.redirect-form', [
            'route' => route('reserva.resultados'),
            'params' => [
                'sala_id' => $salaId,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ]
        ]);
    }

    public function disponibilidade($id)
    {
        $sala = Sala::where('ativo', 1)->findOrFail($id);
        return view('reserva.disponibilidade', [
            'sala' => $sala,
            'tituloPagina' => 'Verificar Disponibilidade'
        ]);
    }

    private function verificarDisponibilidadeDia($salaId, $data, $turnoId)
    {
        $dataObj = Carbon::parse($data);

        $reservas = DataReserva::where('data', $dataObj->format('Y-m-d'))
            ->whereHas('reserva', function ($query) use ($salaId) {
                $query->where('idSala', $salaId)
                    ->where(function ($q) {
                        $q->where('situacaoAprovada', '!=', 1);
                    });
            })
            ->get();

        if ($salaId == 3) {
            $reservasSalas12 = DataReserva::where('data', $dataObj->format('Y-m-d'))
                ->whereHas('reserva', function ($query) {
                    $query->whereIn('idSala', [1, 2])
                        ->where(function ($q) {
                            $q->where('situacaoAprovada', '!=', 1);
                        });
                })
                ->get();

            $reservas = $reservas->merge($reservasSalas12);
        } else if ($salaId == 1 || $salaId == 2) {
            $reservasAuditorio = DataReserva::where('data', $dataObj->format('Y-m-d'))
                ->whereHas('reserva', function ($query) {
                    $query->where('idSala', 3)
                        ->where(function ($q) {
                            $q->where('situacaoAprovada', '!=', 1);
                        });
                })
                ->get();

            $reservas = $reservas->merge($reservasAuditorio);
        }

        if ($turnoId == 1) {
            $disponivel = !$reservas->contains(function ($dataReserva) {
                return $dataReserva->manha == 1 || $dataReserva->diaTodo == 1;
            });
            return $disponivel;
        } else if ($turnoId == 2) {
            $disponivel = !$reservas->contains(function ($dataReserva) {
                return $dataReserva->tarde == 1 || $dataReserva->diaTodo == 1;
            });
            return $disponivel;
        } else if ($turnoId == 3) {
            $disponivel = !$reservas->contains(function ($dataReserva) {
                return $dataReserva->manha == 1 || $dataReserva->tarde == 1 || $dataReserva->diaTodo == 1;
            });
            return $disponivel;
        }

        return false;
    }

    public function mostrarResultados(Request $request)
    {
        $salaId = $request->input('sala_id');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if (!$salaId || !$dataInicio || !$dataFim) {
            return redirect()->route('home')->with('error', 'Parâmetros de consulta incompletos');
        }

        try {
            $sala = Sala::findOrFail($salaId);
            $turnos = Turno::where('ativo', 1)->get();

            $dataInicioObj = Carbon::parse($dataInicio);
            $dataFimObj = Carbon::parse($dataFim);
            $hoje = Carbon::today();
            $amanha = Carbon::tomorrow();

            if ($dataInicioObj->lte($amanha)) {
                return redirect()->route('reserva.disponibilidade', ['id' => $salaId])
                    ->with('error', 'Não é possível fazer reservas para hoje ou amanhã. Por favor, selecione datas futuras.');
            }

            if ($dataInicioObj->lte($hoje)) {
                return redirect()->route('reserva.disponibilidade', ['id' => $salaId])
                    ->with('error', 'Não é possível fazer reservas para hoje ou amanhã. Por favor, selecione datas futuras.');
            }

            $diasDisponiveis = [];

            for ($data = $dataInicioObj->copy(); $data->lte($dataFimObj); $data->addDay()) {
                if ($data->isWeekend()) continue;

                $dataFormatada = $data->format('Y-m-d');
                $turnosDisponiveis = [];

                foreach ($turnos as $turno) {
                    $turnosDisponiveis[] = [
                        'IDTurno' => $turno->idTurno,
                        'Descricao' => $turno->descricao,
                        'Horario' => $turno->horario,
                        'Disponivel' => $this->verificarDisponibilidadeDia($salaId, $dataFormatada, $turno->idTurno)
                    ];
                }

                $diasDisponiveis[$dataFormatada] = $turnosDisponiveis;
            }

            return view('reserva.resultados', [
                'sala' => $sala,
                'diasDisponiveis' => $diasDisponiveis,
                'dataInicio' => $dataInicio,
                'dataFim' => $dataFim,
                'turnosDisponiveis' => $turnos
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao verificar disponibilidade: ' . $e->getMessage());
            return redirect()->route('reserva.disponibilidade', $salaId)
                ->with('error', 'Ocorreu um erro ao processar sua solicitação: ' . $e->getMessage());
        }
    }

    public function agendar(Request $request)
    {
        Log::info('Parâmetros recebidos em agendar()', $request->all());

        $request->validate([
            'sala_id' => 'required|exists:Salas,idSala',
            'reservas' => 'required|array|min:1',
            'reservas.*' => 'string|in:1,2,3'
        ]);

        $sala = Sala::findOrFail($request->sala_id);
        $turnos = Turno::whereIn('idTurno', [1, 2, 3])->get()->keyBy('idTurno');

        $diasSelecionados = [];
        $valorTotal = 0;
        $totalTaxaLimpeza = 0;


        foreach ($request->reservas as $data => $turnoId) {
            if (!$this->verificarDisponibilidadeDia($sala->idSala, $data, $turnoId)) {
                return back()->with('error', 'Um ou mais horários selecionados não estão mais disponíveis.');
            }

            $diasSelecionados[] = [
                'data' => $data,
                'turno' => $turnos[$turnoId]
            ];

            $valorTotal += ($turnoId == 3) ? $sala->valorIntegral : $sala->valorMeioPeriodo;
            $valorTotal += $sala->taxaLimpeza;
            $totalTaxaLimpeza += $sala->taxaLimpeza;
        }

        return view('reserva.agendar', [
            'sala' => $sala,
            'diasSelecionados' => $diasSelecionados,
            'valorTotal' => $valorTotal,
            'valorIntegram' => $sala->valorIntegral,
            'valorMeioPeriodo' => $sala->valorMeioPeriodo,
            'totalTaxaLimpeza' => $totalTaxaLimpeza,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sala_id' => 'required|exists:Salas,idSala',
            'reservas' => 'required|array|min:1',
            'reservas.*' => 'string|in:1,2,3',
            'cpf' => 'required|string|max:14',
            'nome' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'telefone' => 'required|string|max:25',
            'PossuiProjeto' => 'required|boolean',
            'CodProjeto' => 'nullable|string|max:20|required_if:PossuiProjeto,1',
            'FuncFapeu' => 'required|boolean',
            'matricula' => 'nullable|string|max:50|required_if:FuncFapeu,1',
            'MotivoReserva' => 'required|string|max:500',
            'FormaPgto' => 'required|integer|between:1,4'
        ]);

        $validated['cpf'] = preg_replace('/[^0-9]/', '', $validated['cpf']);
        $validated['telefone'] = preg_replace('/[^0-9]/', '', $validated['telefone']);

        if ($validated['FuncFapeu']) {
            $validated['matricula'] = preg_replace('/[^0-9a-zA-Z]/', '', $validated['matricula']);
        } else {
            $validated['matricula'] = null;
        }

        if ($validated['PossuiProjeto']) {
            $validated['CodProjeto'] = preg_replace('/[^0-9a-zA-Z]/', '', $validated['CodProjeto']);
        } else {
            $validated['CodProjeto'] = null;
        }

        try {
            DB::beginTransaction();

            $sala = Sala::findOrFail($validated['sala_id']);
            $reservasCriadas = [];
            $valorTotal = 0;

            $datasReserva = array_keys($validated['reservas']);
            $dataInicial = min($datasReserva);
            $dataFinal = max($datasReserva);

            $hashCancelamento = Str::random(5);

            $reserva = Reserva::create([
                'idSala' => $validated['sala_id'],
                'dataReservaInicial' => $dataInicial,
                'dataReservaFinal' => $dataFinal,
                'cpf' => $validated['cpf'],
                'nome' => $validated['nome'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'],
                'funcFapeu' => $validated['FuncFapeu'],
                'matricula' => $validated['matricula'],
                'possuiProjeto' => $validated['PossuiProjeto'],
                'codProjeto' => $validated['CodProjeto'],
                'motivoReserva' => $validated['MotivoReserva'],
                'situacaoAprovada' => 0,
                'formaPgto' => $validated['FormaPgto'],
                'pago' => false,
                'situacaoPgto' => 0,
                'situacaoTermo' => 0,
                'envioEmailAviso' => false,
                'isentoLimpeza' => false,
                'solicitadoEm' => now(),
                'dataAnalise' => null,
                'idUsuario' =>  null,
                'idLancamento' => null,
                'observacao' => null,
                'justificativa' => null,
                'valorPago' => 0,
                'hash' => Str::random(32),
                'hashCancelamento' => $hashCancelamento
            ]);

            $datasReservaDetalhes = [];
            foreach ($validated['reservas'] as $data => $turnoId) {
                if (!$this->verificarDisponibilidadeDia($validated['sala_id'], $data, $turnoId)) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 'Um ou mais horários selecionados não estão mais disponíveis.');
                }

                $valor = ($turnoId == 3) ? $sala->valorIntegral : $sala->valorMeioPeriodo;
                $valor += $sala->taxaLimpeza;
                $valorTotal += $valor;

                $reserva->valor = $valorTotal;
                $reserva->save();

                $dataReserva = DataReserva::create([
                    'data' => $data,
                    'manha' => $turnoId == 1 || $turnoId == 3 ? 1 : 0,
                    'tarde' => $turnoId == 2 || $turnoId == 3 ? 1 : 0,
                    'diaTodo' => $turnoId == 3 ? 1 : 0,
                    'idReserva' => $reserva->idReserva
                ]);

                $datasReservaDetalhes[] = $dataReserva;
            }

            Log::info('Reserva criada com sucesso: ' . $reserva->idReserva);

            Mail::to($reserva->email)
                ->send(new ReservaConfirmada($reserva));

            if ($validated['PossuiProjeto']) {
                $reserva->load('sala', 'datasReserva');

                Mail::to('gabriel.lima@fapeu.org.br')
                    ->send(new ReservaComProjeto($reserva));

                Log::info('Email de notificação de projeto enviado: ' . $reserva->idReserva);
            }

            $reservasCriadas[] = $reserva->idReserva;

            DB::commit();

            return redirect()->route('reserva.confirmacao')
                ->with([
                    'success' => count($datasReservaDetalhes) . ' reserva(s) realizada(s) com sucesso!',
                    'reservas_ids' => $reservasCriadas,
                    'valor_total' => 'Valor Total: R$ ' . number_format($valorTotal, 2, ',', '.')
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar reserva: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao processar sua reserva. Por favor, tente novamente.');
        }
    }


    public function consulta(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'cpf' => 'required|string',
                'id_reserva' => 'required|numeric'
            ]);

            $cpf = preg_replace('/[^0-9]/', '', $request->cpf);

            $reserva = Reserva::with(['sala', 'datasReserva', 'turno'])
                ->where('idReserva', $request->id_reserva)
                ->where('cpf', $cpf)
                ->first();

            if (!$reserva) {
                return redirect()->route('reserva.consulta')
                    ->with('error', 'Reserva não encontrada. Verifique os dados informados.');
            }

            $success = $request->success ?? session('success');

            return view('reserva.consulta', [
                'reserva' => $reserva,
                'success' => $success
            ]);
        }

        return view('reserva.consulta');
    }


    public function cancelarDia(Request $request)
    {
        $request->validate([
            'data_reserva_id' => 'required|exists:DataReserva,id',
            'reserva_id' => 'required|exists:Reserva,idReserva',
            'cpf' => 'required',
            'hashCancelamento' => 'required|string|size:5'
        ]);

        try {
            $reserva = Reserva::where('idReserva', $request->reserva_id)
                ->where('cpf', $request->cpf)
                ->first();

            if (!$reserva) {
                return back()->with('error', 'Reserva não encontrada.');
            }

            if ($reserva->hashCancelamento !== $request->hashCancelamento) {
                Log::warning('Tentativa de cancelamento com hash inválido para reserva: ' . $reserva->idReserva);
                return back()->with('error', 'Código de cancelamento inválido. Por favor, verifique o código enviado no email de confirmação da reserva.');
            }

            $dataReserva = DataReserva::findOrFail($request->data_reserva_id);

            if ($dataReserva->idReserva != $reserva->idReserva) {
                return back()->with('error', 'Esta data não pertence à reserva informada.');
            }

            $agora = Carbon::now('America/Sao_Paulo');
            $dataReservada = Carbon::parse($dataReserva->data)->setTimezone('America/Sao_Paulo');

            if ($dataReserva->diaTodo == 1 || $dataReserva->manha == 1) {
                $horarioLimite = $dataReservada->copy()->setTime(8, 0, 0);
            } else {
                $horarioLimite = $dataReservada->copy()->setTime(13, 0, 0);
            }

            $prazoFinalCancelamento = $horarioLimite->copy()->subHours(48);

            if ($agora->greaterThan($prazoFinalCancelamento)) {
                return back()->with('error', 'O cancelamento só pode ser realizado com pelo menos 48 horas de antecedência do horário da reserva (horário de Brasília).');
            }

            $sala = Sala::findOrFail($reserva->idSala);

            $valorDiario = 0;
            if ($dataReserva->diaTodo == 1) {
                $valorDiario = $sala->valorIntegral;
            } else {
                $valorDiario = $sala->valorMeioPeriodo;
            }

            $valorDiario += $sala->taxaLimpeza;

            DB::beginTransaction();

            $reserva->valor -= $valorDiario;
            $reserva->save();

            $dataReserva->delete();

            $datasRestantes = DataReserva::where('idReserva', $reserva->idReserva)
                ->where('id', '!=', $dataReserva->id)
                ->count();

            if ($datasRestantes == 0) {
                $reserva->delete();
                DB::commit();
                return redirect()->route('reserva.consulta')
                    ->with('success', 'Reserva cancelada com sucesso!');
            }

            $datasAtualizadas = DataReserva::where('idReserva', $reserva->idReserva)
                ->orderBy('data', 'asc')
                ->get();

            if ($datasAtualizadas->count() > 0) {
                $reserva->dataReservaInicial = $datasAtualizadas->first()->data;
                $reserva->dataReservaFinal = $datasAtualizadas->last()->data;
                $reserva->save();
            }

            DB::commit();

            $reservaAtualizada = Reserva::with(['sala', 'datasReserva', 'turno'])
                ->where('idReserva', $reserva->idReserva)
                ->first();

            if (!$reservaAtualizada) {
                return redirect()->route('reserva.consulta')
                    ->with('success', 'Dia cancelado com sucesso!');
            }

            return view('reserva.consulta', [
                'reserva' => $reservaAtualizada,
                'success' => 'Dia cancelado com sucesso! O valor da reserva foi atualizado.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao cancelar reserva: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao cancelar o dia de reserva. Por favor, tente novamente.');
        }
    }

    public function gerarPix($id)
    {
        try {
            $reserva = Reserva::with(['sala', 'datasReserva'])
                ->where('idReserva', $id)
                ->where('situacaoAprovada', 1)
                ->firstOrFail();

            if ($reserva->pago) {
                return redirect()->route('reserva.consulta')
                    ->with([
                        'error' => 'Esta reserva já foi paga.'
                    ]);
            }

            // Dados para integração com GetNet (estes são dados de exemplo)
            $getnetConfig = [
                'seller_id' => env('GETNET_SELLER_ID'),
                'client_id' => env('GETNET_CLIENT_ID'),
                'client_secret' => env('GETNET_CLIENT_SECRET'),
                'environment' => env('GETNET_ENVIRONMENT', 'sandbox'),
                'debug' => env('GETNET_DEBUG', false),
            ];


            try {
                // integração real com a GetNet
                // Código exemplo:
                /*
            $client = new GetnetClient($getnetConfig);
            $qrCodeData = $client->generatePixQrCode([
                'amount' => $reserva->valor,
                'order_id' => $reserva->idReserva,
                'customer' => [
                    'name' => $reserva->nome,
                    'document' => $reserva->cpf,
                    'email' => $reserva->email
                ]
            ]);
            */

                // simulando dados retornados pela GetNet
                $qrCodeData = [
                    'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=',
                    'qr_code_text' => '00020101021226890014br.gov.bcb.pix2567pix.example.com/v2/123456789abcdef5204000053039865802BR5924FUNDACAO AMPARO PESQUISA6009SAO PAULO62070503***630400B4',
                    'expiration_date' => Carbon::now()->addDays(1)->toIso8601String()
                ];

                return view('pagamento.pix', [
                    'reserva' => $reserva,
                    'qrCodeData' => $qrCodeData
                ]);
            } catch (\Exception $e) {
                Log::error('Erro na integração com GetNet: ' . $e->getMessage());
                return redirect()->route('reserva.consulta')
                    ->with([
                        'error' => 'Ocorreu um erro ao gerar o PIX. Por favor, entre em contato com o suporte.'
                    ]);
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->route('reserva.consulta')
                ->with([
                    'error' => 'Reserva não encontrada ou não está aprovada.'
                ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PIX: ' . $e->getMessage());
            return redirect()->route('reserva.consulta')
                ->with([
                    'error' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.'
                ]);
        }
    }
}
