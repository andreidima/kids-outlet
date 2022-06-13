@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            <div class="col-lg-3">
                <h4 class="mb-0"><a href="{{ route('produse-operatii.index') }}"><i class="fas fa-tasks me-1"></i>Produse - operații</a></h4>
            </div>
            <div class="col-lg-6">
                <form class="needs-validation" novalidate method="GET" action="{{ route('produse-operatii.index') }}">
                    @csrf
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        {{-- <input type="text" class="form-control form-control-sm col-md-4 me-1 border rounded-pill" id="search_produs" name="search_produs" placeholder="Produs" autofocus
                                value="{{ $search_produs }}"> --}}
                        <div class="col-lg-8">
                            {{-- <label for="search_produs_id" class="mb-0 ps-3">Produs</label> --}}
                            <select name="search_produs_id"
                                class="form-select bg-white rounded-3 {{ $errors->has('search_produs_id') ? 'is-invalid' : '' }}"
                            >
                                    <option value='' selected>Selectează un produs</option>
                                @foreach ($produse as $produs)
                                    <option
                                        value='{{ $produs->id }}'
                                        {{ ($produs->id == $search_produs_id) ? 'selected' : '' }}
                                    >{{ $produs->nume }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <input type="text" class="form-control border rounded-3" id="search_nume" name="search_nume" placeholder="Operatie" autofocus
                                    value="{{ $search_nume }}">
                        </div>
                        {{-- <input type="text" class="form-control form-control-sm col-md-4 me-1 border rounded-pill" id="search_telefon" name="search_telefon" placeholder="Telefon" autofocus
                                value="{{ $search_telefon }}"> --}}
                    </div>
                    <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-primary text-white col-md-4 me-1 border border-dark rounded-3" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn bg-secondary text-white col-md-4 border border-dark rounded-3" href="{{ route('produse-operatii.index') }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 text-end">
                <a class="btn btn-sm bg-success text-white border border-dark rounded-pill col-md-8" href="{{ route('produse-operatii.create') }}" role="button">
                    <i class="fas fa-plus-square text-white me-1"></i>Adaugă operație
                </a>
            </div>
        </div>

        <div class="card-body px-0 py-3">

            @include ('errors')

            <div class="table-responsive rounded">
                <table class="table table-striped table-hover table-sm rounded">
                    <thead class="text-white rounded" style="background-color:#e66800;">
                        <tr class="" style="padding:2rem">
                            <th>Fază</th>
                            {{-- <th>Produs</th> --}}
                            <th>Nume operație</th>
                            {{-- <th>Număr de fază</th> --}}
                            {{-- <th>Timp</th>
                            <th>Preț</th> --}}
                            <th class="text-end">Preț</th>
                            <th class="text-end">Norma efectuată</th>
                            <th class="text-end">Norma totală</th>
                            <th class="text-end">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produse_operatii as $produs_operatie)
                            <tr>
                                {{-- <td align="">
                                    {{ ($produse_operatii ->currentpage()-1) * $produse_operatii ->perpage() + $loop->index + 1 }}
                                    {{ $loop->iteration }}
                                </td> --}}
                                <td>
                                    <b>{{ $produs_operatie->numar_de_faza }}</b>
                                </td>
                                {{-- <td>
                                    <b>{{ $produs_operatie->produs->nume ?? '' }}</b>
                                </td> --}}
                                <td>
                                    <b>{{ $produs_operatie->nume }}</b>
                                </td>
                                {{-- <td>
                                    {{ $produs_operatie->timp ? \Carbon\Carbon::parse($produs_operatie->timp)->isoFormat('HH:mm') : '' }}
                                </td>
                                <td>
                                    {{ $produs_operatie->pret }}
                                </td> --}}
                                <td class="text-end">
                                    {{ $produs_operatie->pret }}
                                </td>
                                <td class="text-end">
                                    <form class="needs-validation" novalidate method="GET" action="{{ route('norme-lucrate.index') }}" target="_blank">
                                        @csrf
                                            <input type="hidden" name="search_produs_id" value="{{ $produs_operatie->produs_id }}">
                                            <input type="hidden" name="search_numar_de_faza" value="{{ $produs_operatie->numar_de_faza }}">
                                            {{-- <a class="btn btn-primary" href="#" role="button" type="submit">Link</a> --}}
                                            <button class="btn btn-sm btn-primary text-white py-0" type="submit">
                                                {{ $produs_operatie->norma_totala_efectuata }}
                                            </button>

                                    </form>
                                    {{-- {{ $produs_operatie->norma_totala_efectuata }} --}}
                                </td>
                                <td class="text-end">
                                    {{ $produs_operatie->produs->cantitate ?? 0 }}
                                </td>
                                <td class="d-flex justify-content-end">
                                    <a href="{{ $produs_operatie->path() }}"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-success">Vizualizează</span>
                                    </a>
                                    <a href="{{ route('produse-operatii.edit', ['produs_operatie' => $produs_operatie->id, 'last_url' => '/produse-operatii']) }}"
                                        class="flex me-1"
                                    >
                                        <span class="badge bg-primary">Modifică</span>
                                    </a>
                                    <div style="flex" class="">
                                        <a
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stergeOperatie{{ $produs_operatie->id }}"
                                            title="Șterge Operatie"
                                            >
                                            <span class="badge bg-danger">Șterge</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                            <tr>
                                <td colspan="2" class="text-end">
                                    <b>Preț total</b>
                                </td>
                                <td class="text-end">
                                    <b>{{ $produse_operatii->sum('pret') }}</b>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                </table>
            </div>

                {{-- <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        {{$produse_operatii->appends(Request::except('page'))->links()}}
                    </ul>
                </nav> --}}

        </div>
    </div>

    Modalele pentru stergere produs_operatie
    @foreach ($produse_operatii as $produs_operatie)
        <div class="modal fade text-dark" id="stergeOperatie{{ $produs_operatie->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Produs: {{ $produs_operatie->produs->nume ?? '' }} / Operație: <b>{{ $produs_operatie->nume }}</b></h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="text-align:left;">
                    Ești sigur ca vrei să ștergi Operația?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Renunță</button>

                    <form method="POST" action="{{ $produs_operatie->path() }}">
                        @method('DELETE')
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-danger text-white"
                            >
                            Șterge Operație
                        </button>
                    </form>

                </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
