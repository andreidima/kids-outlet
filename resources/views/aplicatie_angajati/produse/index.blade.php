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

                @include('errors')

                <h4 class="text-center">ALEGE PRODUSUL</h4>

                <small class="mb-3 text-center">
                    * Produsele active sunt colorate cu portocaliu.
                    <br>
                    * Produsele inactive sunt colorate cu gri.
                </small>

                @foreach ($produse as $produs)
                    <a class="mb-3 btn btn-lg w-100 text-white" href="/aplicatie-angajati/produse/{{ $produs->id }}/modifica" role="button"
                        @if ($produs->activ == 1)
                            style="background-color: #FC4A1A; border:2px solid white;"
                        @elseif ($produs->activ == 0)
                            style="background-color: #6c757d; border:2px solid white;"
                        @endif
                    >{{ $produs->nume }}</a>
                @endforeach

                <nav>
                    <ul class="pagination justify-content-center">
                        {{$produse->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

                <br>

                <a class="btn btn-lg btn-secondary w-100" href="/aplicatie-angajati/meniul-principal" style="border:2px solid white;">ÎNAPOI</a>
            </div>
        </div>
    </div>
@endsection
