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

                @if (!session()->has('status') && !session()->has('warning'))
                    Se va muta tot lucrul introdus luna aceasta, din intervalul
                    <b>{{ \Carbon\Carbon::today()->startOfMonth()->isoFormat('DD.MM.YYYY') }} - {{ \Carbon\Carbon::today()->startOfMonth()->addDays(14)->isoFormat('DD.MM.YYYY') }}</b>
                    , pe luna anterioară, respectiv pe data de <b>{{ \Carbon\Carbon::today()->subMonthNoOverflow()->endOfMonth()->isoFormat('DD.MM.YYYY') }}</b>

                    <br><br>
                    Numărul total de „norme lucrate” ce poate fi mutat este de <b>{{ $norme_lucrate->count() }}</b>
                    <br><br>
                    <form class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/muta-lucrul-pe-luna-anterioara" autocomplete="off">
                        @csrf
                        <button class="px-0 mb-0 btn btn-lg w-100 text-white" style="background-color: #FC4A1A; border:2px solid white;"
                            type="submit" role="button" name="action" value="mutaLucrul">
                            MUTĂ LUCRUL
                        </button>
                    </form>
                @endif

                <br><br>

                <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/meniul-principal" style="border:2px solid white;">ÎNAPOI</a>
            </div>
        </div>
    </div>
@endsection
