<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'Salas';
    protected $primaryKey = 'idSala';
    public $timestamps = false;

    protected $fillable = [
        'nomeSala',
        'capacidade',
        'descricao',
        'localizacao',
        'imagem',
        'ativo',
        'valorMeioPeriodo',
        'valorIntegral',
        'taxaLimpeza'
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'idSala', 'idSala');
    }
}