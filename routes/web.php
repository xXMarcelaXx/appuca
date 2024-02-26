<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/index/{id}', [LoginController::class, 'index'])->name('index');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/registro', [LoginController::class, 'registro']);
Route::get('/iniciarSesion', [LoginController::class, 'iniciarSesion'])->name('iniciarSesion');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('mandarSMS/{id}',[LoginController::class, 'mandarSMS'])->name('mandarSMS');
Route::post('/validar-codigo',[LoginController::class, 'validarCodigo'])->name('validar-codigo');

Route::post('/registrar-usuario', [LoginController::class, 'registrarUsuario'])->name('registrar-usuario');


Route::get('/formulario', [LoginController::class, 'formulario'])->name('formulario');
Route::post('/guardar', [LoginController::class, 'guardar'])->name('guardar');

Route::get('/prueba', [LoginController::class, 'prueba'])->name('prueba');
