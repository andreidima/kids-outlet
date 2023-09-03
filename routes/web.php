<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AngajatAplicatieController;
use App\Http\Controllers\AngajatAplicatieAngajatController;
use App\Http\Controllers\AngajatAplicatieProdusController;

use App\Http\Controllers\AngajatController;
use App\Http\Controllers\PontajController;
use App\Http\Controllers\ProdusController;
use App\Http\Controllers\ProdusOperatieController;
use App\Http\Controllers\NormaLucrataController;
use App\Http\Controllers\ImportFisierExcelController;
use App\Http\Controllers\InserareDateDeTestController;
use App\Http\Controllers\InserareAngajatiController;
use App\Http\Controllers\AvansController;
use App\Http\Controllers\LichidareController;
use App\Http\Controllers\SalariuController;

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
Route::any('/aplicatie-angajati/pontaj/{moment}/ponteaza-toti', [AngajatAplicatieController::class, 'pontajPonteazaToti']);
Route::get('/aplicatie-angajati/pontaj/{angajat_de_pontat}/modifica', [AngajatAplicatieController::class, 'pontajModificaPontator']);
Route::get('/aplicatie-angajati/pontaj-verifica', [AngajatAplicatieController::class, 'pontajVerificaPontator']);

Route::get('/aplicatie-angajati/realizat', [AngajatAplicatieController::class, 'realizat'])->name('aplicatie_angajati.realizat');
Route::get('/aplicatie-angajati/norma-lucrata/{norma_lucrata}/sterge', [AngajatAplicatieController::class, 'stergeNormaLucrata']);

Route::get('/aplicatie-angajati/vezi-faze-produse/{produs?}', [AngajatAplicatieController::class, 'veziFazeProduse']);
Route::get('/aplicatie-angajati/vezi-norme/{produs_operatie}', [AngajatAplicatieController::class, 'veziNormeProdusOperatie']);

Route::resource('/aplicatie-angajati/angajati', AngajatAplicatieAngajatController::class,  ['parameters' => ['angajati' => 'angajat']]);
Route::get('/aplicatie-angajati/angajati-acces-faze/{angajat}', [AngajatAplicatieAngajatController::class, 'accesFaze']);
Route::post('/aplicatie-angajati/angajati-acces-faze/{angajat}', [AngajatAplicatieAngajatController::class, 'accesFazePost']);

// Gestionarea fazelor la care au acces angajatii
Route::get('/aplicatie-angajati/produs-faze-angajati', [AngajatAplicatieController::class, 'produsFazeAngajat']);
Route::post('/aplicatie-angajati/produs-faze-angajati', [AngajatAplicatieController::class, 'postProdusFazeAngajat']);
Route::any('/aplicatie-angajati/produs-faze-angajati/axios', [AngajatAplicatieController::class, 'axiosStergeProdusFazeAngajat']);

// Route::get('/aplicatie-angajati/blocheaza-deblocheaza-introducere-comenzi', [AngajatAplicatieController::class, 'blocheazaDeblocheazaIntroducereComenzi']);

Route::resource('/aplicatie-angajati/produse', AngajatAplicatieProdusController::class, ['parameters' => ['produse' => 'produs']]);

Route::any('/aplicatie-angajati/muta-lucrul-pe-luna-anterioara', [AngajatAplicatieController::class, 'mutaLucrulPeLunaAnterioara']);


// Seful de sectie poate sterge, pana in data de 15, si norme de pe una anterioara
Route::get('/aplicatie-angajati/cont-sef-sectie/norma-lucrata/{norma_lucrata}/sterge', [AngajatAplicatieController::class, 'stergeNormaLucrataDinContSefSectie']);


Route::group(['middleware' => 'auth'], function () {
    Route::get('/acasa', function () {
        return view('acasa');
    });

    Route::resource('angajati', AngajatController::class,  ['parameters' => ['angajati' => 'angajat']]);

    Route::get('pontaje/afisare-lunar', [PontajController::class, 'afisareLunar'],  ['parameters' => ['pontaje' => 'pontaj']])->name('pontaje.afisare_lunar');
    Route::get('pontaje/{angajat}/{data}/adauga', [PontajController::class, 'create']);
    Route::resource('pontaje', PontajController::class,  ['parameters' => ['pontaje' => 'pontaj']]);

    Route::post('produse/{produs}/duplica', [ProdusController::class, 'duplica']);
    Route::resource('produse', ProdusController::class,  ['parameters' => ['produse' => 'produs']]);

    Route::resource('produse-operatii', ProdusOperatieController::class,  ['parameters' => ['produse-operatii' => 'produs_operatie']]);

    Route::get('norme-lucrate/afisare-lunar', [NormaLucrataController::class, 'afisareLunar'])->name('norme-lucrate.afisare_lunar');
    Route::get('norme-lucrate/per-angajat-per-data/{angajat}/{data}', [NormaLucrataController::class, 'index']);
    Route::get('norme-lucrate/adauga/per-angajat-per-data/{angajat?}/{data?}', [NormaLucrataController::class, 'create']);
    Route::any('/norme-lucrate/muta-lucrul-pe-luna-anterioara', [NormaLucrataController::class, 'mutaLucrulPeLunaAnterioara']);
    Route::resource('norme-lucrate', NormaLucrataController::class,  ['parameters' => ['norme-lucrate' => 'norma_lucrata']]);

    // Route::get('/import/import-produse-operatii', [ImportFisierExcelController::class, 'importProduseOperatii']);
    // Route::get('/import/import-produse-operatii/setare-norme', [ImportFisierExcelController::class, 'importProduseOperatiiSetareNorme']);

    // Route::get('inserare-angajati', [InserareAngajatiController::class, 'inserareAngajati']);
    // Route::get('inserare-angajati-pontatori', [InserareAngajatiController::class, 'inserareAngajatiPontatori']);

    // Route::get('/inserare-pontaje-de-test', [InserareDateDeTestController::class, 'inserarePontaje']);
    // Route::get('/inserare-comenzi-de-test', [InserareDateDeTestController::class, 'inserareComenzi']);

    // Update faze produse. Se face update din tabelul sheet1, ce contine toate fazele iar in ultima coloana contine id-ul produsului
    // Route::any('/import/update-faze-produse/{update?}', [ImportFisierExcelController::class, 'getUpdateFazeProduse']);

    Route::get('/avansuri', [AvansController::class, 'index']);
    Route::post('/avansuri/axios-actualizare-suma', [AvansController::class, 'axiosActualizareSuma']);

    Route::get('/lichidare', [LichidareController::class, 'index']);

    Route::get('/salarii', [SalariuController::class, 'index']);
    Route::post('/salarii/axios-actualizare-valoare', [SalariuController::class, 'axiosActualizareValoare']);

    // Actualizarea automata a avansurilor din salarii, in luna 09.2023. Se poate sterge codul in luna 11.2023
    Route::get('actualizarea-avansurilor-din-salarii' , function(){
        $avansuriAugust = App\Models\Avans::where('data', '2023-08-01')->get();
        foreach ($avansuriAugust as $avans){
            $salariu = App\Models\Salariu::where('data', '2023-08-01')->where('angajat_id', $avans->angajat_id)->first();
            if ($salariu){
                $salariu->avans = $avans->suma;
                $salariu->save();
            }
        }
        echo 'Done';
    });

});
