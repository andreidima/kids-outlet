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

                <h4 class="mb-4"><small>Bun venit</small> <b>{{ $angajat->nume }}</b></h4>

                @include('errors')

                <h4 class="mb-4 text-center"><b>REALIZAT</b>  </h4>

                <div class="row text-center mb-4 mx-0" id="app1">
                    <form class="needs-validation" novalidate method="GET" action="{{ route('aplicatie_angajati.realizat') }}">
                        @csrf
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <label for="search_data_inceput" class="mb-0 align-self-center me-1">De la:</label>
                                    <vue2-datepicker
                                        data-veche="{{ $search_data_inceput }}"
                                        nume-camp-db="search_data_inceput"
                                        tip="date"
                                        value-type="YYYY-MM-DD"
                                        format="DD-MM-YYYY"
                                        :latime="{ width: '125px' }"
                                    ></vue2-datepicker>

                            </div>
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <label for="search_data_sfarsit" class="mb-0 align-self-center me-1">P??n?? la:</label>
                                    <vue2-datepicker
                                        data-veche="{{ $search_data_sfarsit }}"
                                        nume-camp-db="search_data_sfarsit"
                                        tip="date"
                                        value-type="YYYY-MM-DD"
                                        format="DD-MM-YYYY"
                                        :latime="{ width: '125px' }"
                                    ></vue2-datepicker>
                            </div>
                            <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <button class="btn btn-lg w-100 text-white"
                                    type="submit"
                                    style="background-color: #FC4A1A; border:2px solid white;">
                                    <i class="fas fa-search text-white me-1"></i>Caut??
                                </button>
                                {{-- <a class="btn btn-lg w-100 text-white"
                                    role="button"
                                    type="submit"
                                    style="background-color: #FC4A1A; border:2px solid white;">
                                    <i class="fas fa-search text-white me-1"></i>CAUT??
                                </a> --}}
                            </div>
                    </form>
                </div>

                {{-- @php
                    $suma_totala = 0;
                @endphp
                @forelse ($norme_lucrate->groupBy('numar_de_faza') as $norme_lucrate_per_numar_de_faza)
                    @php
                        $suma_totala += $norme_lucrate_per_numar_de_faza->sum('cantitate') * $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret
                    @endphp
                    <div class="mb-4 px-1" style="background-color:#007e6b;">
                        <small>Num??r de faz??:</small> {{ $norme_lucrate_per_numar_de_faza->first()->numar_de_faza }}
                        <br>
                        <small>Num??r de buc????i ??n total:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') }}
                        <br>
                        <small>Pre?? pe bucat??:</small> {{ $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
                        <br>
                        <small>Suma total??:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') * $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
                    </div>
                @empty
                @endforelse

                <h4 class="mb-4 text-center">
                    <b>
                        REALIZAT TOTAL:
                        <br>
                        {{ $suma_totala }} lei
                    </b>
                </h4> --}}

                {{-- @php
                    $data_stergere_lucru_pana_la = \Carbon\Carbon::parse(\App\Models\Variabila::where('variabila', 'data_stergere_lucru_pana_la')->value('valoare'));
                @endphp --}}
                @forelse ($norme_lucrate->groupBy('data') as $norme_lucrate_per_data)
                    @forelse ($norme_lucrate_per_data as $norma_lucrata)
                        <div class="mb-4 px-1 rounded-3" style="background-color:#007e6b;">
                            <small>Data:</small> {{ $norma_lucrata->data ? \Carbon\Carbon::parse($norma_lucrata->data)->isoFormat('DD.MM.YYYY') : '' }}
                            <br>
                            <small>Produs:</small> {{ $norma_lucrata->produs_operatie->produs->nume }}
                            <br>
                            <small>Num??r de faz??:</small> {{ $norma_lucrata->produs_operatie->numar_de_faza }}
                            <br>
                            <small>Opera??ie:</small> {{ $norma_lucrata->produs_operatie->nume }}
                            <br>
                            <small>Num??r de buc????i:</small> {{ $norma_lucrata->cantitate }}
                            @if (
                                    (
                                        (\Carbon\Carbon::now()->day < 6)
                                        // (\Carbon\Carbon::parse('2022-06-06')->day < 6)
                                        &&
                                        ($norma_lucrata->data >= \Carbon\Carbon::now()->subMonthsNoOverflow(1)->startOfMonth()->toDateString())
                                        // ($norma_lucrata->data >= \Carbon\Carbon::parse('2022-06-06')->subMonthsNoOverflow(1)->startOfMonth()->toDateString())
                                    )
                                    ||
                                    (
                                        (\Carbon\Carbon::now()->day >= 6)
                                        // (\Carbon\Carbon::parse('2022-06-06')->day >= 6)
                                        &&
                                        ($norma_lucrata->data >= \Carbon\Carbon::now()->startOfMonth()->toDateString())
                                        // ($norma_lucrata->data >= \Carbon\Carbon::parse('2022-06-06')->startOfMonth()->toDateString())
                                    )
                                )
                            {{-- @if ($data_stergere_lucru_pana_la->lessThan(\Carbon\Carbon::parse($norma_lucrata->data))) --}}
                            {{-- @if (\Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth()) --}}
                                    <br>
                                    <div class="text-start">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergeComanda{{ $norma_lucrata->id }}"
                                            title="??terge Comanda"
                                            class="btn btn-sm text-white"
                                            style="background-color: #FC4A1A; border:1px solid white;"
                                            >
                                            {{-- <span class="badge bg-danger"> --}}
                                                ??TERGE COMANDA
                                            {{-- </span> --}}
                                        </a>
                                    </div>
                            @endif
                        </div>
                    @empty
                    @endforelse
                @empty
                @endforelse

                <div class="mb-4 px-1 text-dark rounded-3" style="background-color:#d5ff88;">
                    {{-- Dac?? a??i introdus comenzi gre??ite, ave??i disponibil butonul de ??tergere p??n?? ??n ziua de 5 (inclusiv) a lunii urm??toare. --}}
                    {{-- Nu pute??i ??terge comenzi mai vechi de {{ $data_stergere_lucru_pana_la->isoFormat("DD.MM.YYYY") }} inclusiv. --}}
                    Pute??i ??terge comenzi doar din luna curent??.
                </div>

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

            </div>
        </div>
    </div>

    {{-- Modalele pentru stergere --}}
    @foreach ($norme_lucrate as $norma_lucrata)
        {{-- @if (
                (
                    (\Carbon\Carbon::now()->day < 6)
                    &&
                    ($norma_lucrata->data >= \Carbon\Carbon::now()->subMonthsNoOverflow(1)->startOfMonth()->toDateString())
                )
                ||
                (
                    (\Carbon\Carbon::now()->day >= 6)
                    &&
                    ($norma_lucrata->data >= \Carbon\Carbon::now()->startOfMonth()->toDateString())
                )
            ) --}}
        @if (\Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth())
                <div class="modal fade text-dark" id="stergeComanda{{ $norma_lucrata->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white" id="exampleModalLabel">
                                Produs: <b>{{ $norma_lucrata->produs_operatie->produs->nume }}</b>
                                <br>
                                Opera??ie: <b>{{ $norma_lucrata->produs_operatie->nume }}</b>
                                <br>
                                Cantitate: <b>{{ $norma_lucrata->cantitate }}</b>
                            </h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="text-align:left;">
                            E??ti sigur ca vrei s?? ??tergi Comanda?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renun????</button>

                            <a class="btn btn-danger text-white" href="/aplicatie-angajati/norma-lucrata/{{ $norma_lucrata->id }}/sterge" role="button">??TERGE COMANDA</a>

                        </div>
                        </div>
                    </div>
                </div>
        @endif
    @endforeach


@endsection
