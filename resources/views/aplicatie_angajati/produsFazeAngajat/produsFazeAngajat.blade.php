@extends ('layouts.app')

<script type="application/javascript">
    produse = {!! json_encode($produse) !!}
</script>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-users me-1"></i>Produse faze angajați</h6>
                </div>

                @include ('errors')

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                >
                    <form  class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/produs-faze-angajati">

                    @csrf

                    <div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
                        <div class="col-lg-12 px-2 mb-1 text-center">
                            <span class="px-2" style="background-color:bisque">
                                * Pentru a nu se aglomera listele, se afișează doar produsele care sunt active în aplicație
                            </span>
                        </div>
                        <div class="col-lg-12 px-2 mb-0">
                            {{-- Gestionarea fazelor produselor --}}
                            <div class="row mb-4 p-2" id="produsFazeAngajati">
                                {{-- @php
                                    dd($angajat->produseOperatii->toArray(), array_values($angajat->produseOperatii->where('produs.activ' , 1)->toArray()));
                                @endphp --}}
                                <div class="col-lg-12">
                                    <div class="row rounded-3 p-2 mb-4">
                                        <div class="col-lg-4 mb-2">
                                            <label for="produse" class="mb-0 ps-3">Produse:</label>
                                            <select class="form-select rounded-pill mb-2 {{ $errors->has('produse') ? 'is-invalid' : '' }}"
                                                v-model="produsSelectat"
                                                >
                                                <option
                                                    v-for='produs in produse'
                                                    :value='produs.id'
                                                    >
                                                        @{{produs.nume}}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-for="produs in produse">
                                            <div v-if="produs.id === produsSelectat">
                                                <div v-for="operatie in produs.produse_operatii" class="col-lg-12 mb-4 px-2" style="background-color:rgb(201, 252, 231)">
                                                    <div class="col-lg-6 mb-2">
                                                        <b>@{{operatie.numar_de_faza}}</b> - @{{operatie.nume}}
                                                    </div>
                                                    <div v-for="angajat in operatie.angajati" class="col-lg-6 mb-2 text-end" style="background-color:rgb(82, 255, 183)">
                                                        <button type="button" class="btn m-0 p-0 mb-0" @click="stergeAngajat(produs.id,operatie.id,angajat.id)">
                                                            <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                                                Șterge
                                                            </span>
                                                        </button>
                                                        @{{angajat.nume}}
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-4">
                                            <label for="produse" class="mb-0 ps-3">Faze:</label>
                                            <select class="form-select rounded-pill mb-2 {{ $errors->has('produse') ? 'is-invalid' : '' }}"
                                                v-model="operatieSelectata"
                                                >
                                                <option
                                                    v-for='operatii in operatiiProdusSelectat'
                                                    :value='operatii.id'
                                                    >
                                                         @{{operatii.numar_de_faza}} - @{{operatii.nume}}
                                                </option>
                                            </select>
                                        </div> --}}
                                        {{-- <div class="col-lg-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-success text-white rounded-pill m-0 px-2 mb-0" @click="adaugaOperatieAngajatului()">
                                                    Adaugă faza
                                            </button>
                                        </div> --}}
                                    </div>

                                    <div class="row rounded-3 p-2 mb-4">
                                        {{-- <div v-for="(operatie, index) in angajatProduseOperatii" class="col-lg-12 mb-3">
                                            <input type="hidden" name="angajatProduseOperatii[]" :value=operatie.id>
                                            @{{ operatie.produsNume }}: <b>@{{ operatie.numar_de_faza }}</b> - @{{ operatie.nume }}
                                            <button type="button" class="btn m-0 p-0 mb-0" @click="angajatProduseOperatii.splice(index, 1)">
                                                <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                                    Șterge faza
                                                </span>
                                            </button>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row py-2 justify-content-center">
                                {{-- <div class="col-lg-8 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary text-white me-2 rounded-pill">Salvează</button>
                                    <a class="btn btn-secondary rounded-pill" href="/aplicatie-angajati/angajati">Renunță</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
