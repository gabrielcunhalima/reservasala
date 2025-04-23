<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataReserva extends Model
{
    use HasFactory;

    protected $table = 'DataReserva';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'data',
        'manha',
        'tarde',
        'diaTodo',
        'idReserva',
        'Status'
    ];

    /**
     * Define o relacionamento com o modelo Reserva.
     * Uma data de reserva pertence a uma reserva.
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'idReserva', 'idReserva');
    }
}