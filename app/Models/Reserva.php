<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'Reserva';
    protected $primaryKey = 'idReserva';
    public $timestamps = false;

    protected $fillable = [
        'idSala',
        'dataReserva',
        'turno',
        'cpf',
        'nome',
        'matricula',
        'email',
        'telefone',
        'funcFapeu',
        'possuiProjeto',
        'codProjeto',
        'motivoReserva',
        'situacaoAprovada',
        'dataAnalise',
        'idUsuario',
        'valor',
        'pago',
        'idLancamento',
        'solicitadoEm',
        'situacaoTermo',
        'situacaoPgto',
        'formaPgto',
        'envioEmailAviso',
        'hora',
        'observacao',
        'justificativa',
        'valorPago',
        'isentoLimpeza',
        'dataReservaInicial',
        'dataReservaFinal',
        'hashCancelamento'
    ];

    // uma reserva pertence a uma sala
    public function sala()
    {
        return $this->belongsTo(Sala::class, 'idSala', 'idSala');
    }

    // reserva pode ter muitas datas
    public function datasReserva()
    {
        return $this->hasMany(DataReserva::class, 'idReserva', 'idReserva');
    }
    
    // converter o campo dataReserva para Carbon
    public function getDataReservaAttribute()
    {
        if ($this->dataReservaInicial) {
            return \Carbon\Carbon::parse($this->dataReservaInicial);
        }
        return null;
    }
    
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno', 'idTurno');
    }
}