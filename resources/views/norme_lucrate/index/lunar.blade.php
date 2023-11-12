@extends ('layouts.app')

@php
    use \Carbon\Carbon;
@endphp

@section('content')
{{-- <div class="container card" style="border-radius: 40px 40px 40px 40px;"> --}}
<div class="card mx-1" style="border-radius: 40px 40px 40px 40px;">
    <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
        <div class="col-lg-2">
            {{-- <h4 class="mb-0"><a href="{{ route('norme-lucrate.afisare_lunar') }}">
                <i class="fas fa-clipboard-list me-1"></i>Norme lucrate</a> /
                {{ \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') ?? '' }}
                -
                {{ \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') ?? '' }}
            </h4> --}}
            <h4 class="mb-2">
                <i class="fas fs-4 fa-clipboard-list me-1"></i>Norme lucrate
            {{-- </h4>
            <h4 class="mb-0"> --}}
                {{-- {{ \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') ?? '' }}
                -
                {{ \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') ?? '' }} --}}
            </h4>
            <div>
                <form class="needs-validation" novalidate method="GET" action="{{ route('pontaje.afisare_lunar') }}">
                    @csrf
                </form>
            </div>
        </div>
        <div class="col-lg-10" id="app1">
            <form class="needs-validation" novalidate method="GET" action="{{ route('norme-lucrate.afisare_lunar') }}">
                @csrf
                <div class="row mb-1 input-group custom-search-form justify-content-center">
                    <div class="col-lg-7 d-flex">
                        <div>
                            <input type="text" class="form-control form-control me-1 border rounded-3" id="search_nume" name="search_nume" placeholder="Scrie Angajat" autofocus
                                    value="{{ $search_nume }}">
                        </div>
                        <div class="mx-2 d-flex justify-content-center align-items-end">
                            sau
                        </div>
                        <div class="">
                            <select name="search_angajat_id"
                                class="form-select bg-white rounded-3 {{ $errors->has('search_angajat_id') ? 'is-invalid' : '' }}"
                            >
                                    <option value='' selected>Alege angajat</option>
                                @foreach ($angajati_in_search as $angajat)
                                    <option
                                        value='{{ $angajat->id }}'
                                        {{ ($angajat->id == $search_angajat_id) ? 'selected' : '' }}
                                    >{{ $angajat->nume }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-lg-3">
                        <input type="text" class="form-control me-1 border rounded-3" id="search_nume" name="search_nume" placeholder="Nume" autofocus
                                value="{{ $search_nume }}">
                    </div> --}}
                    <div class="col-lg-5 d-flex">
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
                    <button class="btn btn-sm btn-primary text-white col-md-4 mx-1 border border-dark rounded-3" type="submit">
                        <i class="fas fa-search text-white me-1"></i>Caută
                    </button>
                    <a class="btn btn-sm bg-secondary text-white col-md-4 mx-1 border border-dark rounded-3" href="{{ route('norme-lucrate.afisare_lunar') }}" role="button">
                        <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                    </a>
                </div>
                <div class="row mb-4 input-group custom-search-form justify-content-center">
                    <div class="col-md-3 d-grid gap-2">
                        <button class="btn btn-sm btn-primary text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="saptamana_anterioara">
                            << Săptămâna anterioară
                        </button>
                    </div>
                    <div class="col-md-3 d-grid gap-2">
                        <button class="btn btn-sm btn-primary text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="saptamana_urmatoare">
                            Săptămâna următoare >>
                        </button>
                    </div>
                    {{-- <div class="col-md-3 d-grid gap-2">
                        <button class="btn btn-sm btn-danger text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="export_excel">
                            Export Excel Salarii
                        </button>
                    </div> --}}
                </div>
                {{-- <div class="row input-group custom-search-form justify-content-center">
                    <div class="col-md-6 d-grid gap-2 d-flex align-items-center">
                        <div class="px-2 d-flex align-items-center" style="background-color:rgb(252, 252, 173)">
                            Export avansuri: &nbsp;
                            <button class="btn btn-sm btn-warning text-dark mx-1 border border-dark rounded-3 shadow" type="submit"
                                name="action" value="exportExcelAvansuri">
                                Excel Toate
                            </button>
                            <button class="btn btn-sm btn-warning text-dark mx-1 border border-dark rounded-3 shadow" type="submit"
                                name="action" value="exportExcelBancaBt">
                                Excel BT
                            </button>
                            <button class="btn btn-sm btn-warning text-dark mx-1 border border-dark rounded-3 shadow" type="submit"
                                name="action" value="exportTxtBancaIng">
                                Txt ING
                            </button>
                        </div>
                    </div>
                </div> --}}
            </form>
        </div>
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
                        <th class="text-center" style="min-width: 120px;">
                            Realizat
                        </th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ziuaDinSaptamana este necesara la calcularea normelor pe fiecare zi in parte --}}
                    @php
                        $ziuaDinSaptamana = Carbon::today()->dayOfWeek;
                    @endphp

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
    width: 160px;
    background-color:#e66800;
    color:white;
    "
    >
                                    {{ $angajat->nume ?? '' }}
                                </div>
                            </td>

                            @php
                                $suma_totala_pe_toata_perioada = 0;
                            @endphp
                            @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                                <td class="text-center">
                                    @php
                                        $suma_totala = 0;
                                        $minuteTotale = 0;
                                    @endphp
                                    @forelse ($angajat->norme_lucrate->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('YYYY-MM-DD')) as $norma_lucrata)
                                            @php
                                                $suma_totala += $norma_lucrata->cantitate * ($norma_lucrata->produs_operatie->pret ?? 0);

                                                if ($norma_lucrata->produs_operatie->norma && ($norma_lucrata->produs_operatie->norma > 0)){
                                                    $minuteTotale += $norma_lucrata->cantitate * (480/$norma_lucrata->produs_operatie->norma);
                                                }
                                            @endphp
                                    @empty
                                    @endforelse

                                    @if ($minuteTotale > 0)
                                        {{-- 480 de minute este norma pentru romani toate zilele, iar pentru straini doar in ziua de vineri --}}
                                        @if (
                                            ($angajat->limba_aplicatie == 1) // angajatul este roman
                                            || (($angajat->limba_aplicatie == 2) && ($ziuaDinSaptamana == 5)) // angajatul este strain si este vineri
                                        )
                                            @if ($minuteTotale < 480)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-danger p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @elseif($minuteTotale == 480)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-success p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @elseif($minuteTotale > 480)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-primary p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @endif
                                        @elseif (
                                            ($angajat->limba_aplicatie == 2) && ($ziuaDinSaptamana < 5) // angajatul este strain si ziua este luni-joi
                                        )
                                            @if ($minuteTotale < 600)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-danger p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @elseif($minuteTotale == 600)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-success p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @elseif($minuteTotale > 600)
                                                <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                                    <span class="badge bg-primary p-1">
                                                        {{ number_format($minuteTotale, 0) . ' min' }}
                                                    </span>
                                                </a>
                                            @endif
                                        @endif
                                        <br>
                                    @endif
                                    <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                        {{ ($suma_totala <> '0') ? ($suma_totala . ' lei') : '' }}
                                    </a>

                                    @php
                                        $suma_totala_pe_toata_perioada += $suma_totala;
                                    @endphp
                                </td>
                            @endfor
                            <td class="text-center">
                                {{ ($suma_totala_pe_toata_perioada <> '0') ? ($suma_totala_pe_toata_perioada . ' lei') : '' }}
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>

        </div>

        {{-- <div class="row">
            <div class="col-lg-12 my-0 py-0">
                <b>Legendă:</b>
                    <span class="badge bg-secondary mx-1" style="font-size: 1em">
                        Număr de fază
                    </span>
                    <span class="badge bg-success mx-1" style="font-size: 1em">
                        Cantitate
                    </span>
            </div>
        </div> --}}
                {{-- <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$angajati->appends(Request::except('page'))->links()}}
                    </ul>
                </nav> --}}

    </div>


</div>


@endsection
