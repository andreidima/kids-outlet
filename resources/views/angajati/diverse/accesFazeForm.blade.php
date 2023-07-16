@extends ('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="shadow-lg" style="border-radius: 40px 40px 40px 40px;">
                <div class="border border-secondary p-2" style="border-radius: 40px 40px 0px 0px; background-color:#e66800">
                    <h6 class="ms-2 my-0" style="color:white"><i class="fas fa-users me-1"></i>Adaugă un angajat nou</h6>
                </div>

                @include ('errors')

                <div class="card-body py-2 border border-secondary"
                    style="border-radius: 0px 0px 40px 40px;"
                >
                    <form  class="needs-validation" novalidate method="POST" action="/aplicatie-angajati/angajati-acces-faze/{{ $angajat->id }}">

                    @csrf

                    <div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
                        <div class="col-lg-12 px-2 mb-1 text-center">
                            <span class="px-2" style="background-color:bisque">
                                * Pentru a nu se aglomera listele, se afișează doar produsele care sunt active în aplicație
                            </span>
                        </div>
                        <div class="col-lg-12 px-2 mb-0">
                            {{-- Gestionarea fazelor produselor --}}
                            <div class="row mb-4 p-2" id="gestionareFazeAngajati">
                                <script type="application/javascript">
                                    produse = {!! json_encode($produse) !!}
                                    // angajatPontatori={!! json_encode(old('angajat_pontatori', $angajat->angajati_pontatori->pluck('id')->toArray() ?? [] )) !!}
                                    // angajatProduseOperatii= {!! json_encode(old('produseOperatii', $angajat->produseOperatii->pluck('id')->toArray() ?? [] )) !!}
                                    angajatProduseOperatii= {!! json_encode(old('produseOperatii', array_values($angajat->produseOperatii->where('produs.activ' , 1)->toArray()) ?? [] )) !!}
                                </script>
                                {{-- @php
                                    dd($angajat->produseOperatii->toArray(), array_values($angajat->produseOperatii->where('produs.activ' , 1)->toArray()));
                                @endphp --}}
                                <div class="col-lg-12">
                                    <div class="row rounded-3 p-2">
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
                                        <div class="col-lg-6">
                                            <label for="produse" class="mb-0 ps-3">Faze:</label>
                                            <select class="form-select rounded-pill mb-2 {{ $errors->has('produse') ? 'is-invalid' : '' }}"
                                                v-model="operatieSelectata"
                                                >
                                                <option
                                                    v-for='operatii in operatiiProdusSelectat'
                                                    :value='operatii.id'
                                                    >
                                                        @{{operatii.nume}}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 d-flex align-items-center">
                                            {{-- <button type="button" class="btn m-0 px-2 mb-2" @click="adaugaOperatieAngajatului()" style="background-color:rgb(10, 83, 112); color:rgb(255, 255, 255); border-radius:20px"> --}}
                                            <button type="button" class="btn btn-success text-white rounded-pill m-0 px-2 mb-0" @click="adaugaOperatieAngajatului()">
                                                {{-- <span class="px-2" style="background-color:rgb(10, 83, 112); color:rgb(255, 255, 255); border-radius:20px"> --}}
                                                    Adaugă faza
                                                {{-- </span> --}}
                                            </button>
                                        </div>

                                        <div v-for="(operatie, index) in angajatProduseOperatii" class="col-lg-12 mb-3">
                                            <input type="hidden" name="angajatProduseOperatii[]" :value=operatie.id>
                                            {{-- @{{ index+1 }}. @{{ operatie.produsNume }} - @{{ operatie.nume }} --}}
                                            @{{ operatie.produsNume }} - @{{ operatie.nume }}
                                            {{-- @{{ operatie.produs.nume }} - @{{ operatie.nume }} --}}
                                            <button type="button" class="btn m-0 p-0 mb-0" @click="angajatProduseOperatii.splice(index, 1)">
                                                <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                                    Șterge faza
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row py-2 justify-content-center">
                                <div class="col-lg-8 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary text-white me-2 rounded-pill">Salvează</button>
                                    {{-- <a class="btn btn-secondary mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                                    <a class="btn btn-secondary rounded-pill" href="/aplicatie-angajati/angajati">Renunță</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
