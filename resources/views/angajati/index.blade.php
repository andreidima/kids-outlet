@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            <div class="col-lg-3">
                <h4 class="mb-0"><a href="{{ route('angajati.index') }}"><i class="fas fa-users me-1"></i>Angajați</a></h4>
            </div>
            <div class="col-lg-6">
                <form class="needs-validation" novalidate method="GET" action="{{ route('angajati.index') }}">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <input type="text" class="form-control form-control-sm col-md-4 me-1 border rounded-pill" id="search_nume" name="search_nume" placeholder="Nume" autofocus
                                value="{{ $search_nume }}">
                        <input type="text" class="form-control form-control-sm col-md-4 me-1 border rounded-pill" id="search_telefon" name="search_telefon" placeholder="Telefon" autofocus
                                value="{{ $search_telefon }}">
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ route('angajati.index') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                <a class="btn btn-sm bg-success text-white border border-dark rounded-pill col-md-8" href="{{ route('angajati.create') }}" role="button">
                    <i class="fas fa-plus-square text-white me-1"></i>Adaugă angajat
                </a>
            </div>
        </div>

        <div class="card-body px-0 py-3">

            @include ('errors')

            <div class="table-responsive rounded">
                <table class="table table-striped table-hover table-sm rounded">
                    <thead class="text-white rounded" style="background-color:#e66800;">
                        <tr class="" style="padding:2rem">
                            <th>#</th>
                            <th>Nume</th>
                            <th>Prod</th>
                            {{-- <th>Cod de acces</th> --}}
                            <th>Sectia</th>
                            <th>Firma</th>
                            <th>Foaie pontaj</th>
                            <th>Ore angajare</th>
                            <th>Stare cont</th>
                            <th class="text-end">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($angajati as $angajat)
                            <tr>
                                <td align="">
                                    {{ ($angajati ->currentpage()-1) * $angajati ->perpage() + $loop->index + 1 }}
                                </td>
                                <td>
                                    <b>{{ $angajat->nume }}</b>
                                </td>
                                <td>
                                    <b>{{ $angajat->prod }}</b>
                                </td>
                                {{-- <td>
                                    {{ $angajat->cod_de_acces }}
                                </td> --}}
                                <td>
                                    {{ $angajat->sectia }}
                                </td>
                                <td>
                                    {{ $angajat->firma }}
                                </td>
                                <td>
                                    {{ $angajat->foaie_pontaj }}
                                </td>
                                <td class="text-center">
                                    {{ $angajat->ore_angajare }}
                                </td>
                                <td>
                                    @if ($angajat->activ === 1)
                                        <small class="text-success">Deschis</small>
                                    @else
                                        <small class="text-danger">Închis</small>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-end">
                                    <a href="{{ $angajat->path() }}"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-success">Vizualizează</span>
                                    </a>
                                    <a href="{{ $angajat->path() }}/modifica"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-primary">Modifică</span>
                                    </a>
                                    {{-- <div style="flex" class="">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergeAngajat{{ $angajat->id }}"
                                            title="Șterge Angajat"
                                            >
                                            <span class="badge bg-danger">Șterge</span>
                                        </a>
                                    </div> --}}
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
                        {{$angajati->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

        </div>
    </div>

    {{-- Modalele pentru stergere angajat --}}
    {{-- @foreach ($angajati as $angajat)
        <div class="modal fade text-dark" id="stergeAngajat{{ $angajat->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Angajat: <b>{{ $angajat->nume }}</b></h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:left;">
                    Ești sigur ca vrei să ștergi Angajatul?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                    <form method="POST" action="{{ $angajat->path() }}">
                        @method('DELETE')
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-danger text-white"
                            >
                            Șterge Angajat
                        </button>
                    </form>

                </div>
                </div>
            </div>
        </div>
    @endforeach --}}

@endsection
