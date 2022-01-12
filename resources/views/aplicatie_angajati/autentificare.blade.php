@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-4 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <h4 class="pb-1">{{ config('app.name', 'Laravel') }}</h4>

                <div class="mb-5" style="background-color: #000000; height:5px;"></div>

                @include('errors')

                <h4 class="text-center">COD DE ACCES</h4>

                <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati"
                    autocomplete="off"
                >
                        @csrf

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <input class="form-control form-control-lg mb-3" type="text" name="cod_de_acces">

                            <button type="submit" class="btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;">AUTENTIFICARE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
