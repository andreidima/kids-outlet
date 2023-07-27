@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <form class="needs-validation" novalidate method="GET" action="{{ url()->current() }}">
            @csrf
            <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
                <div class="col-lg-3">
                    <h4 class="mb-0">Lichidare</a></h4>
                </div>
                <div class="col-lg-6" id="app1">
                    <div class="row mb-1 input-group custom-search-form justify-content-center">
                        <div class="col-lg-5 d-flex justify-content-center">
                            <label for="searchLuna" class="mb-0 align-self-center me-1">Luna:</label>
                            <input type="text" class="form-control form-control border rounded-3" id="searchLuna" name="searchLuna" placeholder="Luna" autofocus
                                    value="{{ $searchLuna }}">
                        </div>
                        <div class="col-lg-5 d-flex justify-content-center">
                            <label for="searchAn" class="mb-0 align-self-center me-1">An:</label>
                            <input type="text" class="form-control form-control me-1 border rounded-3" id="searchAn" name="searchAn" placeholder="An" autofocus
                                    value="{{ $searchAn }}">
                        </div>
                    </div>
                    {{-- <div class="row input-group custom-search-form justify-content-center">
                        <button class="btn btn-sm btn-primary text-white col-md-4 me-1 border border-dark rounded-pill" type="submit">
                            <i class="fas fa-search text-white me-1"></i>Caută
                        </button>
                        <a class="btn btn-sm bg-secondary text-white col-md-4 border border-dark rounded-pill" href="{{ url()->current() }}" role="button">
                            <i class="far fa-trash-alt text-white me-1"></i>Resetează căutarea
                        </a>
                    </div> --}}
                </div>
                <div class="col-lg-3">
                    <div class="mb-2 d-flex align-items-center justify-content-end">
                        <button class="btn btn-sm btn-success text-white border border-dark rounded-pill shadow" type="submit"
                            name="action" value="exportExcelToate">
                            Export Excel Salarii
                        </button>
                    </div>
                    {{-- <div class="d-grid gap-2 d-flex align-items-center justify-content-end">
                        <div class="px-2 py-0 d-flex align-items-center rounded-pill text-dark" style="background-color:rgb(193, 255, 226)">
                            Export: &nbsp;
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportExcelBancaBt">
                                Excel BT
                            </button>
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportTxtBancaIng">
                                Txt ING
                            </button>
                            <button class="btn btn-sm btn-success text-white mx-1 border border-dark rounded-pill" type="submit"
                                name="action" value="exportExcelMana">
                                Excel Mână
                            </button>
                        </div>
                    </div> --}}
                </div>
                <div class="col-lg-12 d-grid gap-2 d-flex align-items-center">
                </div>
            </div>
        </form>

        <div class="card-body px-0 py-3">

            @include ('errors')

        </div>
    </div>

@endsection
