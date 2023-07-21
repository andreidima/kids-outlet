@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
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
                <div class="row input-group custom-search-form justify-content-center">
                    <div class="col-md-3">
                        <button class="btn btn-sm btn-primary text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="saptamana_anterioara">
                            << Săptămâna anterioară
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-sm btn-primary text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="saptamana_urmatoare">
                            Săptămâna următoare >>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-sm btn-danger text-white border border-dark rounded-3 shadow" type="submit"
                            name="action" value="export_excel">
                            Export Excel Salarii
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-sm btn-warning text-dark border border-dark rounded-3 shadow" type="submit"
                            name="action" value="exportExcelAvansuri">
                            Export Excel Avansuri
                        </button>
                    </div>
                </div>
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
                                    @endphp
                                    @forelse ($angajat->norme_lucrate
                                                            // ->where('created_at', '>', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua))
                                                            // ->where('created_at', '<', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua+1))
                                                            ->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('YYYY-MM-DD'))
                                            as $norma_lucrata)
                                            @php
                                                $suma_totala += $norma_lucrata->cantitate * $norma_lucrata->produs_operatie->pret;
                                            @endphp
                                    @empty
                                    @endforelse

                                    <a href="/norme-lucrate/per-angajat-per-data/{{ $angajat->id }}/{{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString() }}">
                                        {{ ($suma_totala <> '0') ? ($suma_totala . ' lei') : '' }}
                                    </a>

                                    @php
                                        $suma_totala_pe_toata_perioada += $suma_totala;
                                    @endphp

                                    {{-- @forelse ($angajat->norme_lucrate->groupBy('data') as $norme_lucrate_per_data)
                                        @forelse ($norme_lucrate_per_data as $norma_lucrata)
                                            @if (\Carbon\Carbon::parse($norma_lucrata->created_at)->startOfDay() == \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua))

                                                <p class="m-0 p-0" style="white-space: nowrap">
                                                    <span class="badge bg-secondary mx-1" style="font-size: 1em">
                                                        {{ $norma_lucrata->numar_de_faza }}
                                                    </span>
                                                    =
                                                    <span class="badge bg-success mx-1" style="font-size: 1em">
                                                        {{ $norma_lucrata->cantitate }}
                                                    </span>
                                                        {{ $norma_lucrata->produs_operatie->pret ?? '' }}
                                                </p>

                                            @endif
                                        @empty
                                        @endforelse
                                    @empty
                                    @endforelse --}}
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
