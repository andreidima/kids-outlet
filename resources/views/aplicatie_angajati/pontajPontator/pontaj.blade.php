@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
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

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                <h4 class="mb-4"><b>PONTAJ</b>: {{ \Carbon\Carbon::today()->isoFormat('DD.MM.YYYY') }}  </h4>

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

                                                <button type="submit" class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                                    SETEAZĂ
                                                </button>
                                            </form>
                                        @endisset
                                    </td>
                                    <td class="text-center">
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

                                                <button type="submit" class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">
                                                    SETEAZĂ
                                                </button>
                                            </form>
                                        @endisset
                                    </td>
                                    <td>
                                        <a class="btn btn-primary text-white" href="#" role="button">
                                            MODIFICĂ
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection