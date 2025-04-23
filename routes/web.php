<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\ReservaController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sala/{id}', [SalaController::class, 'show'])->name('sala.show');

Route::prefix('reservas')->group(function () {
     Route::get('/disponibilidade/{id}', [ReservaController::class, 'disponibilidade'])
          ->name('reserva.disponibilidade');

     Route::post('/verificar', [ReservaController::class, 'verificarDisponibilidade'])
          ->name('reserva.verificar');

     Route::post('/disponiveis', [ReservaController::class, 'mostrarResultados'])
          ->name('reserva.resultados');

     Route::post('/agendar', [ReservaController::class, 'agendar'])
          ->name('reserva.agendar');

     Route::any('/store', [ReservaController::class, 'store'])
          ->name('reserva.store');

     Route::match(['get', 'post'], '/consulta', [ReservaController::class, 'consulta'])
          ->name('reserva.consulta');
          
     Route::post('/cancelar-dia', [ReservaController::class, 'cancelarDia'])
          ->name('reserva.cancelar.dia');

     Route::get('/confirmacao', function () {
          return view('reserva.confirmacao');
     })->name('reserva.confirmacao');
});