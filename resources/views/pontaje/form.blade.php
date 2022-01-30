@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-12 mb-4 text-center">
                Angajat: <b>{{ $pontaj->angajat->nume }}</b>
                <input class="" type="hidden" name="angajat_id" value="{{ $pontaj->angajat_id }}" />

                <br>

                Data: <b>{{ $pontaj->data ? \Carbon\Carbon::parse($pontaj->data)->isoFormat('DD.MM.YYYY') : '' }}</b>
                <input class="" type="hidden" name="data" value="{{ $pontaj->data }}" />
            </div>
            {{-- <div class="col-lg-12 mb-4">
                <label for="angajat_id" class="mb-0 ps-3">Angajat:</label>
                <select name="angajat_id"
                    class="form-select rounded-pill {{ $errors->has('angajat_id') ? 'is-invalid' : '' }}"
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
            <div class="col-lg-4 mb-4">
                <label for="data" class="mb-0 ps-1">Data:</label>
                    <vue2-datepicker
                        data-veche="{{ old('data', ($pontaj->data ?? '')) }}"
                        nume-camp-db="data"
                        tip="date"
                        value-type="YYYY-MM-DD"
                        format="DD-MM-YYYY"
                        :latime="{ width: '125px' }"
                    ></vue2-datepicker>
            </div> --}}
            <div class="col-lg-6 mb-4 text-lg-center">
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
            <div class="col-lg-6 mb-5 text-lg-center">
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
            <div class="col-lg-6 ">
                <div class="form-check mb-3">
                    <input class="form-check-input form-check-input-lg" type="radio" value="0" name="concediu" id="prezent_la_muna"
                        {{ (old('concediu', $pontaj->concediu) == '0') || (old('concediu', $pontaj->concediu) == '') ? 'checked' : '' }}>
                    <label class="form-check-label" for="prezent_la_muna">
                        Prezent la muncă
                    </label>
                </div>
            </div>
            <div class="col-lg-6 ">
                <div class="form-check mb-3">
                    <input class="form-check-input form-check-input-lg" type="radio" value="1" name="concediu" id="concediu_medical"
                        {{ old('concediu', $pontaj->concediu) == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="concediu_medical">
                        Concediu medical
                    </label>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" value="2" name="concediu" id="concediu_de_odihna"
                        {{ old('concediu', $pontaj->concediu) == '2' ? 'checked' : '' }}>
                    <label class="form-check-label" for="concediu_de_odihna">
                        Concediu de odihnă
                    </label>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" value="3" name="concediu" id="concediu_fara_plata"
                        {{ old('concediu', $pontaj->concediu) == '3' ? 'checked' : '' }}>
                    <label class="form-check-label" for="concediu_fara_plata">
                        Concediu fără plată
                    </label>
                </div>
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white me-2 rounded-3 border-dark shadow">{{ $buttonText }}</button>
                <a class="btn btn-secondary rounded-3 border-dark shadow" href="{{ Session::get('pontaj_return_url') }}">Renunță</a>
            </div>
        </div>
    </div>
</div>
