@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-users me-1"></i>Adaugă un angajat nou</h6>
                </div>

                @include ('errors')

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                >
                    <form  class="needs-validation" novalidate method="POST" action="/angajati">


                                @include ('angajati.form', [
                                    'angajat' => new App\Models\Angajat,
                                    'buttonText' => 'Adaugă Angajatul'
                                ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
