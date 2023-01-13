@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white">Norme lucrate / Mută lucrul pe luna anterioară</h6>
                </div>

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                    id="app1"
                >

                @include ('errors')

                {{-- Textul si butonul apar doar daca nu a avut deja loc actiunea de mutare a normelor --}}
                @if (!session()->has('status') && !session()->has('warning'))
                    Se va muta tot lucrul introdus luna aceasta, din intervalul
                    <b>{{ \Carbon\Carbon::today()->startOfMonth()->isoFormat('DD.MM.YYYY') }} - {{ \Carbon\Carbon::today()->startOfMonth()->addDays(14)->isoFormat('DD.MM.YYYY') }}</b>
                    , pe luna anterioară, respectiv pe data de <b>{{ \Carbon\Carbon::today()->subMonthNoOverflow()->endOfMonth()->isoFormat('DD.MM.YYYY') }}</b>

                    <br><br>
                    Numărul total de „norme lucrate” ce poate fi mutat este de <b>{{ $norme_lucrate->count() }}</b>
                    <br><br>
                    <form class="needs-validation text-center" novalidate method="POST" action="/norme-lucrate/muta-lucrul-pe-luna-anterioara" autocomplete="off">
                        @csrf
                        <button class="btn btn-lg btn-success text-white rounded-pill"
                            type="submit" role="button" name="action" value="mutaLucrul">
                            Mută lucrul
                        </button>
                    </form>
                @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
