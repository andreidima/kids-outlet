@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-2">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $angajat->nume) }}"
                    required>
            </div>
            <div class="col-lg-12 mb-2">
                <label for="telefon" class="mb-0 ps-3">Telefon:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('telefon') ? 'is-invalid' : '' }}"
                    name="telefon"
                    placeholder=""
                    value="{{ old('telefon', $angajat->telefon) }}"
                    required>
            </div>
            <div class="col-lg-12">
                <label for="cod_de_acces" class="mb-0 ps-3">Cod de acces:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cod_de_acces') ? 'is-invalid' : '' }}"
                    name="cod_de_acces"
                    placeholder=""
                    value="{{ old('cod_de_acces', $angajat->cod_de_acces) }}"
                    required>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/angajati">Renunță</a>
            </div>
        </div>
    </div>
</div>
