@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;" id="app1">
        <div class="row p-2 align-items-center">
            <div class="col-md-6 col-lg-5 p-0 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>

                    <div class="mb-3" style="background-color: #000000; height:5px;"></div>

                    <div>
                        <a class="btn btn-sm text-white" href="/aplicatie-angajati/deconectare" role="button" style="background-color: #FC4A1A; border:1px solid white;">DECONECTARE</a>
                    </div>
                </div>

                <div class="mb-2" style="background-color: #000000; height:5px;"></div>

                @include('errors')

                <div class="table-responsive">
                    <table class="table table-light table-striped align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">
                                    ANGAJAT
                                </th>
                                <th scope="col" class="text-center">
                                    Cod de acces
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($angajati as $angajat)
                                <tr>
                                    <td>
                                        {{ $angajat->nume }}
                                    </td>
                                    <td>
                                        {{ $angajat->cod_de_acces }}
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
