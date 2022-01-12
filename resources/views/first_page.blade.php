@extends('layouts.app')

@section('content')
    <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center">
            <div class="col-md-4 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>

                    <div class="mb-3" style="background-color: #000000; height:5px;"></div>
                </div>


                <div class="mb-2" style="background-color: #000000; height:5px;"></div>

                <h4 class="mb-4">Bun venit</h4>

                <h4 class="mb-4 text-center">Întră în aplicație ca:</h4>
                @include('errors')


                <div class="row">
                    <div class="col-md-12 mb-4 text-center">
                        <a class="btn btn-lg btn-primary w-100 text-white" href="/aplicatie-angajati"
                            style="background-color: #FC4A1A; border:2px solid white"
                        >
                            ANGAJAT</a>
                    </div>
                    <div class="col-md-12 mb-2 text-center">
                        <a class="btn btn-lg btn-primary w-100 text-white" href="/login"
                            style="background-color: #FC4A1A; border:2px solid white;"
                        >ADMINISTRATOR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
