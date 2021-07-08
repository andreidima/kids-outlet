@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-2 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>
                    <div>
                        {{-- <form class="needs-validation" novalidate method="POST" action="/adauga-comanda-noua">
                            <button type="submit" class="btn btn-sm text-white" style="background-color: #FC4A1A;">DECONECTARE</button>
                        </form> --}}
                        <a class="btn btn-sm text-white" href="/adauga-comanda-noua" role="button" style="background-color: #FC4A1A;">DECONECTARE</a>
                    </div>
                </div>

                <div class="mb-3" style="background-color: #000000; height:5px;"></div>

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat_comanda->nume }}</b></h4>

                <h4 class="mb-4">
                    <small>Nume de fază:</small> {{ $angajat_comanda->numar_de_faza }}
                    <br>
                    <small>Preț pe bucată:</small> {{ $angajat_comanda->numar_de_faza }}
                </h4>
                @include('errors')

                <h4 class="text-center">NUMĂR DE BUCĂȚI</h4>

                <form class="needs-validation" novalidate method="POST" action="/adauga-comanda-pasul-3"
                    autocomplete="off"
                >
                        @csrf

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <input class="form-control form-control-lg mb-3" type="text" name="numar_de_bucati">

                            <button type="submit" class="btn btn-lg w-100 text-white" style="background-color: #FC4A1A;">ADAUGĂ</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
