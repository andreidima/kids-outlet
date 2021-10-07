@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <label for="angajat_id" class="mb-0 ps-3">Angajat:</label>
                <select name="angajat_id"
                    class="form-select form-select-sm rounded-pill {{ $errors->has('angajat_id') ? 'is-invalid' : '' }}"
                >
                        <option value='' selected>Selectează</option>
                    @foreach ($angajati as $angajat)
                        <option
                            value='{{ $angajat->id }}'
                            {{ ($angajat->id == old('angajat_id', $norma_lucrata->angajat->id ?? '')) ? 'selected' : '' }}
                        >{{ $angajat->nume }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 mb-2">
                <label for="numar_de_faza" class="mb-0 ps-3">Număr de fază:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('numar_de_faza') ? 'is-invalid' : '' }}"
                    name="numar_de_faza"
                    placeholder=""
                    value="{{ old('numar_de_faza', $norma_lucrata->numar_de_faza) }}">
            </div>
            <div class="col-lg-6 mb-2">
                <label for="cantitate" class="mb-0 ps-3">Cantitate:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cantitate') ? 'is-invalid' : '' }}"
                    name="cantitate"
                    placeholder=""
                    value="{{ old('cantitate', $norma_lucrata->cantitate) }}">
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                <a class="btn btn-secondary btn-sm rounded-pill" href="/norme-lucrate">Renunță</a>
            </div>
        </div>
    </div>
</div>
