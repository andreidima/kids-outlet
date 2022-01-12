@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-4 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>
                    <div>
                        {{-- <form class="needs-validation" novalidate method="POST" action="/adauga-comanda-noua">
                            <button type="submit" class="btn btn-sm text-white" style="background-color: #FC4A1A;">DECONECTARE</button>
                        </form> --}}
                        <a class="btn btn-sm text-white" href="/aplicatie-angajati/deconectare" role="button" style="background-color: #FC4A1A; border:1px solid white;">DECONECTARE</a>
                    </div>
                </div>

                <div class="mb-3" style="background-color: #000000; height:5px;"></div>

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                <h4 class="mb-4">
                    <small>Număr de fază:</small> {{ $angajat->numar_de_faza }}
                    <br>
                    <small>Produs:</small> {{ $angajat->produs }}
                    <br>
                    <small>Operație:</small> {{ $angajat->operatie }}
                    <br>
                    <br>
                    <small>Preț pe bucată:</small> {{ $angajat->pret_pe_bucata }} lei
                </h4>
                @include('errors')

                <h4 class="text-center">NUMĂR DE BUCĂȚI</h4>

                <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/adauga-comanda-pasul-2"
                    autocomplete="off"
                >
                        @csrf

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <input class="form-control form-control-lg mb-3" type="text" name="numar_de_bucati">

                            <button type="submit" class="mb-2 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">ADAUGĂ</button>
                            <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/meniul-principal" style="border:2px solid white;">RENUNȚĂ</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
