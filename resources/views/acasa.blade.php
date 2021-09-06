@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Pagina principală</div>

                <div class="card-body">
                    @include ('errors')

                    Bine ai venit <b>{{ auth()->user()->name ?? '' }}</b>!
                    <br><br><br>
                        <p class="text-center">
                            {{-- {{ \Illuminate\Foundation\Inspiring::quote() }} --}}
                        </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
