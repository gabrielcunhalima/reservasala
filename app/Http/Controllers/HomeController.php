<?php

namespace App\Http\Controllers;

use App\Models\Sala;

class HomeController extends Controller
{
    public function index()
    {
        $salas = Sala::where('ativo', 1)->orderBy('idSala','asc')->get(); 
        return view('home.index', [
            'salas' => $salas,
            'tituloPagina' => 'Reserva de Salas'
        ]);
    }
}