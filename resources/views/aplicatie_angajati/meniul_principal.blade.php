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

                {{ $angajat->numar_de_faza }}
                <br>
                {{ $angajat->numar_de_bucati }}

                @include('errors')

                <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/adauga-comanda-pasul-1" role="button" style="background-color: #FC4A1A; border:2px solid white;">COMANDĂ</a>
                <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj" role="button" style="background-color: #FC4A1A; border:2px solid white;">PONTAJ</a>
                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/realizat" role="button" style="background-color: #FC4A1A; border:2px solid white;">REALIZAT</a>


            </div>
        </div>
    </div>
@endsection