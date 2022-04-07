@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-clipboard-list me-1"></i>Adaugă o Normă Lucrată nouă</h6>
                </div>

                @include ('errors')

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                >
                    <form  class="needs-validation" novalidate method="POST" action="/norme-lucrate">


                                @include ('norme_lucrate.form', [
                                    // 'norma_lucrata' => new App\Models\NormaLucrata,
                                    'buttonText' => 'Adaugă Norma Lucrată'
                                ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
