@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-2 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
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

                <div class="row text-center mb-4 mx-0">
                    <div class="col-6 p-2" style="background-color:#007e6b; border-right: 5px #4ABDAC solid">
                        <h4>SOSIRE</h4>

                            {{-- {{ \App\Models\Pontaj::where('data', \Carbon\Carbon::today()); }} --}}
                        {{-- @php
                        dd ($angajat, $angajat->pontaj_azi->ora_sosire, \App\Models\Pontaj::where('data', \Carbon\Carbon::today())->first());
                            dd($angajat);

                        @endphp --}}
                        @isset($angajat->pontaj_azi->ora_sosire)
                            <h4 class="mt-3">{{ $angajat->pontaj_azi->ora_sosire ?? '' }}</h4>
                        @else
                            <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj/sosire" style="background-color: #FC4A1A; border:2px solid white;">SETEAZĂ</a>
                        @endisset
                    </div>
                    <div class="col-6 p-2" style="background-color:#007e6b; border-left: 5px #4ABDAC solid">
                        <h4>PLECARE</h4>
                        @isset($angajat->pontaj_azi->ora_plecare)
                            <h4 class="mt-3">{{ $angajat->pontaj_azi->ora_plecare ?? '' }}</h4>
                        @else
                            <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj/plecare" style="background-color: #FC4A1A; border:2px solid white;">SETEAZĂ</a>
                        @endisset
                    </div>
                </div>

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
