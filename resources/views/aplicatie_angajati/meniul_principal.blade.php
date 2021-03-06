@extends('layouts.app')

@section('content')
    {{-- <div class="container-fluid vh-100 py-2" style="background-color: #DFDCE3;">
        <div class="row p-2 vh-100 align-items-center"> --}}
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

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                {{ $angajat->numar_de_faza }}
                <br>
                {{ $angajat->numar_de_bucati }}

                @include('errors')

                {{-- @php
                    dd($angajat->roluri->first());
                @endphp --}}
                {{-- @if ($angajat->hasRol('pontaj'))
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj" role="button" style="background-color: #FC4A1A; border:2px solid white;">PONTAJ</a>
                @endif --}}
                @if ($angajat->angajati_de_pontat->count() > 0 )
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj" role="button" style="background-color: #FC4A1A; border:2px solid white;">PONTAJ</a>
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/pontaj-verifica" role="button" style="background-color: #FC4A1A; border:2px solid white;">VERIFIC?? PONTAJ</a>
                @endif


                {{-- Borchina Liliana nu poate introduce comenzi --}}
                @if (
                    !($angajat->id === 91) // Borchina Liliana
                )
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/adauga-comanda-pasul-1" role="button" style="background-color: #FC4A1A; border:2px solid white;">COMAND??</a>
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/realizat" role="button" style="background-color: #FC4A1A; border:2px solid white;">REALIZAT</a>
                @endif

                {{-- Conturile ce pot vedea si fazele si ce s-a introdus la fiecare --}}
                @if (
                        ($angajat->id === 1) // Andrei Dima Administrator 1
                        || ($angajat->id === 3) // Andrei Dima Administrator 3
                        || ($angajat->id === 4) // Mocanu Geanina
                        || ($angajat->id === 12) // Duna Luminita
                        || ($angajat->id === 91) // Borchina Liliana
                    )
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/vezi-faze-produse" role="button" style="background-color: #FC4A1A; border:2px solid white;">VEZI FAZE PRODUSE</a>
                @endif

                {{-- Conturile ce poate vedea angajatii --}}
                @if (
                        ($angajat->id === 4) // Mocanu Geanina
                        || ($angajat->id === 12) // Duna Luminita
                        || ($angajat->id === 91) // Borchina Liliana
                    )
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/angajati" role="button" style="background-color: #FC4A1A; border:2px solid white;">ANGAJA??I</a>
                @endif

                {{-- Contul Mocanu Geanina poate bloca introducerea comenzilor --}}
                {{-- @if (
                        ($angajat->id === 4) // Mocanu Geanina
                    )
                    @php
                        $acces_introducere_comenzi = \App\Models\Variabila::where('variabila', 'acces_introducere_comenzi')->value('valoare');
                    @endphp
                    <a class="mb-3 btn btn-lg w-100 text-white"
                        href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#blocheazaDeblocheazaIntroducereComenzi"
                        role="button" style="background-color: #FC4A1A; border:2px solid white;">

                        @if ($acces_introducere_comenzi === 'da')
                            BLOCHEAZ?? INTRODUCERE COMENZI
                        @else
                            ACTIVEAZ?? INTRODUCERE COMENZI
                        @endif
                    </a>

                        <div class="modal fade text-dark" id="blocheazaDeblocheazaIntroducereComenzi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white" id="exampleModalLabel">
                                        @if (\App\Models\Variabila::where('variabila', 'acces_introducere_comenzi')->value('valoare') === 'da')
                                            Blocheaz?? introducere comenzi
                                        @else
                                            Activeaz?? introducere comenzi
                                        @endif
                                    </h5>
                                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align:left;">
                                        @if (\App\Models\Variabila::where('variabila', 'acces_introducere_comenzi')->value('valoare') === 'da')
                                            E??ti sigur ca vrei s?? blochezi introducerea de comenzi?
                                            <br>
                                            Angaja??ii nu vor mai putea introduce lucrul ??n aplica??ie
                                        @else
                                            E??ti sigur ca vrei s?? activezi introducerea de comenzi?
                                            <br>
                                            Angaja??ii vor putea introduce lucrul ??n aplica??ie
                                        @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renun????</button>
                                    <a class="btn btn-danger text-white" href="/aplicatie-angajati/blocheaza-deblocheaza-introducere-comenzi" role="button">
                                        @if (\App\Models\Variabila::where('variabila', 'acces_introducere_comenzi')->value('valoare') === 'da')
                                            Blocheaz??
                                        @else
                                            Activeaz??
                                        @endif
                                    </a>
                                </div>
                                </div>
                            </div>
                        </div>

                @endif --}}


            </div>
        </div>
    </div>
@endsection
