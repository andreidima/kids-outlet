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

                @isset($norme_lucrate)
                    <table class="table table-bordered table-dark table-striped">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center">
                                        {{ $norme_lucrate->first()->produs_operatie->produs->nume ?? '' }}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-center">
                                        {{ $norme_lucrate->first()->produs_operatie->numar_de_faza ?? '' }} - {{ $norme_lucrate->first()->produs_operatie->nume ?? '' }}
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Persoana
                                    </th>
                                    <th>
                                        Cantitate
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($norme_lucrate as $norma_lucrata)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $norma_lucrata->angajat->nume ?? '' }}
                                        </td>
                                        <td class="text-end">
                                            {{ $norma_lucrata->cantitate }}
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                    </table>
                @endisset

                <a class="btn btn-lg btn-secondary w-100" href="{{ $return_url }}" style="border:2px solid white;">ÎNAPOI</a>
            </div>
        </div>
    </div>
@endsection
