@extends ('layouts.app')

<script type="application/javascript">
    angajati =  {!! json_encode($angajati) !!}
</script>

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <form class="needs-validation" novalidate method="GET" action="{{ url()->current() }}">
            @csrf
            <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
                <div class="col-lg-2">
                    <h4 class="mb-0">Avansuri</a></h4>
                </div>
                <div class="col-lg-6" id="app1">
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
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-2 d-flex align-items-center justify-content-end">
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
                    <div class="d-grid gap-2 d-flex align-items-center justify-content-end">
                        <div class="px-2 py-0 d-flex align-items-center rounded-pill text-dark" style="background-color:rgb(193, 255, 226)">
                            Export: &nbsp;
                        {{-- </div>
                        <div class="col-md-3 d-grid gap-2"> --}}
                            {{-- <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportExcelAvansuri">
                                Excel Toate
                            </button> --}}
                        {{-- </div>
                        <div class="col-md-3 d-grid gap-2"> --}}
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportExcelBancaBt">
                                Excel BT
                            </button>
                        {{-- </div>
                        <div class="col-md-3 d-grid gap-2"> --}}
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportTxtBancaIng">
                                Txt ING
                            </button>
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportExcelMana">
                                Excel Mână
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 d-grid gap-2 d-flex align-items-center">
                </div>
            </div>
        </form>

        <div class="card-body px-0 py-3">

            @include ('errors')

            <div class="row" id="setareAvansuri">
                {{-- Varianta PHP --}}
                {{-- @foreach ($angajati->groupBy('prod') as $angajatiPerProd)
                <div class="col-lg-7 mb-3 mx-auto">
                    <div class="table-responsive rounded">
                        <table class="table table-striped table-hover table-sm rounded">
                            <thead class="text-white rounded" style="background-color:#e66800;">
                                <tr>
                                    <th colspan=3 class="text-center">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                Prod: {{ $angajatiPerProd->first()->prod }}
                                            </div>
                                            <div>
                                                {{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}
                                            </div>
                                        </div>
                                    </th>
                                <tr class="" style="padding:2rem">
                                    <th style="width: 50px;">#</th>
                                    <th>Angajat</th>
                                    <th class="text-end" style="width: 80px;">Avans</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($angajatiPerProd as $angajat)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <b>{{ $angajat->nume ?? '' }}</b>
                                    </td>
                                    <td class="d-flex justify-content-end align-items-center">
                                        <div v-cloak v-if="avansId === {{ $angajat->avansuri->first()->id }}" class="me-2 text-success">
                                            <i class="fas fa-thumbs-up"></i>
                                        </div>
                                        <input type="text" class="form-control form-control-sm bg-white text-end rounded-3" style="width: 80px" id="avans" name="avans"
                                                value="{{ $angajat->avansuri->first()->suma ?? '' }}"
                                                v-on:blur = "actualizeazaAvans({{ $angajat->avansuri->first()->id }}, $event.target.value)"
                                                >
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach --}}

                {{-- Varianta VueJs --}}
                <div v-for="angajatiPerProd in angajatiPerProduri" class="col-lg-7 mb-3 mx-auto">
                    <div v-if="angajatiPerProd.length">
                        <div class="table-responsive rounded">
                            <table class="table table-striped table-hover table-sm rounded">
                                <thead class="text-white rounded" style="background-color:#e66800;">
                                    <tr>
                                        <th colspan=3 class="text-center">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Prod: @{{ angajatiPerProd[0].prod }}
                                                </div>
                                                <div>
                                                    {{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}
                                                </div>
                                            </div>
                                        </th>
                                    <tr class="" style="padding:2rem">
                                        <th style="width: 50px;">#</th>
                                        <th>Angajat</th>
                                        <th class="text-end" style="width: 80px;">Avans</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    <tr v-for="(angajat, index) in angajatiPerProd">
                                        <td>
                                            @{{ index + 1 }}
                                        </td>
                                        <td>
                                            <b>@{{ angajat.nume }}</b>
                                        </td>
                                        <td class="d-flex justify-content-end align-items-center">
                                            <div v-cloak v-if="avansId === angajat.avansuri[0].id" class="me-2 text-success">
                                                <i class="fas fa-thumbs-up"></i>
                                            </div>
                                            <input type="text" class="form-control form-control-sm bg-white text-end rounded-3" style="width: 80px" id="avans" name="avans"
                                                    :value="angajat.avansuri[0].suma"
                                                    v-on:blur = "actualizeazaAvans(angajat.avansuri[0].id, $event.target.value)"
                                                    >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center">
                                            <b>Total</b>
                                        </td>
                                        <td style="text-align: right; padding-right:10px">
                                            <b>@{{ totalAvansuriPerProduri[angajatiPerProd[0].prod] }}</b>
                                        </td>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
