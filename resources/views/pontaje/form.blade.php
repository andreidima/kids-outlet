@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-4">
                {{-- <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <label for="nume" class="mb-0 ps-3">{{ $pontaj->angajat->nume ?? '' }}</label> --}}
                <label for="angajat_id" class="mb-0 ps-3">Angajat:</label>
                <select name="angajat_id"
                    class="form-select form-select-sm rounded-pill {{ $errors->has('angajat_id') ? 'is-invalid' : '' }}"
                >
                        <option value='' selected>Selectează</option>
                    @foreach ($angajati as $angajat)
                        <option
                            value='{{ $angajat->id }}'
                            {{ ($angajat->id == old('angajat_id', $pontaj->angajat->id ?? '')) ? 'selected' : '' }}
                        >{{ $angajat->nume }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 mb-2">
                <label for="data" class="mb-0 ps-1">Data:</label>
                    <vue2-datepicker
                        data-veche="{{ old('data', ($pontaj->data ?? '')) }}"
                        nume-camp-db="data"
                        tip="date"
                        value-type="YYYY-MM-DD"
                        format="DD-MM-YYYY"
                        :latime="{ width: '125px' }"
                    ></vue2-datepicker>
            </div>
            <div class="col-lg-4 mb-2 text-lg-center">
                <label for="data" class="mb-0 ps-1">Ora sosire:</label>
                    <vue2-datepicker
                        data-veche="{{ old('ora_sosire', ($pontaj->ora_sosire ?? '')) }}"
                        nume-camp-db="ora_sosire"
                        tip="time"
                        value-type="HH:mm"
                        format="HH:mm"
                        :latime="{ width: '90px' }"
                    ></vue2-datepicker>
            </div>
            <div class="col-lg-4 mb-2 text-lg-end">
                <label for="data" class="mb-0 pe-2">Ora plecare:</label>
                    <vue2-datepicker
                        data-veche="{{ old('ora_plecare', ($pontaj->ora_plecare ?? '')) }}"
                        nume-camp-db="ora_plecare"
                        tip="time"
                        value-type="HH:mm"
                        format="HH:mm"
                        :latime="{ width: '90px' }"
                    ></vue2-datepicker>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                <a class="btn btn-secondary btn-sm rounded-pill" href="/pontaje">Renunță</a>
            </div>
        </div>
    </div>
</div>
