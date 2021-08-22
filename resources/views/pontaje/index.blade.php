@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            <div class="col-lg-3">
                <h4 class="mb-0"><a href="{{ route('pontaje.index') }}"><i class="fas fa-user-clock me-1"></i>Pontaje</a></h4>
            </div>
            <div class="col-lg-6" id="app1">
                <form class="needs-validation" novalidate method="GET" action="{{ route('pontaje.index') }}">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm me-1 border rounded-pill" id="search_nume" name="search_nume" placeholder="Nume" autofocus
                                    value="{{ $search_nume }}">
                        </div>
                        <div class="col-lg-4 d-flex">
                            <label for="search_data" class="mb-0 align-self-center me-1">Data:</label>
                            <vue2-datepicker
                                data-veche="{{ $search_data }}"
                                nume-camp-db="search_data"
                                tip="date"
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
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ route('pontaje.index') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                <a class="btn btn-sm bg-success text-white border border-dark rounded-pill col-md-8" href="{{ route('pontaje.create') }}" role="button">
                    <i class="fas fa-plus-square text-white me-1"></i>Adaugă pontaj
                </a>
            </div>
        </div>

        <div class="card-body px-0 py-3">

            @include ('errors')

            <div class="table-responsive rounded">
                <table class="table table-striped table-hover table-sm rounded">
                    <thead class="text-white rounded" style="background-color:#e66800;">
                        <tr class="" style="padding:2rem">
                            <th>Nr. Crt.</th>
                            <th>Nume</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">Ora sosire</th>
                            <th class="text-center">Ora plecare</th>
                            <th class="text-end">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pontaje as $pontaj)
                            <tr>
                                <td align="">
                                    {{ ($pontaje ->currentpage()-1) * $pontaje ->perpage() + $loop->index + 1 }}
                                </td>
                                <td>
                                    <b>{{ $pontaj->angajat->nume ?? '' }}</b>
                                </td>
                                <td class="text-center">
                                    {{ $pontaj->data ? \Carbon\Carbon::parse($pontaj->data)->isoFormat('DD.MM.YYYY') : '' }}
                                </td>
                                <td class="text-center">
                                    {{ $pontaj->ora_sosire ? \Carbon\Carbon::parse($pontaj->ora_sosire)->isoFormat('HH:mm') : '' }}
                                </td>
                                <td class="text-center">
                                    {{ $pontaj->ora_plecare ? \Carbon\Carbon::parse($pontaj->ora_plecare)->isoFormat('HH:mm') : '' }}
                                </td>
                                <td class="d-flex justify-content-end">
                                    <a href="{{ $pontaj->path() }}"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-success">Vizualizează</span>
                                    </a>
                                    <a href="{{ $pontaj->path() }}/modifica"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-primary">Modifică</span>
                                    </a>
                                    <div style="flex" class="">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergePontaj{{ $pontaj->id }}"
                                            title="Șterge Pontaj"
                                            >
                                            <span class="badge bg-danger">Șterge</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- <div>Nu s-au gasit rezervări în baza de date. Încearcă alte date de căutare</div> --}}
                        @endforelse
                        </tbody>
                </table>
            </div>

                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$pontaje->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

        </div>
    </div>

    {{-- Modalele pentru stergere pontaj --}}
    @foreach ($pontaje as $pontaj)
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
    @endforeach

@endsection
