@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;">
        <div class="row p-2 align-items-center">
            <div class="col-md-6 col-lg-3 p-3 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="">{{ config('app.name', 'Laravel') }}</h4>
                    </div>
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

                <h4 class="alert-success p-2">
                    @if ($angajat->limba_aplicatie === 1)
                        Comanda a fost introdusă cu success!
                    @elseif ($angajat->limba_aplicatie === 2)
                        ඇණවුම සාර්ථකව ඇතුළු විය!
                        <br>
                        Order successfully entered!
                    @endif
                </h4>

                <h4 class="mb-4">
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Produs:
                        @elseif ($angajat->limba_aplicatie === 2)
                            නිෂ්පාදන:
                            /
                            Product:
                        @endif
                    </small> {{ $angajat->produs_nume }}
                    <br>
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Număr de fază:
                        @elseif ($angajat->limba_aplicatie === 2)
                            අදියර අංකය:
                            /
                            Phase number:
                        @endif
                    </small> {{ $angajat->numar_de_faza }}
                    <br>
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Operație:
                        @elseif ($angajat->limba_aplicatie === 2)
                            මෙහෙයුම්:
                            /
                            Operation:
                        @endif
                    </small> {{ $angajat->operatie_nume }}
                    <br>
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Cantitate:
                        @elseif ($angajat->limba_aplicatie === 2)
                            {{-- මෙහෙයුම්:
                            / --}}
                            Amount:
                        @endif
                    </small> {{ $angajat->cantitate }}
                    <br>
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Preț pe bucată:
                        @elseif ($angajat->limba_aplicatie === 2)
                            {{-- මෙහෙයුම්:
                            / --}}
                            Price per piece:
                        @endif
                    </small> {{ $angajat->pret_pe_bucata }}
                    <br>
                    <small>
                        @if ($angajat->limba_aplicatie === 1)
                            Suma totală:
                        @elseif ($angajat->limba_aplicatie === 2)
                            {{-- මෙහෙයුම්:
                            / --}}
                            Total amount:
                        @endif
                    </small> {{ $angajat->cantitate * $angajat->pret_pe_bucata }}
                    {{-- <br>
                    <br>
                    <small>Preț pe bucată:</small> {{ $angajat->pret_pe_bucata }} lei --}}
                </h4>

                @include('errors')

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
@endsection
