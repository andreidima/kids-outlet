<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AngajatController;
use App\Http\Controllers\InregistrareComandaController;

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

Auth::routes(['register' => false, 'password.request' => false, 'reset' => false]);

Route::get('/', function () {
    return view('acasa');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Rute pentru comanda inregistrata de guest
Route::any('/adauga-comanda-noua', [InregistrareComandaController::class, 'adaugaComandaNoua'])->name('adauga-comanda-noua');
Route::get('/adauga-comanda-pasul-1', [InregistrareComandaController::class, 'adaugaComandaPasul1']);
Route::post('/adauga-comanda-pasul-1', [InregistrareComandaController::class, 'postadaugaComandaPasul1']);
Route::get('/adauga-comanda-pasul-2', [InregistrareComandaController::class, 'adaugaComandaPasul2']);
Route::post('/adauga-comanda-pasul-2', [InregistrareComandaController::class, 'postAdaugaComandaPasul2']);
Route::get('/adauga-comanda-pasul-3', [InregistrareComandaController::class, 'adaugaComandaPasul3']);
Route::post('/adauga-comanda-pasul-3', [InregistrareComandaController::class, 'postAdaugaComandaPasul3']);
Route::get('/adauga-comanda-pasul-4', [InregistrareComandaController::class, 'adaugaComandaPasul4']);

Route::group(['middleware' => 'auth'], function () {
    Route::resource('angajati', AngajatController::class,  ['parameters' => ['angajati' => 'angajat']]);
});

