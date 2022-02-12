@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $produs->nume) }}"
                    required>
            </div>
            {{-- <div class="col-lg-12 mb-2">
                <label for="client_pret" class="mb-0 ps-3">Client preț:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('client_pret') ? 'is-invalid' : '' }}"
                    name="client_pret"
                    placeholder=""
                    value="{{ old('client_pret', $produs->client_pret) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div> --}}
            {{-- <div class="col-lg-12 mb-2">
                <label for="cost_produs" class="mb-0 ps-3">Cost produs:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cost_produs') ? 'is-invalid' : '' }}"
                    name="cost_produs"
                    placeholder=""
                    value="{{ old('cost_produs', $produs->cost_produs) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div> --}}
            {{-- <div class="col-lg-12 mb-2">
                <label for="cantitate" class="mb-0 ps-3">Cantitate:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cantitate') ? 'is-invalid' : '' }}"
                    name="cantitate"
                    placeholder=""
                    value="{{ old('cantitate', $produs->cantitate) }}">
            </div> --}}
            {{-- <div class="col-lg-12 mb-2">
                <label for="observatii" class="mb-0 ps-3">Observații:</label>
                <textarea class="form-control form-control-sm {{ $errors->has('observatii') ? 'is-invalid' : '' }}"
                    name="observatii" rows="2">{{ old('observatii', $produs->observatii) }}</textarea>
            </div> --}}
            <div class="col-lg-12 mb-4 mx-auto d-flex align-items-center justify-content-center">
                <div class="form-check">
                    <input class="form-check-input" type="hidden" name="activ" value="0" />
                    <input class="form-check-input" type="checkbox" value="1" name="activ" id="activ"
                        {{ old('activ', $produs->activ) == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="activ">
                        Activ
                    </label>
                </div>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/produse">Renunță</a>
            </div>
        </div>
    </div>
</div>
