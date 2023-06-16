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
                                    <th colspan="4" class="text-center">
                                        {{ $norme_lucrate->first()->produs_operatie->produs->nume ?? '' }}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-center">
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
                                    <th class="text-end">
                                        Acțiune
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
                                        <td class="text-end">
                                            @if (
                                                    (
                                                        \Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth()
                                                        ||
                                                        (
                                                            \Carbon\Carbon::parse($norma_lucrata->data)->isLastMonth()
                                                            &&
                                                            \Carbon\Carbon::now()->day <= 14
                                                        )
                                                    )
                                                    &&
                                                    ($angajat->id !== 12) // Duna Luminita
                                                )
                                                <a
                                                    href="#"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stergeComanda{{ $norma_lucrata->id }}"
                                                    title="Șterge Comanda"
                                                    class="btn btn-sm text-white"
                                                    style="background-color: #FC4A1A; border:1px solid white;"
                                                    >
                                                        ȘTERGE
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                    </table>
                @endisset

                {{-- <a class="btn btn-lg btn-secondary w-100" href="{{ $return_url }}" style="border:2px solid white;">ÎNAPOI</a> --}}
                <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/vezi-faze-produse/{{ $norma_lucrata->produs_operatie->produs->id ?? ''}}" style="border:2px solid white;">ÎNAPOI</a>
            </div>
        </div>
    </div>


    {{-- Modalele pentru stergere --}}
    @foreach ($norme_lucrate as $norma_lucrata)
        @if (
                (
                    \Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth()
                    ||
                    (
                        \Carbon\Carbon::parse($norma_lucrata->data)->isLastMonth()
                        &&
                        \Carbon\Carbon::now()->day <= 14
                    )
                )
                &&
                (!$angajat->id !== 12) // Duna Luminita
            )
                <div class="modal fade text-dark" id="stergeComanda{{ $norma_lucrata->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white" id="exampleModalLabel">
                                Angajat: <b>{{ $norma_lucrata->angajat->nume ?? '' }}</b>
                                <br>
                                Produs: <b>{{ $norma_lucrata->produs_operatie->produs->nume }}</b>
                                <br>
                                Operație: <b>{{ $norma_lucrata->produs_operatie->nume }}</b>
                                <br>
                                Număr de bucăți: <b>{{ $norma_lucrata->cantitate }}</b>
                            </h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="text-align:left;">
                            Ești sigur ca vrei să ștergi Comanda?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                RENUNȚĂ
                            </button>

                            <a class="btn btn-danger text-white" href="/aplicatie-angajati/cont-sef-sectie/norma-lucrata/{{ $norma_lucrata->id }}/sterge" role="button">
                                ȘTERGE COMANDA
                            </a>

                        </div>
                        </div>
                    </div>
                </div>
        @endif
    @endforeach


@endsection
