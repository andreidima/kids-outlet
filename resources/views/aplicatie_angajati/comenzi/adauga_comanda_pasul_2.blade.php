@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;">
        <div class="row p-2 align-items-center">
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

                <h4 class="text-left mb-4">
                    <small>Produs:</small> {{ $angajat->produs_nume }}
                </h4>

                <h4 class="text-center">NUMĂR DE FAZĂ:</h4>

                <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/adauga-comanda-pasul-2"
                    autocomplete="off"
                >
                        @csrf

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <input class="form-control form-control-lg mb-3" type="text" name="numar_de_faza" autofocus>

                            <button type="submit" class="mb-2 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">SELECTEAZĂ</button>
                            <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/meniul-principal" style="border:2px solid white;">RENUNȚĂ</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
