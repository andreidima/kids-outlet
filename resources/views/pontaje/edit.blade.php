@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-user-clock me-1"></i>Schimbă datele pontajului</h6>
                </div>

                @include ('errors')

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                >
                    <form  class="needs-validation" novalidate method="POST" action="{{ $pontaj->path() }}">
                        @method('PATCH')


                                @include ('pontaje.form', [
                                    'buttonText' => 'Modifică Pontajul'
                                ])

                    </form>

                    <div class="text-center p-4">
                        <a
                            class="btn btn-danger text-white rounded-3 border-dark"
                            href=""
                            data-bs-toggle="modal"
                            data-bs-target="#stergePontaj{{ $pontaj->id }}"
                            title="Șterge Pontaj"
                            >
                            Șterge pontajul
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


        {{-- Modala pentru stergere pontaj --}}
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

@endsection
