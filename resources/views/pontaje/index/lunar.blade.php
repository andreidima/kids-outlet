@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
    <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
        <div class="col-lg-6">
            <h4 class="mb-0"><a href="{{ route('pontaje.afisare_lunar') }}">
                <i class="fas fa-user-clock me-1"></i>Pontaje</a> /
                {{-- {{ \Carbon\Carbon::parse($search_data)->isoFormat('MMMM YYYY') }} --}}
                {{ \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') ?? '' }}
                -
                {{ \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') ?? '' }}
            </h4>
        </div>
        <div class="col-lg-6" id="app1">
            <form class="needs-validation" novalidate method="GET" action="{{ route('pontaje.afisare_lunar') }}">
                @csrf
                <div class="row mb-1 input-group custom-search-form justify-content-center">
                    <div class="col-lg-6">
                        <input type="text" class="form-control form-control-sm me-1 border rounded-pill" id="search_nume" name="search_nume" placeholder="Nume" autofocus
                                value="{{ $search_nume }}">
                    </div>
                    <div class="col-lg-6 d-flex">
                        <label for="search_data" class="mb-0 align-self-center me-1">Interval:</label>
                        <vue2-datepicker
                            data-veche="{{ $search_data_inceput }}"
                            nume-camp-db="search_data_inceput"
                            tip="date"
                            latime="100"
                            value-type="YYYY-MM-DD"
                            format="DD-MM-YYYY"
                            :latime="{ width: '125px' }"
                        ></vue2-datepicker>
                        <vue2-datepicker
                            data-veche="{{ $search_data_sfarsit }}"
                            nume-camp-db="search_data_sfarsit"
                            tip="date"
                            latime="150"
                            value-type="YYYY-MM-DD"
                            format="DD-MM-YYYY"
                            :latime="{ width: '125px' }"
                        ></vue2-datepicker>
                    </div>
                </div>
                <div class="row input-group custom-search-form justify-content-center">
                    <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                        <i class="fas fa-search text-white me-1"></i>Caută
                    </button>
                    <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ route('pontaje.afisare_lunar') }}" role="button">
                        <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                    </a>
                </div>
            </form>
        </div>
    </div>
{{--
    <div class="card-body px-0 py-3">

        @include ('errors')

        <div class="table-responsive rounded mb-4">
            <table class="table table-striped table-hover table-sm rounded table-bordered">
                <thead class="text-white rounded" style="background-color:#e66800;">
                    <tr class="" style="padding:2rem">
                        <th style="min-width: 50px;">Nr. Crt.</th>
                        <th style="min-width: 150px;">Nume</th>
                        @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                            <th class="text-center" style="min-width: 120px;">
                                {{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD.MM.YYYY') }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pontaje->groupBy('angajat.nume') as $pontaje_per_persoana)
                        <tr>
                            <td align="">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $pontaje_per_persoana->first()->angajat->nume ?? '' }}
                            </td>

                            @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                <td class="text-center">
                                    @forelse ($pontaje_per_persoana->groupBy('data') as $pontaje_per_persoana_per_zi)
                                        @php
                                            $timp_total = \Carbon\Carbon::today();
                                        @endphp
                                        @forelse ($pontaje_per_persoana_per_zi as $pontaj)
                                            @if (\Carbon\Carbon::parse($pontaj->data) == \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua))

                                                @if ($loop->iteration > 1)
                                                    <br>
                                                @endif
                                                {{ $pontaj->ora_sosire ? \Carbon\Carbon::parse($pontaj->ora_sosire)->isoFormat('HH:mm') : '' }}
                                                -
                                                {{ $pontaj->ora_plecare ? \Carbon\Carbon::parse($pontaj->ora_plecare)->isoFormat('HH:mm') : '' }}

                                                @if ($pontaj->ora_sosire && $pontaj->ora_plecare)
                                                    @php
                                                        $ora_sosire = new \Carbon\Carbon($pontaj->ora_sosire);
                                                        $ora_plecare = new \Carbon\Carbon($pontaj->ora_plecare);

                                                        $timp_in_minute = $ora_plecare->diffInMinutes($ora_sosire);

                                                        $timp_total->addMinutes($timp_in_minute);
                                                    @endphp
                                                @endif
                                            @endif
                                        @empty
                                        @endforelse

                                        @if ($timp_total->diffInMinutes(\Carbon\Carbon::today()) > 0)
                                            <br>
                                            Total:
                                            <b>
                                            {{ \Carbon\Carbon::parse($timp_total->diffInSeconds(\Carbon\Carbon::today()))->isoFormat('HH:mm') }}
                                            </b>
                                        @endif
                                    @empty
                                    @endforelse
                                </td>
                            @endfor
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$pontaje->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

    </div> --}}


    <div class="card-body px-0 py-3">

        @include ('errors')

        <div class="table-responsive rounded mb-4">
            <table class="table table-striped table-hover table-sm rounded table-bordered">
                <thead class="text-white rounded" style="background-color:#e66800;">
                    <tr class="" style="padding:2rem">
                        <th style="min-width: 50px;">Nr. Crt.</th>
                        <th style="min-width: 150px;">Nume</th>
                        @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                            <th class="text-center" style="min-width: 120px;">
                                {{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD.MM.YYYY') }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($angajati as $angajat)
                        <tr>
                            <td style="">
                                {{ $loop->iteration }}
                            </td>
                            <td style="">
                                <div class="px-2"
                                style="
    position: absolute;
    display: inline-block;
    width: 140px;
    background-color:#e66800;
    color:white;
    "
    >
                                    {{ $angajat->nume ?? '' }}
                                </div>
                            </td>

                            @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                <td class="text-center">
                                    @forelse ($angajat->pontaj->groupBy('data') as $pontaj_per_data)
                                        @php
                                            $timp_total = \Carbon\Carbon::today();
                                        @endphp

                                        @forelse ($pontaj_per_data as $pontaj)
                                            @if (\Carbon\Carbon::parse($pontaj->data) == \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua))

                                                @if ($loop->iteration > 1)
                                                    <br>
                                                @endif
                                                {{ $pontaj->ora_sosire ? \Carbon\Carbon::parse($pontaj->ora_sosire)->isoFormat('HH:mm') : '' }}
                                                -
                                                {{ $pontaj->ora_plecare ? \Carbon\Carbon::parse($pontaj->ora_plecare)->isoFormat('HH:mm') : '' }}

                                                @if ($pontaj->ora_sosire && $pontaj->ora_plecare)
                                                    @php
                                                        $ora_sosire = new \Carbon\Carbon($pontaj->ora_sosire);
                                                        $ora_plecare = new \Carbon\Carbon($pontaj->ora_plecare);

                                                        $timp_in_minute = $ora_plecare->diffInMinutes($ora_sosire);

                                                        $timp_total->addMinutes($timp_in_minute);
                                                    @endphp
                                                @endif
                                            @endif
                                        @empty
                                        @endforelse

                                            @if ($timp_total->diffInMinutes(\Carbon\Carbon::today()) > 0)
                                                <br>
                                                {{-- Total: --}}
                                                <span class="badge bg-success">
                                                {{ \Carbon\Carbon::parse($timp_total->diffInSeconds(\Carbon\Carbon::today()))->isoFormat('HH:mm') }}
                                                </span>
                                            @endif
                                    @empty
                                    @endforelse
                                </td>
                            @endfor
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$angajati->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

    </div>


</div>

    {{-- Modalele pentru stergere pontaj --}}
    {{-- @foreach ($pontaje as $pontaj)
        <div class="modal fade text-dark" id="stergePontaj{{ $pontaj->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Pontaj: <b>{{ $pontaj->angajat->nume ?? '' }}</b></h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:left;">
                    Ești sigur ca vrei să ștergi Pontajul?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                    <form method="POST" action="{{ $pontaj->path() }}">
                        @method('DELETE')
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-danger text-white"
                            >
                            Șterge Pontaj
                        </button>
                    </form>

                </div>
                </div>
            </div>
        </div>
    @endforeach --}}

@endsection
