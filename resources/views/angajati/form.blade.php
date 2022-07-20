@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $angajat->nume) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="telefon" class="mb-0 ps-3">Telefon:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('telefon') ? 'is-invalid' : '' }}"
                    name="telefon"
                    placeholder=""
                    value="{{ old('telefon', $angajat->telefon) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="cod_de_acces" class="mb-0 ps-3">Cod de acces:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cod_de_acces') ? 'is-invalid' : '' }}"
                    name="cod_de_acces"
                    placeholder=""
                    value="{{ old('cod_de_acces', $angajat->cod_de_acces) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="sectia" class="mb-0 ps-3">Secția:</label>
                <select name="sectia" class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option selected>Selectează</option>
                    <option value="Moda" {{ old('sectia', $angajat->sectia) === "Moda" ? 'selected' : '' }}>Moda</option>
                    <option value="Sectie" {{ old('sectia', $angajat->sectia) === "Sectie" ? 'selected' : '' }}>Sectie</option>
                    <option value="Mostre" {{ old('sectia', $angajat->sectia) === "Mostre" ? 'selected' : '' }}>Mostre</option>
                </select>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="firma" class="mb-0 ps-3">Firma:</label>
                <select name="firma" class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option selected>Selectează</option>
                    <option value="Darimode Style S.R.L." {{ old('firma', $angajat->firma) === "Darimode Style S.R.L." ? 'selected' : '' }}>Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L." {{ old('firma', $angajat->firma) === "Petit Atelier S.R.L." ? 'selected' : '' }}>Petit Atelier S.R.L.</option>
                </select>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="prod" class="mb-0 ps-3">Prod:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('prod') ? 'is-invalid' : '' }}"
                    name="prod"
                    placeholder=""
                    value="{{ old('prod', $angajat->prod) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="ore_angajare" class="mb-0 ps-3">Ore angajare:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('ore_angajare') ? 'is-invalid' : '' }}"
                    name="ore_angajare"
                    placeholder=""
                    value="{{ old('ore_angajare', $angajat->ore_angajare) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="avans" class="mb-0 ps-3">Avans:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('avans') ? 'is-invalid' : '' }}"
                    name="avans"
                    placeholder=""
                    value="{{ old('avans', $angajat->avans) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="foaie_pontaj" class="mb-0 ps-3">Foaie pontaj:</label>
                <select name="foaie_pontaj" class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option selected>Selectează</option>
                    <option value="Darimode Style S.R.L." {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Darimode Style S.R.L." ? 'selected' : '' }}>Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L." {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Petit Atelier S.R.L." ? 'selected' : '' }}>Petit Atelier S.R.L.</option>
                    <option value="Darimode Magazin Depozit - DO" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Darimode Magazin Depozit - DO" ? 'selected' : '' }}>Darimode Magazin Depozit - DO</option>
                    <option value="Kids Ooutlet Depozit Darimode" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Kids Ooutlet Depozit Darimode" ? 'selected' : '' }}>Kids Ooutlet Depozit Darimode</option>
                    <option value="Kids Outlet Depozit Petit" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Kids Outlet Depozit Petit" ? 'selected' : '' }}>Kids Outlet Depozit Petit</option>
                    <option value="Petit Magazin depozit - DO" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Petit Magazin depozit - DO" ? 'selected' : '' }}>Petit Magazin depozit - DO</option>
                </select>
            </div>
            <div class="col-lg-6 mb-4 mx-auto d-flex align-items-center justify-content-center">
                <div class="">
                    <div class="form-check">
                        <input class="form-check-input" type="hidden" name="activ" value="0" />
                        <input class="form-check-input" type="checkbox" value="1" name="activ" id="activ"
                            {{ old('activ', $angajat->activ) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="activ">
                            Cont activ
                        </label>
                    </div>
                    {{-- <div style="line-height: 100%">
                        <small>
                            * Această bifă nu are efect asupra pontajului sau a normelor lucrate, ci doar stabilește dacă angajatul se poate conecta in aplicație!
                        </small>
                    </div> --}}
                </div>
            </div>
        </div>

{{-- @php
    dd($angajati);
@endphp --}}
        {{-- Gestionarea pontatorilor angajatului --}}
        <div class="row" id="angajati">
            <script type="application/javascript">
                angajati = {!! json_encode($angajati) !!}
                // angajatPontatori={!! json_encode(\Illuminate\Support\Arr::flatten(old('angajat_pontatori', $angajat->angajati_pontatori->pluck('nume', 'id')->toArray() ?? [] ))) !!}
                angajatPontatori={!! json_encode(old('angajat_pontatori', $angajat->angajati_pontatori->pluck('id')->toArray() ?? [] )) !!}
                // angajatPontatori={!! json_encode(old('angajat_pontatori', $angajat->angajati_pontatori->toArray() ?? [] )) !!}
                </script>


            <div v-for="pontator in angajat_pontatori.length" :key="pontator" class="col-lg-12">
                <div class="form-row align-items-start mb-2" style="background-color:#005757; border-radius: 10px 10px 10px 10px;">
                    <div class="col-lg-6">
                        <label for="pontator_nume" class="col-form-label col-form-label-sm mb-0 py-0 mr-2">Nume și prenume:</label>
                        {{-- <input type="text"
                            class="form-control form-control-sm"
                            :name="'angajat_pontatori[' + pontator + ']'"
                            v-model="angajat_pontatori[pontator-1].nume"> --}}
                                Pontator @{{ pontator }}:
                                <br>

                                                        <select class="custom-select-sm custom-select {{ $errors->has('angajat_pontatori') ? 'is-invalid' : '' }}"
                                                            name="'angajat_pontatori[' + pontator + ']'"
                                                            v-model="angajat_pontatori[pontator-1]"
                                                            >
                                                            <option disabled value="">Selectează un pontator</option>
                                                            <option
                                                                v-for='angajat in angajati'
                                                                :value='angajat.id'
                                                                >
                                                                    @{{angajat.nume}}
                                                            </option>
                                                        </select>
                                <br>
                                <button  type="button" class="btn m-0 p-0 mb-1" @click="stergeAngajat(pontator-1)">
                                    <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                        Șterge pontatorul
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        {{-- <div class="row">
                            <div class="form-group col-lg-3">
                                <label for="adulti_nume" class="col-form-label col-form-label-sm mb-0 py-0 mr-2">Nume și prenume:</label>
                                <input type="text"
                                    class="form-control form-control-sm"
                                    :name="'adulti[nume][' + adult + ']'"
                                    v-model="adulti_nume[adult-1]">
                            </div>
                            <div class="col-lg-3">
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>


            @foreach ( $angajat->angajati_pontatori as $angajat)
                {{ $angajat->nume }}
            @endforeach

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/angajati">Renunță</a>
            </div>
        </div>
    </div>
</div>
