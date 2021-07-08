<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AngajatController;
use App\Http\Controllers\AngajatAplicatieController;

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


// Rute pentru aplicatie angajati
Route::get('/autentificare', [AngajatAplicatieController::class, 'autentificare'])->name('autentificare');
Route::post('/autentificare', [AngajatAplicatieController::class, 'autentificare'])->name('post_autentificare');

Route::get('/adauga-comanda-pasul-1', [AngajatAplicatieController::class, 'adaugaComandaPasul1']);

Route::any('/adauga-comanda-noua', [AngajatAplicatieController::class, 'adaugaComandaNoua'])->name('adauga-comanda-noua');
Route::get('/adauga-comanda-pasul-1', [AngajatAplicatieController::class, 'adaugaComandaPasul1']);
Route::post('/adauga-comanda-pasul-1', [AngajatAplicatieController::class, 'postadaugaComandaPasul1']);
Route::get('/adauga-comanda-pasul-2', [AngajatAplicatieController::class, 'adaugaComandaPasul2']);
Route::post('/adauga-comanda-pasul-2', [AngajatAplicatieController::class, 'postAdaugaComandaPasul2']);
Route::get('/adauga-comanda-pasul-3', [AngajatAplicatieController::class, 'adaugaComandaPasul3']);
Route::post('/adauga-comanda-pasul-3', [AngajatAplicatieController::class, 'postAdaugaComandaPasul3']);
Route::get('/adauga-comanda-pasul-4', [AngajatAplicatieController::class, 'adaugaComandaPasul4']);

Route::group(['middleware' => 'auth'], function () {
    Route::resource('angajati', AngajatController::class,  ['parameters' => ['angajati' => 'angajat']]);
});

