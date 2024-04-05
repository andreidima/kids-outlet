@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            {{-- @if (!$angajat) --}}
            <div class="col-lg-3">
                <h4 class="mb-0"><a href="{{ route('norme-lucrate.index') }}"><i class="fas fa-clipboard-list me-1"></i>Norme lucrate</a></h4>
            </div>
            <div class="col-lg-6" id="app1">
                <form class="needs-validation" novalidate method="GET" action="{{ route('norme-lucrate.index') }}">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control me-1 border rounded-3" id="search_nume" name="search_nume" placeholder="Angajat" autofocus
                                    value="{{ $search_nume }}">
                        </div>
                        <div class="col-lg-6 d-flex justify-content-center">
                            <label for="search_data" class="mb-0 align-self-center me-1">Interval:</label>
                            <vue2-datepicker
                                data-veche="{{ $search_data }}"
                                nume-camp-db="search_data"
                                tip="date"
                                range="range"
                                value-type="YYYY-MM-DD"
                                format="DD-MM-YYYY"
                                :latime="{ width: '225px' }"
                            ></vue2-datepicker>
                        </div>
                        {{-- <div class="col-lg-6 d-flex justify-content-center">
                            <label for="search_data" class="mb-0 align-self-center me-1">Data:</label>
                            <vue2-datepicker
                                data-veche="{{ $search_data }}"
                                nume-camp-db="search_data"
                                tip="date"
                                value-type="YYYY-MM-DD"
                                format="DD-MM-YYYY"
                                :latime="{ width: '125px' }"
                            ></vue2-datepicker>
                        </div> --}}
                        <div class="col-lg-6">
                            <select name="search_produs_id" class="form-select bg-white rounded-3 {{ $errors->has('search_produs_id') ? 'is-invalid' : '' }}">
                                    <option value='' selected>Produs</option>
                                @foreach ($produse as $produs)
                                    <option value='{{ $produs->id }}'
                                            {{ ($produs->id == $search_produs_id) ? 'selected' : '' }}>
                                        {{ $produs->nume }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control me-1 border rounded-3" id="search_numar_de_faza" name="search_numar_de_faza" placeholder="Nr. fază" autofocus
                                    value="{{ $search_numar_de_faza }}">
                        </div>
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ route('norme-lucrate.index') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                {{-- Id = 4, norme lucrate, nu poate umbla la actiuni --}}
                @if (auth()->user()->id != 4)
                    <a class="btn btn-sm bg-success text-white border border-dark rounded-pill col-md-8"
                        href="{{ route('norme-lucrate.create') }}" role="button">
                        <i class="fas fa-plus-square text-white me-1"></i>Adaugă normă lucrată
                    </a>
                @endif
            </div>
            {{-- @else
                <div class="col-lg-9">
                    <h4 class="mb-0">
                        <i class="fas fs-4 fa-clipboard-list me-1"></i>
                        Norme lucrate / {{ $angajat->nume }} /
                        {{ $search_data ? \Carbon\Carbon::parse($search_data)->isoFormat('DD.MM.YYYY') : '' }}
                    </h4>
                </div>
                <div class="col-lg-3 text-end">
                    <a class="btn btn-sm bg-success border border-dark text-white rounded-3 shadow col-md-8"
                        href="/norme-lucrate/adauga/per-angajat-per-data/{{ $angajat->id ?? '' }}/{{ $search_data }}" role="button">
                        <i class="fas fa-plus-square text-white me-1"></i>Adaugă normă lucrată
                    </a>
                </div>
            @endif --}}
        </div>

        <div class="card-body px-0 py-3">

            @include ('errors')

            <div class="table-responsive rounded">
                <table class="table table-striped table-hover table-sm rounded mb-0">
                    <thead class="text-white rounded" style="background-color:#e66800;">
                        <tr class="" style="padding:2rem">
                            <th>#</th>
                            @if (!$angajat)
                                <th>Angajat</th>
                            @endif
                            <th>Produs</th>
                            <th>Nr. fază</th>
                            <th>Operație</th>
                            <th class="text-center">Cantitate</th>
                            <th class="text-center">Norma</th>
                            <th class="text-center">Preț</th>
                            <th class="text-center">Suma</th>
                            @if (!$angajat)
                                <th class="text-center">Data lucrării</th>
                            @endif

                            {{-- Id = 4, norme lucrate, nu poate umbla la actiuni --}}
                            @if (auth()->user()->id != 4)
                                <th class="text-end">Acțiuni</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($angajat)
                            @php
                                $suma_totala = 0;
                            @endphp
                        @endif
                        @forelse ($norme_lucrate as $norma_lucrata)
                            <tr>
                                <td align="">
                                    {{ ($norme_lucrate ->currentpage()-1) * $norme_lucrate ->perpage() + $loop->index + 1 }}
                                </td>
                                @if (!$angajat)
                                    <td>
                                        <b>{{ $norma_lucrata->angajat->nume ?? '' }}</b>
                                    </td>
                                @endif
                                <td>
                                    {{ $norma_lucrata->produs_operatie->produs->nume ?? '' }}
                                </td>
                                <td>
                                    {{ $norma_lucrata->produs_operatie->numar_de_faza ?? '' }}
                                </td>
                                <td>
                                    {{ $norma_lucrata->produs_operatie->nume ?? '' }}
                                </td>
                                <td class="text-center">
                                    {{ $norma_lucrata->cantitate }}
                                </td>
                                <td class="text-center">
                                    {{ number_format(($norma_lucrata->produs_operatie->norma ?? 0), 0) }}
                                </td>
                                <td class="text-center">
                                    {{ $norma_lucrata->produs_operatie->pret ?? ''}}
                                </td>
                                <td class="text-center">
                                    {{ $norma_lucrata->cantitate * ($norma_lucrata->produs_operatie->pret ?? 0) }}
                                    @if ($angajat)
                                        @php
                                            $suma_totala += $norma_lucrata->cantitate * ($norma_lucrata->produs_operatie->pret ?? 0);
                                        @endphp
                                    @endif
                                </td>
                                @if (!$angajat)
                                    <td class="text-center">
                                        {{ $norma_lucrata->data ? \Carbon\Carbon::parse($norma_lucrata->data)->isoFormat('DD.MM.YYYY') : '' }}
                                    </td>
                                @endif

                                {{-- Id = 4, norme lucrate, nu poate umbla la actiuni --}}
                                @if (auth()->user()->id != 4)
                                    <td class="">
                                        <div class="d-flex justify-content-end">
                                            {{-- <a href="{{ $norma_lucrata->path() }}"
                                                class="flex me-1"
                                            >
                                                <span class="badge bg-success">Vizualizează</span>
                                            </a> --}}
                                            <a href="{{ $norma_lucrata->path() }}/modifica"
                                                class="flex me-1"
                                            >
                                                <span class="badge bg-primary">Modifică</span>
                                            </a>
                                            <div style="flex" class="">
                                                <a
                                                    href="#"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#stergeNormaLucrata{{ $norma_lucrata->id }}"
                                                    title="Șterge NormaLucrata"
                                                    >
                                                    <span class="badge bg-danger">Șterge</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>

                            @if ($loop->last)
                                @if ($angajat)
                                <tr>
                                    <td colspan="6" class="text-end">
                                        Total
                                    </td>
                                    <td class="text-center">
                                        {{ $suma_totala }}
                                    </td>
                                    <td></td>
                                </tr>
                                @endif
                            @endif
                        @empty
                            {{-- <div>Nu s-au gasit rezervări în baza de date. Încearcă alte date de căutare</div> --}}
                        @endforelse
                        </tbody>
                </table>
            </div>

                <nav class="">
                    <ul class="pagination justify-content-center">
                        {{$norme_lucrate->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

        </div>

        @if ($angajat)
            <div class="row mb-2 py-2 justify-content-center">
                <div class="d-grid gap-2 col-lg-2">
                    <a class="btn btn-secondary rounded-3 shadow" href="{{ Session::get('norme_lucrate_afisare_tabelara_return_url') }}">Înapoi</a>
                </div>
            </div>
        @endif
    </div>

    {{-- Id = 4, norme lucrate, nu poate umbla la actiuni --}}
    @if (auth()->user()->id != 4)
        {{-- Modalele pentru stergere norma_lucrata --}}
        @foreach ($norme_lucrate as $norma_lucrata)
            <div class="modal fade text-dark" id="stergeNormaLucrata{{ $norma_lucrata->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Norma Lucrată: <b>{{ $norma_lucrata->angajat->nume ?? '' }}</b></h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="text-align:left;">
                        Ești sigur ca vrei să ștergi Norma Lucrată?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                        <form method="POST" action="{{ $norma_lucrata->path() }}">
                            @method('DELETE')
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-danger text-white"
                                >
                                Șterge Norma Lucrată
                            </button>
                        </form>

                    </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection
