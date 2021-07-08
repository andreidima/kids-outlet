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
                        <a class="btn btn-sm text-white" href="/adauga-comanda-noua" role="button" style="background-color: #FC4A1A;">DECONECTARE</a>
                    </div>
                </div>

                <div class="mb-5" style="background-color: #000000; height:5px;"></div>

                @include('errors')

                <a class="btn btn-sm text-white" href="/adauga-comanda-noua" role="button" style="background-color: #FC4A1A;">sdf</a>
                <a class="btn btn-sm text-white" href="/adauga-comanda-noua" role="button" style="background-color: #FC4A1A;">DECONECTARE</a>


            </div>
        </div>
    </div>
@endsection
