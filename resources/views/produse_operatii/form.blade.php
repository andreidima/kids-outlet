@csrf

<input type="hidden" id="last_url" name="last_url" value="{{ $last_url }}">

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-6 mb-4">
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
            <div class="col-lg-6 mb-4">
                <label for="numar_de_faza" class="mb-0 ps-3">Număr de fază:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('numar_de_faza') ? 'is-invalid' : '' }}"
                    name="numar_de_faza"
                    placeholder=""
                    value="{{ old('numar_de_faza', $produs_operatie->numar_de_faza) }}"
                    required>
            </div>
            <div class="col-lg-12 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $produs_operatie->nume) }}"
                    required>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="timp" class="mb-0 ps-3">Timp:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('timp') ? 'is-invalid' : '' }}"
                    name="timp"
                    placeholder=""
                    value="{{ old('timp', $produs_operatie->timp) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="pret" class="mb-0 ps-3">Preț:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('pret') ? 'is-invalid' : '' }}"
                    name="pret"
                    placeholder=""
                    value="{{ old('pret', $produs_operatie->pret) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="pret_pe_minut" class="mb-0 ps-3">Preț pe minut:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('pret_pe_minut') ? 'is-invalid' : '' }}"
                    name="pret_pe_minut"
                    placeholder=""
                    value="{{ old('pret_pe_minut', $produs_operatie->pret_pe_minut) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="timp_total" class="mb-0 ps-3">Timp total:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('timp_total') ? 'is-invalid' : '' }}"
                    name="timp_total"
                    placeholder=""
                    value="{{ old('timp_total', $produs_operatie->timp_total) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="norma" class="mb-0 ps-3">Norma:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('norma') ? 'is-invalid' : '' }}"
                    name="norma"
                    placeholder=""
                    value="{{ old('norma', $produs_operatie->norma) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="pret_100_pe_minut" class="mb-0 ps-3">Preț 100 pe minut:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('pret_100_pe_minut') ? 'is-invalid' : '' }}"
                    name="pret_100_pe_minut"
                    placeholder=""
                    value="{{ old('pret_100_pe_minut', $produs_operatie->pret_100_pe_minut) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="pret_100_pe_faze" class="mb-0 ps-3">Preț 100 pe faze:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('pret_100_pe_faze') ? 'is-invalid' : '' }}"
                    name="pret_100_pe_faze"
                    placeholder=""
                    value="{{ old('pret_100_pe_faze', $produs_operatie->pret_100_pe_faze) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="J" class="mb-0 ps-3">J:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('J') ? 'is-invalid' : '' }}"
                    name="J"
                    placeholder=""
                    value="{{ old('J', $produs_operatie->J) }}">
                <small class="ps-3">Punct(.) pentru zecimalele</small>
            </div>
            <div class="col-lg-4 mb-4">
                <label for="norma_totala" class="mb-0 ps-3">Norma totală:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('norma_totala') ? 'is-invalid' : '' }}"
                    name="norma_totala"
                    placeholder=""
                    value="{{ old('norma_totala', $produs_operatie->norma_totala) }}">
            </div>
            <div class="col-lg-12 mb-4">
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
