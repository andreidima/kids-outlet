@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            <div class="col-lg-3">
                <h4 class="mb-0"><a href="{{ route('produse.index') }}"><i class="fas fa-tshirt me-1"></i>Produse</a></h4>
            </div>
            <div class="col-lg-6">
                <form class="needs-validation" novalidate method="GET" action="{{ route('produse.index') }}">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <input type="text" class="form-control form-control-sm col-md-4 me-1 border rounded-pill" id="search_nume" name="search_nume" placeholder="Nume" autofocus
                                value="{{ $search_nume }}">
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ route('produse.index') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                <a class="btn btn-sm bg-success text-white border border-dark rounded-pill col-md-8" href="{{ route('produse.create') }}" role="button">
                    <i class="fas fa-plus-square text-white me-1"></i>Adaugă produs
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
                            {{-- <th>Client preț</th>
                            <th>Cost produs</th>
                            <th>Cantitate</th> --}}
                            <th class="text-end">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produse as $produs)
                            <tr>
                                <td align="">
                                    {{ ($produse ->currentpage()-1) * $produse ->perpage() + $loop->index + 1 }}
                                </td>
                                <td>
                                    <b>{{ $produs->nume }}</b>
                                </td>
                                {{-- <td>
                                    {{ $produs->client_pret }}
                                </td>
                                <td>
                                    {{ $produs->cost_produs }}
                                </td>
                                <td>
                                    {{ $produs->cantitate }}
                                </td> --}}
                                <td class="d-flex justify-content-end">
                                    <a href="{{ $produs->path() }}"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-success">Vizualizează</span>
                                    </a>
                                    <a href="{{ $produs->path() }}/modifica"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-primary">Modifică</span>
                                    </a>
                                    <div style="flex" class="">
                                        <a
                                            class="flex me-1"
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#duplicaProdus{{ $produs->id }}"
                                            title="Duplică Produs"
                                            >
                                            <span class="badge bg-warning">Duplică</span>
                                        </a>
                                    </div>
                                    <div style="flex" class="">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergeProdus{{ $produs->id }}"
                                            title="Șterge Produs"
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
                        {{$produse->appends(Request::except('page'))->links()}}
                    </ul>
                </nav>

        </div>
    </div>

    {{-- Modalele pentru duplicare produs --}}
    @foreach ($produse as $produs)
        <div class="modal fade text-dark" id="duplicaProdus{{ $produs->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Produs: <b>{{ $produs->nume }}</b></h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:left;">
                    Ești sigur ca vrei să duplici Produsul?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                    <form method="POST" action="{{ $produs->path() }}/duplica">
                        {{-- @method('DELETE') --}}
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-warning text-white"
                            >
                            Duplică Produs
                        </button>
                    </form>

                </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modalele pentru stergere produs --}}
    @foreach ($produse as $produs)
        <div class="modal fade text-dark" id="stergeProdus{{ $produs->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Produs: <b>{{ $produs->nume }}</b></h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:left;">
                    Ești sigur ca vrei să ștergi Produsul?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                    <form method="POST" action="{{ $produs->path() }}">
                        @method('DELETE')
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-danger text-white"
                            >
                            Șterge Produs
                        </button>
                    </form>

                </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
