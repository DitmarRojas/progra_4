<?php

use App\Http\Middleware\VerificarUsuarioNoBloqueado;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Cuentas;
use App\Livewire\Usuarios;
use App\Livewire\Organizaciones;
use App\Livewire\Periodos;
use App\Livewire\Transacciones;
use App\Livewire\AsientosDiarios;
use App\Livewire\SumasYSaldos;

/* Route::get('/', function () {
    return view('welcome');
})->name('home'); */
Route::get('/', Login::class)
    ->middleware('guest')
    ->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth' , VerificarUsuarioNoBloqueado::class])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('/usuarios', Usuarios::class)->name('users');
    Route::get('/organizaciones', Organizaciones::class)->name('organizaciones');
    Route::get('/cuentas', Cuentas::class)->name('cuentas');
    Route::get('/periodos', Periodos::class)->name('periodos');
    Route::get('/transacciones', Transacciones::class)->name('transacciones');
    Route::get('/asientos_diarios', AsientosDiarios::class)->name('asientos_diarios');
    Route::get('/sumas-saldos', SumasYSaldos::class)->name('sumas-saldos');
});

require __DIR__.'/auth.php';
