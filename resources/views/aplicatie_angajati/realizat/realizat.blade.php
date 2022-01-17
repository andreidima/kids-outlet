@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-6 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>

                    <div class="mb-3" style="background-color: #000000; height:5px;"></div>

                    <div>
                        {{-- <form class="needs-validation" novalidate method="POST" action="/adauga-comanda-noua">
                            <button type="submit" class="btn btn-sm text-white" style="background-color: #FC4A1A;">DECONECTARE</button>
                        </form> --}}
                        <a class="btn btn-sm text-white" href="/aplicatie-angajati/deconectare" role="button" style="background-color: #FC4A1A; border:1px solid white;">DECONECTARE</a>
                    </div>
                </div>

                <div class="mb-2" style="background-color: #000000; height:5px;"></div>

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                @include('errors')

                <h4 class="mb-4 text-center"><b>REALIZAT</b>  </h4>

                <div class="row text-center mb-4 mx-0" id="app1">
                    <form class="needs-validation" novalidate method="GET" action="{{ route('aplicatie_angajati.realizat') }}">
                        @csrf
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <label for="search_data_inceput" class="mb-0 align-self-center me-1">De la:</label>
                                    <vue2-datepicker
                                        data-veche="{{ $search_data_inceput }}"
                                        nume-camp-db="search_data_inceput"
                                        tip="date"
                                        value-type="YYYY-MM-DD"
                                        format="DD-MM-YYYY"
                                        :latime="{ width: '125px' }"
                                    ></vue2-datepicker>

                            </div>
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <label for="search_data_sfarsit" class="mb-0 align-self-center me-1">Până la:</label>
                                    <vue2-datepicker
                                        data-veche="{{ $search_data_sfarsit }}"
                                        nume-camp-db="search_data_sfarsit"
                                        tip="date"
                                        value-type="YYYY-MM-DD"
                                        format="DD-MM-YYYY"
                                        :latime="{ width: '125px' }"
                                    ></vue2-datepicker>
                            </div>
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <button class="btn btn-lg w-100 text-white"
                                    type="submit"
                                    style="background-color: #FC4A1A; border:2px solid white;">
                                    <i class="fas fa-search text-white me-1"></i>Caută
                                </button>
                                {{-- <a class="btn btn-lg w-100 text-white"
                                    role="button"
                                    type="submit"
                                    style="background-color: #FC4A1A; border:2px solid white;">
                                    <i class="fas fa-search text-white me-1"></i>CAUTĂ
                                </a> --}}
                            </div>
                    </form>
                </div>

                {{-- @php
                    $suma_totala = 0;
                @endphp
                @forelse ($norme_lucrate->groupBy('numar_de_faza') as $norme_lucrate_per_numar_de_faza)
                    @php
                        $suma_totala += $norme_lucrate_per_numar_de_faza->sum('cantitate') * $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret
                    @endphp
                    <div class="mb-4 px-1" style="background-color:#007e6b;">
                        <small>Număr de fază:</small> {{ $norme_lucrate_per_numar_de_faza->first()->numar_de_faza }}
                        <br>
                        <small>Număr de bucăți în total:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') }}
                        <br>
                        <small>Preț pe bucată:</small> {{ $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
                        <br>
                        <small>Suma totală:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') * $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
                    </div>
                @empty
                @endforelse

                <h4 class="mb-4 text-center">
                    <b>
                        REALIZAT TOTAL:
                        <br>
                        {{ $suma_totala }} lei
                    </b>
                </h4> --}}


                @forelse ($norme_lucrate->groupBy('produs_operatie_id') as $norme_lucrate_per_operatie)
                    {{-- @forelse ($norme_lucrate_per_produs->groupBy('numar_de_faza') as $norme_lucrate_per_numar_de_faza_per_numar_de_faza) --}}
                        <div class="mb-4 px-1" style="background-color:#007e6b;">
                            <small>Data:</small>
                                {{
                                    $norme_lucrate_per_operatie->first()->data ?
                                    \Carbon\Carbon::parse($norme_lucrate_per_operatie->first()->data)->isoFormat('DD.MM.YYYY')
                                    :
                                    ''
                                }}
                            <br>
                            <small>Produs:</small> {{ $norme_lucrate_per_operatie->first()->produs_operatie->produs->nume }}
                            <br>
                            <small>Număr de fază:</small> {{ $norme_lucrate_per_operatie->first()->produs_operatie->numar_de_faza }}
                            <br>
                            <small>Operație:</small> {{ $norme_lucrate_per_operatie->first()->produs_operatie->nume }}
                            <br>
                            <small>Număr de bucăți în total:</small> {{ $norme_lucrate_per_operatie->sum('cantitate') }}
                        </div>
                    {{-- @empty
                    @endforelse --}}
                @empty
                @endforelse

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
