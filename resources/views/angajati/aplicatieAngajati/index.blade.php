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

                @foreach ($angajati->sortBy('prod')->groupBy('prod') as $angajati_per_prod)
                    <h3 class="text-center">
                        Prod {{ $angajati_per_prod->first()->prod ?? 'ne setat' }}
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-light table-striped align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        ANGAJAT
                                    </th>
                                    <th scope="col">
                                        Cod de acces
                                    </th>
                                    <th>
                                        Sectia
                                    </th>
                                    <th>
                                        firma
                                    </th>
                                    <th>
                                        Foaie pontaj
                                    </th>
                                    <th>
                                        Ore angajare
                                    </th>
                                    <th>
                                        Stare cont
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($angajati_per_prod->sortBy('nume') as $angajat)
                                    <tr>
                                        <td>
                                            {{ $angajat->nume }}
                                        </td>
                                        <td>
                                            {{-- Conturilor pentru care se afiseaza codurile de acces --}}
                                            @if (
                                                    ($angajat->id === 1) // Andrei Dima Administrator 1
                                                    || ($angajat->id === 3) // Andrei Dima Administrator 3
                                                    || ($angajat->id === 4) // Mocanu Geanina
                                                    || ($angajat->id === 12) // Duna Luminita
                                                    // || ($angajat->id === 91) // Porchina Luminita
                                                )
                                            @else
                                                {{ $angajat->cod_de_acces }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $angajat->sectia }}
                                        </td>
                                        <td>
                                            {{ $angajat->firma }}
                                        </td>
                                        <td>
                                            {{ $angajat->foaie_pontaj }}
                                        </td>
                                        <td>
                                            {{ $angajat->ore_angajare }}
                                        </td>
                                        <td>
                                            @if ($angajat->activ === 1)
                                                <small class="text-success">Deschis</small>
                                            @else
                                                <small class="text-danger">Închis</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
