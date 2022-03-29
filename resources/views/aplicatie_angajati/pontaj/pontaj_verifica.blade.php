@extends ('layouts.app')

@section('content')
    <div class="container-fluid" style="background-color: #DFDCE3;">
        <div class="row p-0 align-items-center">
            <div class="col-md-6 col-lg-3 px-0 py-1 mx-auto border border-dark text-white shadow-lg" style="background-color: #4ABDAC;" id="app1">
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

                <h4 class="mb-2">
                    <b>VERIFICĂ PONTAJ</b>
                    <br>
                    {{ \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') ?? '' }}
                    -
                    {{ \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') ?? '' }}
                </h4>

                @include('errors')

                <form class="needs-validation" novalidate method="GET" action="/aplicatie-angajati/pontaj-verifica">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <div class="col-lg-6 d-flex">
                            {{-- <label for="search_data" class="mb-0 align-self-center me-1">Interval:</label>
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
                            ></vue2-datepicker> --}}
                            <input type="hidden" name="search_data_inceput" value="{{ $search_data_inceput }}">
                            <input type="hidden" name="search_data_sfarsit" value="{{ $search_data_sfarsit }}">
                        </div>
                    </div>
                    <div class="row mb-2 input-group custom-search-form justify-content-center">
                        {{-- <button class="btn btn-sm btn-primary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" type="submit"
                            name="action" value="cautare">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 mx-1 border border-dark rounded-3 shadow" href="{{ route('pontaje.afisare_lunar') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a> --}}
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <div class="col-lg-12 text-center">
                            <button class="btn btn-primary text-white mb-2 border border-dark rounded-3" type="submit"
                                name="action" value="saptamana_anterioara"
                                style="background-color: #FC4A1A; border:2px solid white;">
                                << SĂPTĂMÂNA ANTERIOARĂ
                            </button>
                        </div>
                        <div class="col-lg-12 text-center">
                            <button class="btn btn-primary text-white mb-2 border border-dark rounded-3" type="submit"
                                name="action" value="saptamana_urmatoare"
                                style="background-color: #FC4A1A; border:2px solid white;">
                                SĂPTĂMÂNA URMĂTOARE >>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive rounded mb-2">
                    <table class="table table-bordered table-dark table-striped table-hover table-sm rounded table-bordered">
                        <thead class="text-white rounded" style="background-color:#e66800;">
                            <tr class="" style="padding:2rem">
                                <th style="min-width: 170px;">Nume</th>
                                @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                    <th class="text-center">
                                        {{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD') }}
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
                                        {{-- <div class="px-2"
                                        style="
            position: absolute;
            display: inline-block;
            width: 160px;
            background-color:#e66800;
            color:white;
            "
            > --}}
                                            {{ $angajat->nume ?? '' }}
                                        {{-- </div> --}}
                                    </td>

                                    @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                        <td class="text-center">
                                            {{-- @if (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday()) --}}
                                                @forelse ($angajat->pontaj->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString()) as $pontaj)
                                                    {{-- <a href="/pontaje/{{ $pontaj->id }}/modifica" style="text-decoration: none;"> --}}
                                                        @switch($pontaj->concediu)
                                                            @case(0)
                                                                @if ($pontaj->ora_sosire && $pontaj->ora_plecare)
                                                                    @php
                                                                        $numar_de_ore = \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInHours(\Carbon\Carbon::parse($pontaj->ora_sosire))
                                                                    @endphp
                                                                    @if ($numar_de_ore < 8)
                                                                        {{ $numar_de_ore }}
                                                                    @else
                                                                        8
                                                                    @endif
                                                                @else
                                                                    {{-- <span class="text-danger"> --}}
                                                                        0
                                                                    {{-- </span> --}}
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
                                                    {{-- </a> --}}
                                                @empty
                                                    {{-- <a href="/pontaje/{{ $angajat->id }}/{{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}/adauga">
                                                        <i class="fas fa-plus-square"></i>
                                                    </a> --}}
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
                <a class="btn btn-lg w-100 text-white" href="/aplicatie-angajati/meniul-principal" style="background-color: #FC4A1A; border:2px solid white;">MENIUL PRINCIPAL</a>

    </div>


</div>

@endsection
