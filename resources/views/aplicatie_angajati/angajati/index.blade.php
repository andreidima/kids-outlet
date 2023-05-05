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

                @foreach ($angajati->sortByDesc('activ')->groupBy('activ') as $angajati_per_activ)

                    {{-- Daca sunt activi, se afiseaza pe fiecare prod in parte --}}
                    @if($angajati_per_activ->first()->activ == 1)
                        @foreach ($angajati_per_activ->sortBy('prod')->groupBy('prod') as $angajati_per_prod)
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
                                                Firma
                                            </th>
                                            <th>
                                                Foaie pontaj
                                            </th>
                                            <th>
                                                Ore angajare
                                            </th>
                                            {{-- <th>

                                            </th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($angajati_per_prod->sortBy('nume') as $angajat)
                                            <tr>
                                                <td>
                                                    {{-- <a class="btn btn-primary border border-dark rounded-3" href="/aplicatie-angajati/angajati/{{ $angajat->id }}/modifica" role="button">
                                                        <i class="fas fa-edit text-white"></i>
                                                    </a> --}}
                                                    <a class="" href="/aplicatie-angajati/angajati/{{ $angajat->id }}/modifica" role="button">
                                                        {{-- <i class="fas fa-edit text-white"></i> --}}
                                                        {{ $angajat->nume }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{-- Conturilor pentru care se afiseaza codurile de acces --}}
                                                    @if (
                                                            ($angajat->id === 1) // Andrei Dima Administrator 1
                                                            || ($angajat->id === 3) // Andrei Dima Administrator 3
                                                            || ($angajat->id === 4) // Mocanu Geanina
                                                            || ($angajat->id === 12) // Duna Luminita
                                                            || ($angajat->id === 91) // Porchina Luminita
                                                            || ($angajat->id === 162) // Toader Maria
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
                                                {{-- <td>
                                                    <a class="btn btn-primary border border-dark rounded-3" href="/aplicatie-angajati/angajati/{{ $angajat->id }}/modifica" role="button">
                                                        <i class="fas fa-edit text-white"></i>
                                                    </a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    @endforeach

                    {{-- Daca NU sunt activi, se afiseaza pe fiecare la gramada --}}
                    @else
                            <h3 class="text-center">
                                Conturi închise
                            </h3>
                        {{-- Conturi închise --}}

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
                                                Firma
                                            </th>
                                            <th>
                                                Foaie pontaj
                                            </th>
                                            <th>
                                                Ore angajare
                                            </th>
                                            {{-- <th>
                                                Stare cont
                                            </th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($angajati_per_activ->sortBy('nume') as $angajat)
                                            <tr>
                                                <td>
                                                    <a class="" href="/aplicatie-angajati/angajati/{{ $angajat->id }}/modifica" role="button">
                                                        {{ $angajat->nume }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{-- Conturilor pentru care se afiseaza codurile de acces --}}
                                                    @if (
                                                            ($angajat->id === 1) // Andrei Dima Administrator 1
                                                            || ($angajat->id === 3) // Andrei Dima Administrator 3
                                                            || ($angajat->id === 4) // Mocanu Geanina
                                                            || ($angajat->id === 12) // Duna Luminita
                                                            || ($angajat->id === 91) // Porchina Luminita
                                                            || ($angajat->id === 162) // Toader Maria
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
                                                {{-- <td>
                                                    @if ($angajat->activ === 1)
                                                        <small class="text-success">Deschis</small>
                                                    @else
                                                        <small class="text-danger">Închis</small>
                                                    @endif
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    @endif
                @endforeach

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>
@endsection
