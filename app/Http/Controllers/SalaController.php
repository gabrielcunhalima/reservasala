<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Sala;
use Illuminate\Http\Request;

class SalaController extends Controller
{
    public function show($id)
    {
        $sala = Sala::where('ativo', 1)->findOrFail($id);
        return view('sala.show', [
            'sala' => $sala,
            'idSala' => $sala->idSala,
            'tituloPagina' => $sala->nomeSala
        ]);
    }
}