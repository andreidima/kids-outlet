@csrf

<input type="hidden" id="last_url" name="last_url" value="{{ $last_url }}">

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-2">
                <label for="produs_id" class="mb-0 ps-3">Produs:</label>
                <select name="produs_id"
                    class="form-select form-select-sm rounded-pill {{ $errors->has('produs_id') ? 'is-invalid' : '' }}"
                >
                        <option value='' selected>Selectează</option>
                    @foreach ($produse as $produs)
                        <option
                            value='{{ $produs->id }}'
                            {{ ($produs->id == old('produs_id', $produs_operatie->produs->id ?? '')) ? 'selected' : '' }}
                        >{{ $produs->nume }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12 mb-2">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $produs_operatie->nume) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-2">
                <label for="timp" class="mb-0 ps-3">Timp:</label>
                <vue2-datepicker
                    data-veche="{{ old('timp', ($produs_operatie->timp ?? '')) }}"
                    nume-camp-db="timp"
                    tip="time"
                    value-type="HH:mm"
                    format="HH:mm"
                    :latime="{ width: '90px' }"
                ></vue2-datepicker>
            </div>
            <div class="col-lg-4 mb-2">
                <label for="pret" class="mb-0 ps-3">Preț:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('pret') ? 'is-invalid' : '' }}"
                    name="pret"
                    placeholder=""
                    value="{{ old('pret', $produs_operatie->pret) }}">
            </div>
            <div class="col-lg-4 mb-2">
                <label for="norma" class="mb-0 ps-3">Norma:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('norma') ? 'is-invalid' : '' }}"
                    name="norma"
                    placeholder=""
                    value="{{ old('norma', $produs_operatie->norma) }}">
            </div>
            <div class="col-lg-12 mb-2">
                <label for="observatii" class="mb-0 ps-3">Observații:</label>
                <textarea class="form-control form-control-sm {{ $errors->has('observatii') ? 'is-invalid' : '' }}"
                    name="observatii" rows="2">{{ old('observatii', $produs_operatie->observatii) }}</textarea>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/produse-operatii">Renunță</a>
            </div>
        </div>
    </div>
</div>
