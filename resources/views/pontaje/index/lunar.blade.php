@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
    <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
        <div class="col-lg-5">
            <h4 class="mb-2">
                <i class="fas fs-4 fa-user-clock me-1"></i>Pontaje /
            {{-- </h4>
            <h4 class="mb-0"> --}}
                {{ \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') ?? '' }}
                -
                {{ \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') ?? '' }}
            </h4>
            <div>
                <form class="needs-validation" novalidate method="GET" action="{{ route('pontaje.afisare_lunar') }}">
                    @csrf
                </form>
            </div>
        </div>
        <div class="col-lg-7" id="app1">
            <form class="needs-validation" novalidate method="GET" action="{{ route('pontaje.afisare_lunar') }}">
                @csrf
                <div class="row mb-1 input-group custom-search-form justify-content-center">
                    <div class="col-lg-6">
                        <input type="text" class="form-control form-control-sm me-1 border rounded-3" id="search_nume" name="search_nume" placeholder="Nume"
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
                <div class="row mb-2 input-group custom-search-form justify-content-center">
                    <button class="btn btn-sm btn-primary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" type="submit"
                        name="action" value="cautare">
                        <i class="fas fa-search text-white me-1"></i>Caută
                    </button>
                    <a class="btn btn-sm bg-secondary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" href="{{ route('pontaje.afisare_lunar') }}" role="button">
                        <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                    </a>
                </div>
                <div class="row input-group custom-search-form justify-content-center">
                    <button class="btn btn-sm btn-primary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" type="submit"
                        name="action" value="saptamana_anterioara">
                        << Săptămâna anterioară
                    </button>
                    <button class="btn btn-sm btn-primary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" type="submit"
                        name="action" value="saptamana_urmatoare">
                        Săptămâna următoare >>
                    </button>
                    {{-- <button class="btn btn-sm btn-danger text-white col-md-3 mx-1 border border-dark rounded-3 shadow" type="submit"
                        name="action" value="export_pdf">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </button> --}}
                    <button class="btn btn-sm btn-danger text-white col-md-3 mx-1 border border-dark rounded-3 shadow" type="submit"
                        name="action" value="export_excel">
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
        {{-- <div class="col-lg-2 text-lg-end">
            <a class="btn btn-sm bg-success text-white border border-dark rounded-3 shadow" href="{{ route('pontaje.create') }}" role="button">
                <i class="fas fa-plus-square text-white me-1"></i>Adaugă pontaj
            </a>
        </div> --}}
    </div>


    <div class="card-body px-0 py-3">

        @include ('errors')

        <div class="table-responsive rounded mb-4">
            <table class="table table-striped table-hover table-sm rounded table-bordered">
                <thead class="text-white rounded" style="background-color:#e66800;">
                    <tr class="" style="padding:2rem">
                        <th style="min-width: 50px;">#</th>
                        <th style="min-width: 170px;">Nume</th>
                        @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                            <th class="text-center" style="min-width: 120px;">
                                {{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD.MM.YYYY') }}
                            </th>
                        @endfor
                        {{-- <th class="text-center" style="min-width: 120px;">
                            Total
                        </th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($angajati as $angajat)
                        {{-- @php
                            $timp_total = \Carbon\Carbon::today();
                        @endphp --}}
                        <tr>
                            <td style="">
                                {{ $loop->iteration }}
                            </td>
                            <td style="">
                                <div class="px-2"
                                style="
    position: absolute;
    display: inline-block;
    width: 160px;
    background-color:#e66800;
    color:white;
    "
    >
                                    {{ $angajat->nume ?? '' }}
                                </div>
                            </td>

                            @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                <td class="text-center">
                                    {{-- @if (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday()) --}}
                                        @forelse ($angajat->pontaj->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString()) as $pontaj)
                                            <a href="/pontaje/{{ $pontaj->id }}/modifica" style="text-decoration: none;">
                                                @switch($pontaj->concediu)
                                                    @case(0)
                                                        @if ($pontaj->ora_sosire && $pontaj->ora_plecare)
                                                            {{-- @php
                                                                $timp_total->addSeconds(\Carbon\Carbon::parse($pontaj->ora_plecare)->diffInSeconds(\Carbon\Carbon::parse($pontaj->ora_sosire)))
                                                            @endphp --}}
                                                            {{-- {{
                                                                \Carbon\Carbon::parse(
                                                                    \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInSeconds(\Carbon\Carbon::parse($pontaj->ora_sosire))
                                                                )->isoFormat('HH:mm')
                                                            }} --}}
                                                            @php
                                                                // $numar_de_ore = round(
                                                                //     \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInMinutes(\Carbon\Carbon::parse($pontaj->ora_sosire))
                                                                //     / 60 )
                                                                $numar_de_ore = \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire))
                                                            @endphp
                                                            @if ($numar_de_ore < 8)
                                                                {{ $numar_de_ore }}
                                                            @else
                                                                8
                                                            @endif
                                                            {{-- @switch (\Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire)))
                                                                @case(0)
                                                                @case(1)
                                                                @case(2)
                                                                        2
                                                                    @break
                                                                @case(3)
                                                                @case(4)
                                                                @case(5)
                                                                        4
                                                                    @break
                                                                @case(6)
                                                                @case(7)
                                                                @case(8)
                                                                @case(9)
                                                                @case(10)
                                                                @case(11)
                                                                @case(12)
                                                                        8
                                                                    @break
                                                                @default
                                                                        {{ \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire)) }}
                                                                    @break
                                                            @endswitch --}}
                                                        @else
                                                            <span class="text-danger">0</span>
                                                        @endif
                                                        @break
                                                    @case(1)
                                                            M
                                                        @break
                                                    @case(2)
                                                            O
                                                        @break
                                                    @case(3)
                                                            Î
                                                        @break
                                                    @case(4)
                                                            N
                                                        @break
                                                @endswitch
                                            </a>
                                        @empty
                                            <a href="/pontaje/{{ $angajat->id }}/{{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}/adauga">
                                                <i class="fas fa-plus-square"></i>
                                            </a>
                                        @endforelse
                                    {{-- @endif --}}
                                </td>
                            @endfor
                            {{-- <td class="text-center">
                                {{
                                    number_format(\Carbon\Carbon::parse($timp_total)->floatDiffInHours(\Carbon\Carbon::today()), 4)
                                }}
                            </td> --}}
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
                {{-- <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$angajati->appends(Request::except('page'))->links()}}
                    </ul>
                </nav> --}}

    </div>


</div>

@endsection
