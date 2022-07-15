@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;" id="app1">
        <div class="row p-2 align-items-center">
            <div class="col-md-6 col-lg-5 p-0 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
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

                <h2 class="mb-4 text-center"><b>PONTAJ</b>: {{ \Carbon\Carbon::parse($data_pontaj)->isoFormat('DD.MM.YYYY') }}  </h2>

                @include('errors')

                {{-- <div class="row text-center mb-4 mx-0">
                    <div class="col-4 p-2" style="background-color:#003f36; border-right: 5px #4ABDAC solid">
                        Angajat
                    </div>
                    <div class="col-4 p-2" style="background-color:#003f36; border-right: 5px #4ABDAC solid">
                        SOSIRE
                    </div>
                    <div class="col-4 p-2" style="background-color:#003f36;">
                        PLECARE
                    </div>
                </div>
                @foreach ($angajati as $angajat)
                    <div class="row text-center mb-4 mx-0">
                        <div class="col-4 p-2 d-flex align-items-center justify-content-center" style="background-color:#007e6b; border-right: 5px #4ABDAC solid">
                            {{ $angajat->nume }}
                        </div>
                        <div class="col-4 p-2 d-flex align-items-center justify-content-center" style="background-color:#007e6b; border-right: 5px #4ABDAC solid">
                            @isset($angajat->pontaj_azi->ora_sosire)
                                <h4 class="mb-0">
                                    {{ $angajat->pontaj_azi->ora_sosire ? \Carbon\Carbon::parse($angajat->pontaj_azi->ora_sosire)->isoFormat('HH:mm') : '' }}
                                </h4>
                            @else
                                <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                                    @csrf

                                    <input class="form-control form-control-lg mb-3" type="hidden" name="angajat_id" value="{{ $angajat->id }}">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="moment" value="sosire">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="data" value="{{ \Carbon\Carbon::now() }}">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="ora" value="{{ \Carbon\Carbon::now() }}">

                                    <button type="submit" class="mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                        SETEAZĂ
                                    </button>
                                </form>
                            @endisset
                        </div>
                        <div class="col-4 p-2 d-flex align-items-center justify-content-center" style="background-color:#007e6b;">
                            @isset($angajat->pontaj_azi->ora_plecare)
                                <h4 class="mb-0">
                                    {{ $angajat->pontaj_azi->ora_plecare ? \Carbon\Carbon::parse($angajat->pontaj_azi->ora_plecare)->isoFormat('HH:mm') : '' }}
                                </h4>
                            @else
                                <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                                    @csrf

                                    <input class="form-control form-control-lg mb-3" type="hidden" name="angajat_id" value="{{ $angajat->id }}">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="moment" value="plecare">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="data" value="{{ \Carbon\Carbon::now() }}">
                                    <input class="form-control form-control-lg mb-3" type="hidden" name="ora" value="{{ \Carbon\Carbon::now() }}">

                                    <button type="submit" class="mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                        SETEAZĂ
                                    </button>
                                </form>
                            @endisset
                        </div>
                    </div>
                @endforeach --}}

                <div class="table-responsive">
                    <table class="table table-light table-striped align-middle">
                        <thead>
                            <tr>
                                {{-- <th scope="col">
                                    #
                                </th> --}}
                                <th scope="col" class="text-center">
                                    ANGAJAT
                                </th>
                                <th scope="col" class="text-center">
                                    SOSIRE
                                </th>
                                <th scope="col" class="text-center">
                                    PLECARE
                                </th>
                                <th scope="col" class="text-center">
                                    AVANSAT
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($angajati as $angajat)
                                <tr>
                                    {{-- <th scope="row">
                                        {{ $loop->iteration }}
                                    </th> --}}
                                    <td>
                                        {{ $angajat->nume }}
                                    </td>
                                    <td class="text-center align-items-center">
                                        @switch($angajat->pontaj->first()->concediu ?? '')
                                            @case(0)
                                                @isset($angajat->pontaj->first()->ora_sosire)
                                                    <h4 class="mb-0">
                                                        {{ $angajat->pontaj->first()->ora_sosire ? \Carbon\Carbon::parse($angajat->pontaj->first()->ora_sosire)->isoFormat('HH:mm') : '' }}
                                                    </h4>
                                                @else
                                                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                                                        @csrf

                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="angajat_id" value="{{ $angajat->id }}">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="moment" value="sosire">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="data" value="{{ $data_pontaj }}">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="ora" value="{{ \Carbon\Carbon::now() }}">

                                                        <button type="submit" class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                                            SETEAZĂ
                                                        </button>
                                                    </form>
                                                @endisset
                                                @break
                                            @case(1)
                                                <h4 class="mb-0">
                                                    C.M.
                                                </h4>
                                                @break
                                            @case(2)
                                                <h4 class="mb-0">
                                                    C.O.
                                                </h4>
                                                @break
                                            @case(3)
                                                <h4 class="mb-0">
                                                    Î
                                                </h4>
                                                @break
                                            @case(4)
                                                <h4 class="mb-0">
                                                    N
                                                </h4>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @switch($angajat->pontaj->first()->concediu ?? '')
                                            @case(0)
                                                @isset($angajat->pontaj->first()->ora_plecare)
                                                    <h4 class="mb-0">
                                                        {{ $angajat->pontaj->first()->ora_plecare ? \Carbon\Carbon::parse($angajat->pontaj->first()->ora_plecare)->isoFormat('HH:mm') : '' }}
                                                    </h4>
                                                @else
                                                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                                                        @csrf

                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="angajat_id" value="{{ $angajat->id }}">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="moment" value="plecare">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="data" value="{{ $data_pontaj }}">
                                                        <input class="form-control form-control-lg mb-3" type="hidden" name="ora" value="{{ \Carbon\Carbon::now() }}">

                                                        <button type="submit" class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                                            SETEAZĂ
                                                        </button>
                                                    </form>
                                                @endisset
                                                @break
                                            @case(1)
                                                <h4 class="mb-0">
                                                    C.M.
                                                </h4>
                                                @break
                                            @case(2)
                                                <h4 class="mb-0">
                                                    C.O.
                                                </h4>
                                                @break
                                            @case(3)
                                                <h4 class="mb-0">
                                                    Î
                                                </h4>
                                                @break
                                            @case(4)
                                                <h4 class="mb-0">
                                                    N
                                                </h4>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-primary text-white" href="/aplicatie-angajati/pontaj/{{ $angajat->id }}/modifica" role="button">
                                            MODIFICĂ
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="">
                                <td class="bg-warning py-5">
                                    Toți Angajații
                                </td>
                                <td class="text-center bg-warning py-5">
                                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj/sosire/ponteaza-toti" autocomplete="off">
                                        @csrf
                                        <div class="mb-1">
                                            <label for="ora_sosire" class="mb-0 ps-1">Sosire:</label>
                                            <vue2-datepicker
                                                data-veche="{{ old('ora_sosire', \Carbon\Carbon::now()->toTimeString()) }}"
                                                nume-camp-db="ora_sosire"
                                                tip="time"
                                                value-type="HH:mm"
                                                format="HH:mm"
                                                :latime="{ width: '90px' }"
                                            ></vue2-datepicker>
                                        </div>
                                        <button class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;"
                                            type="submit" role="button">
                                            SETEAZĂ
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center bg-warning py-5">
                                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj/plecare/ponteaza-toti" autocomplete="off">
                                        @csrf
                                        <div class="mb-1">
                                            <label for="ora_plecare" class="mb-0 ps-1">Plecare:</label>
                                            <vue2-datepicker
                                                data-veche="{{ old('ora_plecare', \Carbon\Carbon::now()->toTimeString()) }}"
                                                nume-camp-db="ora_plecare"
                                                tip="time"
                                                value-type="HH:mm"
                                                format="HH:mm"
                                                :latime="{ width: '90px' }"
                                            ></vue2-datepicker>
                                        </div>
                                        <button class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;"
                                            type="submit" role="button">
                                            SETEAZĂ
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center bg-warning">
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>



                <div class="py-5 align-items-center">
                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/pontaj" autocomplete="off">
                        @csrf
                        <h5 class="text-center">Alege altă dată, pentru a modifica un pontaj din altă zi</h5>
                        <div class="mb-3 d-flex justify-content-center align-items-center">
                            <label for="data_pontaj" class="mb-0 pe-2">Data:</label>
                                <vue2-datepicker
                                    data-veche="{{ old('data_pontaj', $data_pontaj) }}"
                                    nume-camp-db="data_pontaj"
                                    tip="date"
                                    value-type="YYYY-MM-DD"
                                    format="DD-MM-YYYY"
                                    :latime="{ width: '125px' }"
                                ></vue2-datepicker>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="px-0 mb-0 btn btn-lg w-50 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                Selectează
                            </button>
                        </div>
                    </form>
                </div>

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
