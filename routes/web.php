<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AdminController;

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

Route::prefix('admin')->group(function () {

     Route::get('/login', [AdminController::class, 'showLogin'])
          ->name('admin.login');

     Route::post('/login', [AdminController::class, 'login'])
          ->name('admin.login.submit');

     Route::get('/dashboard', [AdminController::class, 'dashboard'])
          ->name('admin.dashboard');

     Route::get('/logout', [AdminController::class, 'logout'])
          ->name('admin.logout');

     Route::get('/reservas/visualizar/{id}', [AdminController::class, 'visualizarReserva'])
          ->name('admin.reservas.visualizar');

     Route::post('/reservas/aprovar/{id}', [AdminController::class, 'aprovarReserva'])
          ->name('admin.reservas.aprovar');

     Route::post('/reservas/rejeitar/{id}', [AdminController::class, 'rejeitarReserva'])
          ->name('admin.reservas.rejeitar');

     Route::get('/reservas/pendentes', [AdminController::class, 'reservasPendentes'])
          ->name('admin.reservas.pendentes');

     Route::get('/reservas/aprovadas', [AdminController::class, 'reservasAprovadas'])
          ->name('admin.reservas.aprovadas');

     Route::get('/reservas/rejeitadas', [AdminController::class, 'reservasRejeitadas'])
          ->name('admin.reservas.rejeitadas');
});

Route::get('/pagamento/pix/{id}', [ReservaController::class, 'gerarPix'])
     ->name('pagamento.pix');
