@extends ('layouts.app')

<script type="application/javascript">
    angajati =  {!! json_encode($angajati) !!}
    produse =  {!! json_encode($produse) !!}

    salariulMinimPeEconomie =  {!! json_encode($salariulMinimPeEconomie) !!}
    numarDeZileLucratoare =  {!! json_encode($numarDeZileLucratoare) !!}
</script>

<style>
table.table-bordered{
    border:1px solid rgb(0, 0, 0);
    margin-top:20px;
  }
table.table-bordered > thead > tr > th{
    border:1px solid rgb(0, 0, 0);
}
table.table-bordered > tbody > tr > td{
    border:1px solid rgb(0, 0, 0);
}
table, th, td {
  border: 1px solid black;
  font-size: 14px;
  padding: 0px;
}
</style>

@section('content')
<div class="card mx-1" style="border-radius: 40px 40px 40px 40px;">
        <form class="needs-validation mb-0" novalidate method="GET" action="{{ url()->current() }}">
            @csrf
            <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
                <div class="col-lg-1">
                    <h4 class="mb-0">Salarii</a></h4>
                </div>
                <div class="col-lg-4" id="app1">
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <div class="col-lg-5 d-flex justify-content-center">
                            <label for="searchLuna" class="mb-0 align-self-center me-1">Luna:</label>
                            <input type="text" class="form-control form-control border rounded-3" id="searchLuna" name="searchLuna" placeholder="Luna" autofocus
                                    value="{{ $searchLuna }}">
                        </div>
                        <div class="col-lg-5 d-flex justify-content-center">
                            <label for="searchAn" class="mb-0 align-self-center me-1">An:</label>
                            <input type="text" class="form-control form-control me-1 border rounded-3" id="searchAn" name="searchAn" placeholder="An" autofocus
                                    value="{{ $searchAn }}">
                        </div>
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ url()->current() }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 py-1 rounded-3" style="background-color: rgb(157, 249, 249)">
                    <div class="mb-2 d-flex align-items-center justify-content-center">
                        <span class="rounded-3 text-white px-2" style="background-color: darkcyan;">
                            AVANSURI
                        </span>
                    </div>
                    <div class="mb-2 d-flex align-items-center justify-content-center">
                        <a
                            class="btn btn-sm btn-danger text-white me-1 border border-dark rounded-pill"
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#calculeazaAutomatAvansurile"
                            title="Calculează automat avansurile"
                            >
                            Calculează automat avansurile
                        </a>
                        <div class="modal fade text-dark" id="calculeazaAutomatAvansurile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white" id="exampleModalLabel">Calculare automată a avansurilor</h5>
                                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align:left;">
                                    Ești sigur ca vrei să se calculeze automat toate avansurile pentru luna aleasă?
                                    <h4 class="text-center">{{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}</h4>
                                    Toate avansurile introduse manual se vor șterge!
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                                        <button class="btn btn-danger text-white border border-dark" type="submit"
                                            name="action" value="calculeazaAutomatAvansurile">
                                            Calculează automat avansurile
                                        </button>

                                </div>
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-question-circle fa-lg text-info" title="Avansurile se calculeaza astfel: zilePontate > 10 - avansul se plătește integral, zilePontate între 7 și 10 - avansul se plătește 300, zilePontate < 7 - avansul se plătește 0"></i>
                    </div>
                    <div class="d-grid gap-2 d-flex align-items-center justify-content-center">
                        <div class="px-2 py-0 align-items-center rounded-pill text-dark" style="background-color:rgb(193, 255, 226)">
                            <div class="mb-1 d-flex justify-content-center align-items-center">
                                Export: &nbsp;
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportAvansuriExcelToate">
                                    Excel Toate
                                </button>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportAvansuriExcelBancaBt">
                                    Excel BT
                                </button>
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportAvansuriTxtBancaIng">
                                    Txt ING
                                </button>
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportAvansuriExcelMana">
                                    Excel Mână
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1">
                </div>
                <div class="col-lg-3 py-1 rounded-3" style="background-color: rgb(255, 219, 219);">
                    <div class="mb-2 d-flex align-items-center justify-content-center">
                        <span class="rounded-3 text-white px-2" style="background-color:brown;">
                            LICHIDĂRI
                        </span>
                    </div>
                    <div class="mb-2 d-flex align-items-center justify-content-center">
                        <a
                            class="btn btn-sm btn-danger text-white me-1 border border-dark rounded-pill"
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#calculeazaAutomatLichidarile"
                            title="Calculează automat lichidarile"
                            >
                            Calculează automat lichidările
                        </a>
                        <div class="modal fade text-dark" id="calculeazaAutomatLichidarile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white" id="exampleModalLabel">Calculare automată a avansurilor</h5>
                                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align:left;">
                                    Ești sigur ca vrei să se calculeze automat toate lichidările pentru luna aleasă?
                                    <h4 class="text-center">{{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}</h4>
                                    Toate lichidările introduse manual se vor șterge!
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                                        <button class="btn btn-danger text-white border border-dark" type="submit"
                                            name="action" value="calculeazaAutomatLichidarile">
                                            Calculează automat lichidările
                                        </button>

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-flex align-items-center justify-content-center">
                        <div class="px-2 py-0 align-items-center rounded-pill text-dark" style="background-color:rgb(193, 255, 226)">
                            <div class="mb-1 d-flex justify-content-center align-items-center">
                                Export: &nbsp;
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportLichidariExcelToate">
                                    Excel Toate
                                </button>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportLichidariExcelBancaBt">
                                    Excel BT
                                </button>
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportLichidariTxtBancaIng">
                                    Txt ING
                                </button>
                                <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                    name="action" value="exportLichidariExcelMana">
                                    Excel Mână
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card-body px-0 py-0">

            @include ('errors')

            <div v-cloak v-if="angajatiPerProduri && angajatiPerProduri.length" class="row" id="salarii">
                <div class="col-lg-12 my-2 d-flex justify-content-center">
                    <button v-if="arataProduseleDesfasurat === 'nu'" class="btn btn-sm btn-primary text-white mx-1 border border-dark rounded-pill" type="button"
                        v-on:click="arataProduseleDesfasurat = 'da'"
                    >
                        Arată și realizatul pe fiecare produs în parte
                    </button>
                    <button v-if="arataProduseleDesfasurat === 'da'" class="btn btn-sm btn-warning text-black mx-1 border border-dark rounded-pill" type="button"
                        v-on:click="arataProduseleDesfasurat = 'nu'"
                    >
                        Ascunde realizatul pe fiecare produs în parte
                    </button>
                </div>
                <div class="col-lg-12 mx-auto rounded">
                    <table class="m-0 table table-sm table-bordered table-hover rounded" >
                        <thead class="text-white rounded sticky-top" style="background-color:#e66800;">
                            <tr class="" style="padding:2rem">
                                <th style="width: 50px;">#</th>
                                <th class="" style="">Angajat</th>
                                <th v-if="arataProduseleDesfasurat === 'da'" v-for="produs in produse" style="font-size: 12px; text-align:center; padding:0px;">@{{ produs.nume }}</th>
                                <th class="text-center px-0">Realizat</th>
                                <th class="text-center px-0">Avans</th>
                                <th class="text-center px-0">CO</th>
                                <th class="text-center px-0">Medicale</th>
                                <th class="text-center px-0">Salariu de bază</th>
                                <th class="text-center px-0">Pus</th>
                                <th class="text-center px-0">Realizat total</th>
                                <th class="text-center px-0">Lichidare</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="angajatiPerProd in angajatiPerProduri">
                                <template v-if="angajatiPerProd.length">
                                    <tr class="text-white" style="background-color:#e66800;">
                                        <th v-if="arataProduseleDesfasurat === 'da'" colspan={{ $produse->count() + 10 }} class="text-center">
                                            Prod @{{ angajatiPerProd[0].prod }} - {{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}
                                        </th>
                                        <th v-if="arataProduseleDesfasurat === 'nu'" colspan=10 class="text-center">
                                            Prod @{{ angajatiPerProd[0].prod }} - {{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}
                                        </th>
                                    </tr>
                                    <tr v-for="(angajat, index) in angajatiPerProd">
                                        <td style="padding: 0px 2px 0px 4px">
                                            @{{ index + 1 }}
                                        </td>
                                        <td style="padding: 0px 0px 0px 0px; font-weight:bold">
                                            @{{ angajat.nume }}
                                        </td>
                                        <td v-if="arataProduseleDesfasurat === 'da'" v-for="produs in produse" style="padding: 0px 2px 0px 4px; text-align:right">
                                            <span v-if="angajat.realizatProduse && angajat.realizatProduse[produs.id]" style="font-size: 12px !important; font-weight:bold;">
                                                @{{ angajat.realizatProduse[produs.id].toFixed(3) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; font-size: 12px !important; font-weight:bold; text-align:right;">
                                            <span v-if="angajat.realizatTotal" style="font-size: 12px !important; font-weight:bold;">
                                                @{{ angajat.realizatTotal.toFixed(3) }}
                                            </span>
                                        </td>
                                        <td class="d-flex justify-content-end align-items-center" style="font-size: 14px; padding:0px;">
                                            <div v-cloak v-if="numeCamp === 'avans' && salariuId === angajat.salarii[0].id" class="me-2 text-success">
                                                <i class="fas fa-thumbs-up"></i>
                                            </div>
                                            {{-- <input type="text" class="bg-white text-end rounded-3" style="width: 60px; border: none; padding:0px;" id="avans" name="avans" --}}
                                            <input type="text" class="bg-white text-end rounded-3" style="width: 60px; border:1px solid aqua; padding:0px;" id="avans" name="avans"
                                                    :value="angajat.salarii[0].avans"
                                                    v-on:blur = "actualizeazaValoare(angajat.salarii[0].id, 'avans', $event.target.value)"
                                                    >
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; text-align:right;">
                                            <span v-if="angajat.sumaConcediuOdihna && (angajat.sumaConcediuOdihna != 0)" style="font-size: 12px !important; font-weight:bold;">
                                                @{{ angajat.sumaConcediuOdihna.toFixed(3) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; text-align:right;">
                                            <span v-if="angajat.sumaConcediuMedical && (angajat.sumaConcediuMedical != 0)" style="font-size: 12px !important; font-weight:bold;">
                                                @{{ angajat.sumaConcediuMedical.toFixed(3) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; text-align:right; background-color:rgb(255, 191, 191)">
                                            {{-- <span v-if="angajat.realizatTotal && angajat.sumaConcediuOdihna && angajat.sumaConcediuMedical" style="font-size: 12px !important; font-weight:bold;"> --}}
                                                @{{ (parseFloat(angajat.realizatTotal) + parseFloat(angajat.sumaConcediuOdihna) + parseFloat(angajat.sumaConcediuMedical)).toFixed(3) }}
                                            {{-- </span> --}}
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; text-align:right;">
                                            <span style="font-size: 12px !important; font-weight:bold;">
                                                0
                                            </span>
                                        </td>
                                        <td style="padding: 0px 2px 0px 4px; text-align:right; background-color:rgb(172, 218, 186)">
                                            {{-- <span v-if="angajat.realizatTotal && angajat.sumaConcediuOdihna && angajat.sumaConcediuMedical" style="font-size: 12px !important; font-weight:bold;"> --}}
                                                @{{ (parseFloat(angajat.realizatTotal) + parseFloat(angajat.sumaConcediuOdihna) + parseFloat(angajat.sumaConcediuMedical)).toFixed(3) }}
                                            {{-- </span> --}}
                                        </td>
                                        {{-- <td style="padding: 0px 2px 0px 4px; text-align:right;">
                                            <span style="font-size: 12px !important; font-weight:bold;">
                                                @{{ (parseFloat(angajat.realizatTotal.toFixed(3)) + parseFloat(angajat.sumaConcediuOdihna.toFixed(3)) + parseFloat(angajat.sumaConcediuMedical.toFixed(3)) - parseFloat(angajat.salarii[0].avans.toFixed(3))).toFixed(3) }}
                                            </span>
                                        </td> --}}
                                        <td class="d-flex justify-content-end align-items-center" style="font-size: 14px; padding:0px;">
                                            <div v-cloak v-if="numeCamp === 'lichidare' && salariuId === angajat.salarii[0].id" class="me-2 text-success">
                                                <i class="fas fa-thumbs-up"></i>
                                            </div>
                                            <input type="text" class="bg-white text-end rounded-3" style="width: 80px; border: 1px solid aqua; padding:0px;" id="lichidare" name="lichidare"
                                                    :value="angajat.salarii[0].lichidare"
                                                    v-on:blur = "actualizeazaValoare(angajat.salarii[0].id, 'lichidare', $event.target.value)"
                                                    >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td v-if="arataProduseleDesfasurat === 'da'" colspan="{{ $produse->count() + 2 }}" style="text-align: center">
                                            <b>Total</b>
                                        </td>
                                        <td v-if="arataProduseleDesfasurat === 'nu'" colspan=2 style="text-align: center">
                                            <b>Total</b>
                                        </td>
                                        <td style="text-align: right; padding-right:2px;">
                                            @{{ totalRealizatPerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                        <td style="text-align: right; padding-right:2px; font-size: 14px !important; font-weight:bold;">
                                            @{{ totalAvansuriPerProduri[angajatiPerProd[0].prod] }}
                                        </td>
                                        <td>
                                            @{{ totalCoPerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                        <td>
                                            @{{ totalMedicalePerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                        <td>
                                            @{{ totalSalariuDeBazaPerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            @{{ totalRealizatTotalPerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                        <td style="text-align: right; padding-right:2px; font-size: 14px !important; font-weight:bold;">
                                            @{{ totalLichidariPerProduri[angajatiPerProd[0].prod].toFixed(3) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td v-if="arataProduseleDesfasurat === 'da'" colspan="{{ $produse->count() + 10 }}">
                                            &nbsp;
                                            <br>
                                            &nbsp;
                                            <br>
                                            &nbsp;
                                        </td>
                                        <td v-if="arataProduseleDesfasurat === 'nu'" colspan="10" style="text-align: center">
                                            &nbsp;
                                            <br>
                                            &nbsp;
                                            <br>
                                            &nbsp;
                                        </td>
                                    </tr>
                                </template>
                            </template>
                    </table>
                </div>

            </div>
        </div>
    </div>

@endsection
