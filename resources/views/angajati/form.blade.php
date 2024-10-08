@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $angajat->nume) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="telefon" class="mb-0 ps-3">Telefon:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('telefon') ? 'is-invalid' : '' }}"
                    name="telefon"
                    placeholder=""
                    value="{{ old('telefon', $angajat->telefon) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="cod_de_acces" class="mb-0 ps-3">Cod de acces:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('cod_de_acces') ? 'is-invalid' : '' }}"
                    name="cod_de_acces"
                    placeholder=""
                    value="{{ old('cod_de_acces', $angajat->cod_de_acces) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="sectia" class="mb-0 ps-3">Secția:</label>
                <select name="sectia" class="form-select rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option value ="" selected>Selectează</option>
                    <option value="Moda" {{ old('sectia', $angajat->sectia) === "Moda" ? 'selected' : '' }}>Moda</option>
                    <option value="Sectie" {{ old('sectia', $angajat->sectia) === "Sectie" ? 'selected' : '' }}>Sectie</option>
                    <option value="Mostre" {{ old('sectia', $angajat->sectia) === "Mostre" ? 'selected' : '' }}>Mostre</option>
                </select>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="firma" class="mb-0 ps-3">Firma:</label>
                <select name="firma" class="form-select rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option value ="" selected>Selectează</option>
                    <option value="Bensar S.R.L." {{ old('firma', $angajat->firma) === "Bensar S.R.L." ? 'selected' : '' }}>Bensar S.R.L.</option>
                    <option value="Mate Andy Style" {{ old('firma', $angajat->firma) === "Mate Andy Style" ? 'selected' : '' }}>Mate Andy Style</option>
                    <option value="Darimode Style S.R.L." {{ old('firma', $angajat->firma) === "Darimode Style S.R.L." ? 'selected' : '' }}>Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L." {{ old('firma', $angajat->firma) === "Petit Atelier S.R.L." ? 'selected' : '' }}>Petit Atelier S.R.L.</option>
                    <option value="Fără firmă" {{ old('firma', $angajat->firma) === "Fără firmă" ? 'selected' : '' }}>Fără firmă</option>
                </select>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="prod" class="mb-0 ps-3">Prod:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('prod') ? 'is-invalid' : '' }}"
                    name="prod"
                    placeholder=""
                    value="{{ old('prod', $angajat->prod) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="ore_angajare" class="mb-0 ps-3">Ore angajare:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('ore_angajare') ? 'is-invalid' : '' }}"
                    name="ore_angajare"
                    placeholder=""
                    value="{{ old('ore_angajare', $angajat->ore_angajare) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="avans" class="mb-0 ps-3">Avans:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('avans') ? 'is-invalid' : '' }}"
                    name="avans"
                    placeholder=""
                    value="{{ old('avans', $angajat->avans) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="foaie_pontaj" class="mb-0 ps-3">Foaie pontaj:</label>
                <select name="foaie_pontaj" class="form-select rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option value ="" selected>Selectează</option>
                    <option value="Darimode Style S.R.L." {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Darimode Style S.R.L." ? 'selected' : '' }}>Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L." {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Petit Atelier S.R.L." ? 'selected' : '' }}>Petit Atelier S.R.L.</option>
                    <option value="Darimode Magazin Depozit - DO" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Darimode Magazin Depozit - DO" ? 'selected' : '' }}>Darimode Magazin Depozit - DO</option>
                    <option value="Kids Ooutlet Depozit Darimode" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Kids Ooutlet Depozit Darimode" ? 'selected' : '' }}>Kids Ooutlet Depozit Darimode</option>
                    <option value="Kids Outlet Depozit Petit" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Kids Outlet Depozit Petit" ? 'selected' : '' }}>Kids Outlet Depozit Petit</option>
                    <option value="Petit Magazin depozit - DO" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Petit Magazin depozit - DO" ? 'selected' : '' }}>Petit Magazin depozit - DO</option>
                    <option value="Mate Andy Style" {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Mate Andy Style" ? 'selected' : '' }}>Mate Andy Style</option>
                    <option value="Bensar S.R.L." {{ old('foaie_pontaj', $angajat->foaie_pontaj) === "Bensar S.R.L." ? 'selected' : '' }}>Bensar S.R.L.</option>
                </select>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="limba_aplicatie" class="mb-0 ps-3">Limba:</label>
                <select name="limba_aplicatie" class="form-select rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option value ="" selected>Selectează</option>
                    <option value="1" {{ old('limba_aplicatie', $angajat->limba_aplicatie) == "1" ? 'selected' : '' }}>Română</option>
                    <option value="2" {{ old('limba_aplicatie', $angajat->limba_aplicatie) == "2" ? 'selected' : '' }}>Singaleză</option>
                    {{ old('limba_aplicatie') }}
                </select>
            </div>
            <div class="col-lg-3 mb-4 d-flex align-items-center justify-content-center">
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
                            * Se stabilește dacă angajatul se poate conecta in aplicație și va apărea în raportul pentru salarii!
                        </small>
                    </div> --}}
                </div>
            </div>
        </div>

        {{-- Gestionarea pontatorilor angajatului --}}
        <div class="row mb-4" id="angajati">
            <script type="application/javascript">
                angajati = {!! json_encode($angajati) !!}
                // angajatPontatori={!! json_encode(\Illuminate\Support\Arr::flatten(old('angajat_pontatori', $angajat->angajati_pontatori->pluck('nume', 'id')->toArray() ?? [] ))) !!}
                angajatPontatori={!! json_encode(old('angajat_pontatori', $angajat->angajati_pontatori->pluck('id')->toArray() ?? [] )) !!}
                // angajatPontatori={!! json_encode(old('angajat_pontatori', $angajat->angajati_pontatori->toArray() ?? [] )) !!}
            </script>

            <div v-for="(pontator, index) in angajat_pontatori.length" class="col-lg-6">
                <div class="row rounded-3 p-2">
                    <div class="col-lg-6" style="background-color: darkcyan">
                        <label :for="'angajat_pontatori[' + index + ']'" class="mb-0 ps-3 text-white">Pontator @{{ index+1 }}:</label>
                        <select class="form-select rounded-pill mb-2 {{ $errors->has('angajat_pontatori') ? 'is-invalid' : '' }}"
                            :name="'angajat_pontatori[' + index + ']'"
                            v-model="angajat_pontatori[index]"
                            >
                            <option
                                v-for='angajat in angajati'
                                :value='angajat.id'
                                >
                                    @{{angajat.nume}}
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 d-flex align-items-end" style="background-color: darkcyan">
                        <button  type="button" class="btn m-0 p-0 mb-1" @click="angajat_pontatori.splice(index, 1)">
                            <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                Șterge pontatorul
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row rounded-3 p-2">
                    <div class="col-lg-12 p-3 d-flex align-items-end justify-content-center" style="background-color: darkcyan">
                        <button  type="button" class="btn m-0 p-0 mb-1" @click="angajat_pontatori.push(undefined)">
                            <span class="px-1" style="background-color:rgb(255, 255, 255); color:rgb(0, 160, 13); border-radius:20px">
                                Adaugă pontator
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2" style="background-color:rgb(192, 216, 248)">
            <div class="col-lg-12 py-2 text-center">
                <span class="px-2 bg-info text-white rounded-3 fs-4">
                    Date bancare
                </span>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="banca_angajat_nume" class="mb-0 ps-3">Angajat nume:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('banca_angajat_nume') ? 'is-invalid' : '' }}"
                    name="banca_angajat_nume"
                    placeholder=""
                    value="{{ old('banca_angajat_nume', $angajat->banca_angajat_nume) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="banca_angajat_cnp" class="mb-0 ps-3">Angajat CNP:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('banca_angajat_cnp') ? 'is-invalid' : '' }}"
                    name="banca_angajat_cnp"
                    placeholder=""
                    value="{{ old('banca_angajat_cnp', $angajat->banca_angajat_cnp) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="banca_iban" class="mb-0 ps-3">IBAN:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('banca_iban') ? 'is-invalid' : '' }}"
                    name="banca_iban"
                    placeholder=""
                    value="{{ old('banca_iban', $angajat->banca_iban) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="banca_detalii_1" class="mb-0 ps-3">Detalii 1:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('banca_detalii_1') ? 'is-invalid' : '' }}"
                    name="banca_detalii_1"
                    placeholder=""
                    value="{{ old('banca_detalii_1', $angajat->banca_detalii_1) }}"
                    required>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="banca_detalii_2" class="mb-0 ps-3">Detalii 2:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('banca_detalii_2') ? 'is-invalid' : '' }}"
                    name="banca_detalii_2"
                    placeholder=""
                    value="{{ old('banca_detalii_2', $angajat->banca_detalii_2) }}"
                    required>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary rounded-pill" href="/angajati">Renunță</a>
            </div>
        </div>
    </div>
</div>
