@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;">
        <div class="row p-2 align-items-center">
            <div class="col-md-6 col-lg-5 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
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

                {{-- <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4> --}}

                <h4 class="mb-4">
                    Pontaj: {{ $pontaj->angajat->nume  }}

                    {{-- Conturilor Mocanu Geanina si Duna Luminita nu li se afiseaza codurile de acces --}}
                    {{-- @if (
                            ($angajat->id === 4) // Mocanu Geanina
                            || ($angajat->id === 12) // Duna Luminita
                        )
                    @else
                        <br>
                        Cod de acces: {{ $pontaj->angajat->cod_de_acces }}
                    @endif --}}

                    <br>
                    Data: {{ $pontaj->data ? \Carbon\Carbon::parse($pontaj->data)->isoFormat('DD.MM.YYYY') : ''}}
                </h4>

                @include('errors')

                <form  class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                    @csrf

                    <div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
                        <div class="col-lg-12 px-2 mb-0">
                            <input class="form-control form-control-lg mb-3" type="hidden" name="angajat_id" value="{{ $pontaj->angajat->id }}">
                            <input class="form-control form-control-lg mb-3" type="hidden" name="moment" value="modificare_particularizata">
                            <input class="form-control form-control-lg mb-3" type="hidden" name="data" value="{{ \Carbon\Carbon::now() }}">

                            <div class="row">
                                <div class="col-6 mb-5 text-center">
                                    <label for="ora_sosire" class="mb-0 ps-1">Sosire:</label>
                                        <vue2-datepicker
                                            data-veche="{{ old('ora_sosire', ($pontaj->ora_sosire ?? '')) }}"
                                            nume-camp-db="ora_sosire"
                                            tip="time"
                                            value-type="HH:mm"
                                            format="HH:mm"
                                            :latime="{ width: '90px' }"
                                        ></vue2-datepicker>
                                </div>
                                <div class="col-6 mb-5 text-center">
                                    <label for="ora_plecare" class="mb-0 pe-2">Plecare:</label>
                                        <vue2-datepicker
                                            data-veche="{{ old('ora_plecare', ($pontaj->ora_plecare ?? '')) }}"
                                            nume-camp-db="ora_plecare"
                                            tip="time"
                                            value-type="HH:mm"
                                            format="HH:mm"
                                            :latime="{ width: '90px' }"
                                        ></vue2-datepicker>
                                </div>

                                <div class="col-12 mb-4">
                                    {{-- <input class="form-check-input" type="hidden" name="concediu" value="0" /> --}}

                                    <div class="form-check mb-3">
                                        <input class="form-check-input form-check-input-lg" type="radio" value="0" name="concediu" id="prezent_la_muna"
                                            {{ (old('concediu', $pontaj->concediu) == '0') || (old('concediu', $pontaj->concediu) == '') ? 'checked' : '' }}>
                                        <label class="form-check-label px-1 fs-5 bg-primary border border-1 shadow shadow-sm" for="prezent_la_muna">
                                            Prezent la muncă
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input form-check-input-lg" type="radio" value="1" name="concediu" id="concediu_medical"
                                            {{ old('concediu', $pontaj->concediu) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label px-1 fs-5 bg-primary border border-1 shadow shadow-sm" for="concediu_medical">
                                            Concediu medical
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="2" name="concediu" id="concediu_de_odihna"
                                            {{ old('concediu', $pontaj->concediu) == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label px-1 fs-5 bg-primary border border-1 shadow shadow-sm" for="concediu_de_odihna">
                                            Concediu de odihnă
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="3" name="concediu" id="invoit"
                                            {{ old('concediu', $pontaj->concediu) == '3' ? 'checked' : '' }}>
                                        <label class="form-check-label px-1 fs-5 bg-primary border border-1 shadow shadow-sm" for="invoit">
                                            Învoit
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="4" name="concediu" id="nemotivat"
                                            {{ old('concediu', $pontaj->concediu) == '4' ? 'checked' : '' }}>
                                        <label class="form-check-label px-1 fs-5 bg-primary border border-1 shadow shadow-sm" for="nemotivat">
                                            Nemotivat
                                        </label>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="mb-2 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">SALVEAZĂ</button>
                                    <a class="mb-2 btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/pontaj" style="border:2px solid white;">RENUNȚĂ</a>
                                    {{-- <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection
