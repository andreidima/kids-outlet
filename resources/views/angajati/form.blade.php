@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
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
                    <option value="Moda">Moda</option>
                    <option value="Mostre">Mostre</option>
                    <option value="Sectie">Sectie</option>
                </select>
            </div>
            <div class="col-lg-6 mb-4">
                <label for="sectia" class="mb-0 ps-3">Firma:</label>
                <select name="sectia" class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option selected>Selectează</option>
                    <option value="Darimode Style S.R.L.">Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L.">Petit Atelier S.R.L.</option>
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
                <label for="sectia" class="mb-0 ps-3">Foaie pontaj:</label>
                <select name="sectia" class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}">
                    <option selected>Selectează</option>
                    <option value="Darimode Style S.R.L.">Darimode Style S.R.L.</option>
                    <option value="Petit Atelier S.R.L.">Petit Atelier S.R.L.</option>
                    <option value="Darimode Magazin Depozit - DO">Darimode Magazin Depozit - DO</option>
                    <option value="Kids Ooutlet Depozit Darimode">Kids Ooutlet Depozit Darimode</option>
                    <option value="Kids Outlet Depozit Petit">Kids Outlet Depozit Petit</option>
                    <option value="Petit Magazin depozit - DO">Petit Magazin depozit - DO</option>
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
                    <div style="line-height: 100%">
                        <small>
                            * Această bifă nu are efect asupra pontajului sau a normelor lucrate, ci doar stabilește dacă angajatul se poate conecta in aplicație!
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/angajati">Renunță</a>
            </div>
        </div>
    </div>
</div>
