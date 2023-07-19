@extends ('layouts.app')

<script type="application/javascript">
    produse = {!! json_encode($produse) !!}
    angajati = {!! json_encode($angajati) !!}
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
                                    <div class="row rounded-3 p-2 mb-2">
                                        <div class="col-lg-4 mb-2 mx-auto">
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
                                    </div>
                                    <div class="row rounded-3 p-2 mb-2">
                                        <div class="col-lg-5 mb-3">
                                            <label for="numereDeFaza" class="mb-0 ps-3">Numere de fază:</label>
                                            <input class="form-control rounded-pill mb-0"
                                                v-model="numereDeFaza"
                                                >
                                            <small class="ps-3">* se pot adăuga mai multe, despărțite prin virgulă</small>
                                        </div>
                                        <div class="col-lg-5 mb-3">
                                            <label for="iduriAngajati" class="mb-0 ps-3">Id-uri angajați:</label>
                                            <input class="form-control rounded-pill mb-0"
                                                v-model="iduriAngajati"
                                                >
                                            <small class="ps-3">* se pot adăuga mai multe, despărțite prin virgulă</small>
                                        </div>
                                        <div class="col-lg-2 mb-3 d-flex justify-content-center align-items-end mb-3">
                                            <button type="button" class="btn btn-success text-white rounded-3 border" @click="adaugaAngajatiLaFaze">
                                                {{-- <span class="bg-success text-white px-1" style="border-radius:20px"> --}}
                                                    Adaugă angajații la faze
                                                {{-- </span> --}}
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="mesajEroare" class="row rounded-3 p-2 mb-4 bg-danger text-white">
                                        <div class="col-lg-12">
                                            @{{ mesajEroare }}
                                        </div>
                                    </div>
                                    <div class="row rounded-3 p-2 mb-4">
                                        <div v-for="(produs, indexProdus) in produse">
                                            <div v-if="produs.id === produsSelectat">
                                                <div v-for="(operatie, indexOperatie) in produs.produse_operatii" class="col-lg-12 mb-4 px-2" style="background-color:rgb(201, 252, 231)">
                                                    <div class="col-lg-12 mb-2">
                                                        <b>@{{operatie.numar_de_faza}}</b> - @{{operatie.nume}}
                                                    </div>
                                                    <div class="col-lg-12 mb-2">
                                                        <div class="col-lg-2">
                                                            <label for="id" class="mb-0 ps-3">ID:</label>
                                                            <input class="form-control rounded-pill mb-2"
                                                                v-model="angajatIdDeAdaugat"
                                                                >
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <label for="angajatiSelectati" class="mb-0 ps-3">Angajati:</label>
                                                            <select class="form-select rounded-pill mb-2 {{ $errors->has('produse') ? 'is-invalid' : '' }}"
                                                                {{-- v-model="operatieSelectata" --}}
                                                                >
                                                                <option
                                                                    v-for='angajat in angajatiSelectati'
                                                                    :value='angajat.id'
                                                                    >
                                                                        @{{angajat.nume}}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    {{-- <div class="col-lg-2 d-flex align-items-center">
                                                        <button type="button" class="btn btn-success text-white rounded-pill m-0 px-2 mb-0" @click="adaugaOperatieAngajatului()">
                                                                Adaugă faza
                                                        </button>
                                                    </div> --}}
                                                    </div>
                                                    <div v-for="(angajat, indexAngajat) in operatie.angajati" class="col-lg-12 mb-2 text-end" style="background-color:rgb(82, 255, 183)">
                                                        <button type="button" class="btn m-0 p-0 mb-0" @click="stergeAngajat(indexProdus,indexOperatie,indexAngajat)">
                                                            <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                                                <i class="far fa-trash-alt text-white"></i>
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
