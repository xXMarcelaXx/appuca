<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth','ips'])->group(function () {
    Route::get('/index', [LoginController::class, 'index'])->name('index');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    //  RUTAS CRUD CURSOS Y CLIENTES  
    //todos    
    Route::get('verCursos', [CursoController::class, 'verCursos'])->name('verCursos');
    //admin y coordinador
    Route::post('postCursos', [CursoController::class, 'post'])->name('postCursos')->middleware('coordinador_admin');
    Route::get('curso/{id}', [CursoController::class, 'show'])->name('verCursos')->middleware('coordinador_admin');
    Route::put('putCursos/{id}', [CursoController::class, 'put'])->name('putCursos')->middleware('coordinador_admin');
    Route::delete('deleteCursos/{id}', [CursoController::class, 'delete'])->name('deleteCursos')->middleware('coordinador_admin');

    //todos
    Route::get('ver/{id}', [CursoController::class, 'show2'])->name('ver');

    //admin y coordinador
    Route::delete('deleteClienteCurso/{id}', [CursoController::class, 'deleteClienteCurso'])->name('deleteClienteCurso')->middleware('coordinador_admin');
    Route::post('postClienteCurso', [CursoController::class, 'postClienteCurso'])->name('postClienteCurso')->middleware('coordinador_admin');

    //todos
    Route::get('clientes', [ClienteController::class, 'clientes'])->name('clientes');

    //admin y coordinador
    Route::post('postClientes', [ClienteController::class, 'post'])->name('postClientes')->middleware('coordinador_admin');
    Route::delete('deleteCliente/{id}', [ClienteController::class, 'delete'])->name('deleteCliente')->middleware('coordinador_admin');
    Route::get('cliente/{id}', [ClienteController::class, 'show'])->name('verCliente')->middleware('coordinador_admin');
    Route::put('putCliente/{id}', [ClienteController::class, 'put'])->name('putCliente')->middleware('coordinador_admin');



    //Rutas gestion de usuarios solo admin 
    //admin
    Route::get('users', [UserController::class, 'users'])->name('users')->middleware('admin');
    Route::delete('deleteUser/{id}', [UserController::class, 'delete'])->name('deleteUser')->middleware('admin');
    Route::get('user/{id}', [UserController::class, 'show'])->name('showUser')->middleware('admin');
    Route::put('putUser/{id}', [UserController::class, 'put'])->name('putUser')->middleware('admin');
});


Route::get('/registro', [LoginController::class, 'registro']);
Route::get('/iniciarSesion', [LoginController::class, 'iniciarSesion'])->name('iniciarSesion');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('mandarSMS/{id}', [LoginController::class, 'mandarSMS'])->name('mandarSMS');
Route::get('mandarCorreo{id}', [LoginController::class, 'mandarCorreo'])->name('mandarCorreo');
Route::post('/validar-codigo{id}', [LoginController::class, 'validarCodigo'])->name('validar-codigo');
Route::post('/registrar-usuario', [LoginController::class, 'registrarUsuario'])->name('registrar-usuario');
Route::get('/formulario', [LoginController::class, 'formulario'])->name('formulario');
Route::post('/guardar', [LoginController::class, 'guardar'])->name('guardar');
Route::get('/prueba', [LoginController::class, 'prueba'])->name('prueba');