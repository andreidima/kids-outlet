<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AngajatAplicatieController;
use App\Http\Controllers\AngajatController;
use App\Http\Controllers\PontajController;
use App\Http\Controllers\ProdusController;
use App\Http\Controllers\ProdusOperatieController;
use App\Http\Controllers\NormaLucrataController;
use App\Http\Controllers\ImportFisierExcelController;
use App\Http\Controllers\InserareDateDeTestController;

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

    // Route::get('/', function () {
    //     return view('first_page');
    // });

Route::redirect('/', '/aplicatie-angajati');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Rute pentru aplicatie angajati
Route::get('/aplicatie-angajati', [AngajatAplicatieController::class, 'autentificare'])->name('autentificare');
Route::post('/aplicatie-angajati', [AngajatAplicatieController::class, 'postAutentificare']);

Route::get('/aplicatie-angajati/deconectare', [AngajatAplicatieController::class, 'deconectare'])->name('deconectare');

Route::get('/aplicatie-angajati/meniul-principal', [AngajatAplicatieController::class, 'meniulPrincipal']);

Route::get('/aplicatie-angajati/adauga-comanda-pasul-1', [AngajatAplicatieController::class, 'adaugaComandaPasul1']);
Route::post('/aplicatie-angajati/adauga-comanda-pasul-1', [AngajatAplicatieController::class, 'postadaugaComandaPasul1']);
Route::get('/aplicatie-angajati/adauga-comanda-pasul-2', [AngajatAplicatieController::class, 'adaugaComandaPasul2']);
Route::post('/aplicatie-angajati/adauga-comanda-pasul-2', [AngajatAplicatieController::class, 'postadaugaComandaPasul2']);
Route::get('/aplicatie-angajati/adauga-comanda-pasul-3', [AngajatAplicatieController::class, 'adaugaComandaPasul3']);
Route::post('/aplicatie-angajati/adauga-comanda-pasul-3', [AngajatAplicatieController::class, 'postAdaugaComandaPasul3']);
Route::get('/aplicatie-angajati/adauga-comanda-pasul-4', [AngajatAplicatieController::class, 'adaugaComandaPasul4']);

// Route::get('/aplicatie-angajati/pontaj/{moment?}', [AngajatAplicatieController::class, 'pontaj']);
Route::get('/aplicatie-angajati/pontaj', [AngajatAplicatieController::class, 'pontajPontator']);
Route::post('/aplicatie-angajati/pontaj', [AngajatAplicatieController::class, 'postPontajPontator']);

Route::get('/aplicatie-angajati/realizat', [AngajatAplicatieController::class, 'realizat'])->name('aplicatie_angajati.realizat');;



Route::group(['middleware' => 'auth'], function () {
    Route::get('/acasa', function () {
        return view('acasa');
    });

    Route::resource('angajati', AngajatController::class,  ['parameters' => ['angajati' => 'angajat']]);

    Route::get('pontaje/afisare-lunar', [PontajController::class, 'afisareLunar'],  ['parameters' => ['pontaje' => 'pontaj']])->name('pontaje.afisare_lunar');
    Route::resource('pontaje', PontajController::class,  ['parameters' => ['pontaje' => 'pontaj']]);

    Route::resource('produse', ProdusController::class,  ['parameters' => ['produse' => 'produs']]);

    Route::resource('produse-operatii', ProdusOperatieController::class,  ['parameters' => ['produse-operatii' => 'produs_operatie']]);

    Route::get('norme-lucrate/afisare-lunar', [NormaLucrataController::class, 'afisareLunar'])->name('norme-lucrate.afisare_lunar');
    Route::resource('norme-lucrate', NormaLucrataController::class,  ['parameters' => ['norme-lucrate' => 'norma_lucrata']]);

    // Route::get('/import/import-produse-operatii', [ImportFisierExcelController::class, 'importProduseOperatii']);
    // Route::get('/import/import-produse-operatii/setare-norme-infinit', [ImportFisierExcelController::class, 'importProduseOperatiiSetareNormeInfinit']);

    Route::get('/inserare-pontaje-de-test', [InserareDateDeTestController::class, 'inserarePontaje']);
    Route::get('/inserare-comenzi-de-test', [InserareDateDeTestController::class, 'inserareComenzi']);
});

