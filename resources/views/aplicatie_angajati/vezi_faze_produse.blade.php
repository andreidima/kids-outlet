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

                <h4 class="mb-5"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                @include('errors')

                <h4 class="text-center">ALEGE PRODUSUL</h4>

                @foreach ($produse as $produs)
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/vezi-faze-produse/{{ $produs->id }}" role="button" style="background-color: #FC4A1A; border:2px solid white;">{{ $produs->nume }}</a>
                @endforeach

                @isset($produse_operatii)
                    <table class="table table-bordered table-dark table-striped">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center">
                                        {{ $produse_operatii->first()->produs->nume }}
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        Faza
                                    </th>
                                    <th>
                                        Nume operație
                                    </th>
                                    <th>
                                        Introdus
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produse_operatii as $operatie)
                                    <tr>
                                        <td>
                                            {{ $operatie->numar_de_faza }}
                                        </td>
                                        <td>
                                            {{ $operatie->nume }}
                                        </td>
                                        <td class="text-end">
                                            {{ $operatie->norma_totala_efectuata }}
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                    </table>
                @endisset

                <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/meniul-principal" style="border:2px solid white;">ÎNAPOI</a>
            </div>
        </div>
    </div>
@endsection
