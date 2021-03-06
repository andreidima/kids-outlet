@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;">
        <div class="row p-2 align-items-center">
            <div class="col-md-6 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
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

                    <h4 class="alert-success p-2">
                        Comanda a fost introdusă cu success!
                    </h4>

                <h4 class="mb-4">

                    <small>Produs:</small> {{ $angajat->produs_nume }}
                    <br>
                    <small>Număr de fază:</small> {{ $angajat->numar_de_faza }}
                    <br>
                    <small>Operație:</small> {{ $angajat->operatie_nume }}
                    <br>
                    {{-- <br> --}}
                    {{-- <small>Număr de bucăți adăugate la ultima comandă:</small> {{ $angajat->cantitate }} --}}
                    <small>Număr de bucăți adăugate:</small> {{ $angajat->cantitate }}
                    {{-- <br>
                    <small>Număr de bucăți în total:</small> {{ $angajat->cantitate_total }}
                    <br>
                    <br>
                    <small>Preț pe bucată:</small> {{ $angajat->pret_pe_bucata }} lei
                    <br>
                    <small>Suma totală:</small> {{ $angajat->cantitate_total * $angajat->pret_pe_bucata }} lei --}}
                </h4>

                @include('errors')

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
