@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-tasks me-1"></i>Produse - operații / {{ $produs_operatie->nume }}</h6>
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
                                    Produs
                                </td>
                                <td>
                                    {{ $produs_operatie->produs->nume ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nume
                                </td>
                                <td>
                                    {{ $produs_operatie->nume }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Număr de fază
                                </td>
                                <td>
                                    {{ $produs_operatie->numar_de_faza }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <td>
                                    Timp
                                </td>
                                <td>
                                    {{ $produs_operatie->timp ? \Carbon\Carbon::parse($produs_operatie->timp)->isoFormat('HH:mm') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Preț
                                </td>
                                <td>
                                    {{ $produs_operatie->pret }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Norma
                                </td>
                                <td>
                                    {{ $produs_operatie->norma }}
                                </td>
                            </tr> --}}
                        </table>
                    </div>

                    <div class="form-row mb-2 px-2">
                        <div class="col-lg-12 d-flex justify-content-center">
                            <a class="btn btn-primary text-white btn-sm rounded-pill" href="/produse-operatii">Pagină produse operații</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
