@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $produs->nume) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="cantitate" class="mb-0 ps-3">Cantitate:</label>
                <input
                    type="text"
                    class="form-control rounded-pill {{ $errors->has('cantitate') ? 'is-invalid' : '' }}"
                    name="cantitate"
                    placeholder=""
                    value="{{ old('cantitate', $produs->cantitate) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-4 d-flex align-items-center justify-content-center">
                <div class="">
                    <div class="form-check">
                        <input class="form-check-input" type="hidden" name="activ" value="0" />
                        <input class="form-check-input" type="checkbox" value="1" name="activ" id="activ"
                            {{ old('activ', $produs->activ) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="activ">
                            Produs activ
                        </label>
                    </div>
                    <div style="line-height: 100%">
                        <small>
                            * Această bifă nu are efect asupra normelor lucrate. Normele lucrate se vor calcula la salarii chiar daca produsul este trecut la inactiv!
                        </small>
                    </div>
                </div>
            </div>
        </div>


        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary rounded-pill" href="/aplicatie-angajati/produse">Renunță</a>
            </div>
        </div>
    </div>
</div>
