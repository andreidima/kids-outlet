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
                        <a class="btn btn-sm text-white" href="/aplicatie-angajati/deconectare" role="button" style="background-color: #FC4A1A; border:1px solid white;">
                            @if ($angajat->limba_aplicatie === 1)
                                DECONECTARE
                            @elseif ($angajat->limba_aplicatie === 2)
                                පිටවීම
                                <br>
                                LOGOUT
                            @endif
                        </a>
                    </div>
                </div>


                <div class="mb-2" style="background-color: #000000; height:5px;"></div>


                <h4 class="mb-4">
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Bun venit
                        @elseif ($angajat->limba_aplicatie === 2)
                            සාදරයෙන් පිළිගනිමු
                            /
                            Welcome
                            <br>
                        @endif
                    </small>
                    <b>{{ $angajat->nume }}</b>
                </h4>

                @include('errors')

                <h4 class="mb-4 text-center">
                        @if ($angajat->limba_aplicatie === 1)
                            REALIZAT
                            <br>
                            {{ \Carbon\Carbon::parse($searchData)->isoFormat('MMMM YYYY') }}
                        @elseif ($angajat->limba_aplicatie === 2)
                            සාදන ලදී
                            <br>
                            MAKED
                            <br>
                            {{ \Carbon\Carbon::parse($searchData)->locale('en_EN')->isoFormat('MMMM YYYY') }}
                        @endif
                </h4>

                <div class="row text-center mb-3 mx-0" id="app1">
                    <form class="needs-validation" novalidate method="GET" action="{{ route('aplicatie_angajati.realizat') }}">
                        @csrf
                            {{-- <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <label for="search_data_inceput" class="mb-0 align-self-center me-1">
                                    @if ($angajat->limba_aplicatie === 1)
                                        De la:
                                    @elseif ($angajat->limba_aplicatie === 2)
                                        සිට
                                        /
                                        From
                                    @endif
                                </label>
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
                                <label for="search_data_sfarsit" class="mb-0 align-self-center me-1">
                                    @if ($angajat->limba_aplicatie === 1)
                                        Până la:
                                    @elseif ($angajat->limba_aplicatie === 2)
                                        දක්වා
                                        /
                                        Up to
                                    @endif
                                </label>
                                    <vue2-datepicker
                                        data-veche="{{ $search_data_sfarsit }}"
                                        nume-camp-db="search_data_sfarsit"
                                        tip="date"
                                        value-type="YYYY-MM-DD"
                                        format="DD-MM-YYYY"
                                        :latime="{ width: '125px' }"
                                    ></vue2-datepicker>
                            </div> --}}
                            {{-- <div class="col-lg-12 mb-3 d-flex justify-content-between">
                                <button class="btn btn-lg w-100 text-white"
                                    type="submit"
                                    style="background-color: #FC4A1A; border:2px solid white;">
                                    <i class="fas fa-search text-white me-1"></i>
                                        @if ($angajat->limba_aplicatie === 1)
                                            Caută
                                        @elseif ($angajat->limba_aplicatie === 2)
                                            සොයන්න
                                            <br>
                                            Search
                                        @endif
                                </button>
                            </div> --}}

                            <input type="hidden" name="searchData" value={{ $searchData }}>
                            <div class="col-lg-12 mb-2">
                                <div class="row">
                                    @if ($searchData->month === \Carbon\Carbon::now()->month)
                                        <div class="col-6 m-0 p-0 mx-auto">
                                            <button class="btn btn-lg text-white"
                                                type="submit"
                                                style="background-color: #FC4A1A; border:2px solid white;"
                                                name="action" value="lunaAnterioara"
                                                >
                                                    @if ($angajat->limba_aplicatie === 1)
                                                        << Luna Anterioară
                                                    @elseif ($angajat->limba_aplicatie === 2)
                                                        << Previous Month
                                                    @endif
                                            </button>
                                        </div>
                                    @else
                                        <div class="col-6 m-0 p-0 mx-auto">
                                            <button class="btn btn-lg text-white"
                                                type="submit"
                                                style="background-color: #FC4A1A; border:2px solid white;"
                                                name="action" value="lunaUrmatoare"
                                                >
                                                    @if ($angajat->limba_aplicatie === 1)
                                                        >> Luna Următoare
                                                    @elseif ($angajat->limba_aplicatie === 2)
                                                        >> Next month
                                                    @endif
                                            </button>
                                        </div>
                                    @endif
                                </div>
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
                        <small>Număr de fază:</small> {{ $norme_lucrate_per_numar_de_faza->first()->numar_de_faza }}
                        <br>
                        <small>Număr de bucăți în total:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') }}
                        <br>
                        <small>Preț pe bucată:</small> {{ $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
                        <br>
                        <small>Suma totală:</small> {{ $norme_lucrate_per_numar_de_faza->sum('cantitate') * $norme_lucrate_per_numar_de_faza->first()->produs_operatie->pret }} lei
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


                @php
                    $suma_totala = 0;
                @endphp
                @forelse ($norme_lucrate->groupBy('data') as $norme_lucrate_per_data)
                    @forelse ($norme_lucrate_per_data as $norma_lucrata)
                        <div class="mb-4 px-1 rounded-3" style="background-color:#007e6b;">
                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Data:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    දිනය
                                    /
                                    Date:
                                @endif
                            </small>
                            {{ $norma_lucrata->data ? \Carbon\Carbon::parse($norma_lucrata->data)->isoFormat('DD.MM.YYYY') : '' }}

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Produs:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    නිෂ්පාදන
                                    /
                                    Product:
                                @endif
                            </small>
                            {{ $norma_lucrata->produs_operatie->produs->nume }}

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Număr de fază:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    අදියර අංකය
                                    /
                                    Phase number:
                                @endif
                            </small>
                            {{ $norma_lucrata->produs_operatie->numar_de_faza }}

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Operație:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    මෙහෙයුම්
                                    /
                                    Operation:
                                @endif
                            </small>
                            {{ $norma_lucrata->produs_operatie->nume }}

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Număr de bucăți:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    කෑලි ගණන
                                    /
                                    Number of pieces:
                                @endif
                            </small>
                            {{ $norma_lucrata->cantitate }}

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Preț pe bucată:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    {{-- කෑලි ගණන
                                    / --}}
                                    Price per piece:
                                @endif
                            </small>
                            @if ($norma_lucrata->produs_operatie->pret == 0)
                                @if ($angajat->limba_aplicatie === 1)
                                    preț neintrodus momentan
                                @elseif ($angajat->limba_aplicatie === 2)
                                    price not currently entered
                                @endif
                            @else
                                {{ $norma_lucrata->produs_operatie->pret }} lei
                            @endif

                            <br>

                            <small>
                                @if ($angajat->limba_aplicatie === 1)
                                    Suma totală:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    {{-- කෑලි ගණන
                                    / --}}
                                    Total amount:
                                @endif
                            </small>
                            @if ($norma_lucrata->produs_operatie->pret == 0)
                                @if ($angajat->limba_aplicatie === 1)
                                    preț neintrodus momentan
                                @elseif ($angajat->limba_aplicatie === 2)
                                    price not currently entered
                                @endif
                            @else
                                {{ $norma_lucrata->cantitate * $norma_lucrata->produs_operatie->pret }} lei
                            @endif


                        <br>

                            @if (\Carbon\Carbon::parse($norma_lucrata->data)->isCurrentMonth())
                                    <div class="text-start">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergeComanda{{ $norma_lucrata->id }}"
                                            title="Șterge Comanda"
                                            class="btn btn-sm text-white"
                                            style="background-color: #FC4A1A; border:1px solid white;"
                                            >
                                            {{-- <span class="badge bg-danger"> --}}
                                                @if ($angajat->limba_aplicatie === 1)
                                                    ȘTERGE COMANDA
                                                @elseif ($angajat->limba_aplicatie === 2)
                                                    ඇණවුම මකන්න
                                                    <br>
                                                    DELETE ORDER
                                                @endif
                                            {{-- </span> --}}
                                        </a>
                                    </div>
                            @endif
                        </div>

                        @php
                            $suma_totala += $norma_lucrata->cantitate * $norma_lucrata->produs_operatie->pret;
                        @endphp
                    @empty
                    @endforelse
                @empty
                @endforelse

                <div class="mb-4 px-1 py-2 rounded-3" style="background-color:#003b33;">
                    <h1 class="mb-0 text-center" style="">
                        {{-- <b> --}}
                            @if ($angajat->limba_aplicatie === 1)
                                REALIZAT TOTAL
                            @elseif ($angajat->limba_aplicatie === 2)
                                {{-- ඇණවුම මකන්න
                                <br> --}}
                                ACHIEVED TOTAL AMOUNT
                            @endif
                            <br>
                            {{ $suma_totala }} lei
                        {{-- </b> --}}
                    </h1>
                </div>

                <div class="mb-4 px-1 text-dark rounded-3" style="background-color:#d5ff88;">
                    {{-- Dacă ați introdus comenzi greșite, aveți disponibil butonul de ștergere până în ziua de 5 (inclusiv) a lunii următoare. --}}
                    {{-- Nu puteți șterge comenzi mai vechi de {{ $data_stergere_lucru_pana_la->isoFormat("DD.MM.YYYY") }} inclusiv. --}}

                    @if ($angajat->limba_aplicatie === 1)
                        Puteți șterge comenzi doar din luna curentă.
                    @elseif ($angajat->limba_aplicatie === 2)
                        ඔබට වත්මන් මාසයේ සිට පමණක් ඇණවුම් මැකීමට හැකිය.
                        <br>
                        You can only delete orders from the current month.
                    @endif
                </div>

                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">
                    @if ($angajat->limba_aplicatie === 1)
                        MENIUL PRINCIPAL
                    @elseif ($angajat->limba_aplicatie === 2)
                        ප්රධාන මෙනුව
                        <br>
                        MAIN MENU
                    @endif
                </a>

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
                                @if ($angajat->limba_aplicatie === 1)
                                    Produs:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    නිෂ්පාදන
                                    /
                                    Product:
                                @endif
                                <b>{{ $norma_lucrata->produs_operatie->produs->nume }}</b>

                                <br>
                                @if ($angajat->limba_aplicatie === 1)
                                    Operație:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    මෙහෙයුම්
                                    /
                                    Operation:
                                @endif
                                <b>{{ $norma_lucrata->produs_operatie->nume }}</b>

                                <br>
                                @if ($angajat->limba_aplicatie === 1)
                                    Număr de bucăți:
                                @elseif ($angajat->limba_aplicatie === 2)
                                    කෑලි ගණන
                                    /
                                    Number of pieces:
                                @endif
                                <b>{{ $norma_lucrata->cantitate }}</b>
                            </h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="text-align:left;">
                            @if ($angajat->limba_aplicatie === 1)
                                Ești sigur ca vrei să ștergi Comanda?
                            @elseif ($angajat->limba_aplicatie === 2)
                                ඔබට ඇණවුම මැකීමට අවශ්‍ය බව විශ්වාසද?
                                <br>
                                Are you sure you want to delete the Order?
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                @if ($angajat->limba_aplicatie === 1)
                                    RENUNȚĂ
                                @elseif ($angajat->limba_aplicatie === 2)
                                    අත්හැර දමන්න
                                    <br>
                                    GIVE UP
                                @endif
                            </button>

                            <a class="btn btn-danger text-white" href="/aplicatie-angajati/norma-lucrata/{{ $norma_lucrata->id }}/sterge" role="button">
                                @if ($angajat->limba_aplicatie === 1)
                                    ȘTERGE COMANDA
                                @elseif ($angajat->limba_aplicatie === 2)
                                    ඇණවුම මකන්න
                                    <br>
                                    DELETE ORDER
                                @endif
                            </a>

                        </div>
                        </div>
                    </div>
                </div>
        @endif
    @endforeach


@endsection
