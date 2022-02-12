@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-tshirt me-1"></i>Produse / {{ $produs->nume }}</h6>
                </div>

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                    id="app1"
                >

            @include ('errors')

                    <div class="table-responsive col-md-12 mx-auto">
                        <table class="table table-sm table-striped table-hover"
                                {{-- style="background-color:#008282" --}}
                        >
                            <tr>
                                <td>
                                    Nume
                                </td>
                                <td>
                                    {{ $produs->nume }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <td>
                                    Client preț
                                </td>
                                <td>
                                    {{ $produs->client_pret }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Cost produs
                                </td>
                                <td>
                                    {{ $produs->cost_produs }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Cantitate
                                </td>
                                <td>
                                    {{ $produs->cantitate }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Observații
                                </td>
                                <td>
                                    {{ $produs->observatii }}
                                </td>
                            </tr> --}}
                        </table>
                    </div>

                    <div class="form-row mb-2 px-2">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <a class="btn btn-primary text-white btn-sm rounded-pill" href="/produse">Pagină produse</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
