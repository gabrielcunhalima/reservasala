<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'Turnos';
    protected $primaryKey = 'idTurno';
    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'horario',
        'ativo',
    ];

    public function scopeAtivos($query)
    {
        return $query->where('ativo', 1);
    }
}