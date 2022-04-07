@csrf
{{-- {{ ($norma_lucrata->angajat_id === null) ? 'dada' : 'nunu'; }} --}}

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px" id="app1">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            @if (!$norma_lucrata->angajat)
                <div class="col-lg-8 mb-4">
                    <label for="angajat_id" class="mb-0 ps-3">Angajat:</label>
                    <select name="angajat_id"
                        class="form-select form-select-sm rounded-3 {{ $errors->has('angajat_id') ? 'is-invalid' : '' }}"
                        {{ ($norma_lucrata->angajat_id === null) ? '' : 'disabled' }}
                    >
                            <option value='' selected>Selectează</option>
                        @foreach ($angajati as $angajat)
                            <option
                                value='{{ $angajat->id }}'
                                {{ ($angajat->id == old('angajat_id', $norma_lucrata->angajat->id ?? $angajat_id ?? '')) ? 'selected' : '' }}
                            >{{ $angajat->nume }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 mb-4">
                    <label for="data" class="mb-0 ps-1">Data:</label>
                        <vue2-datepicker
                            data-veche="{{ old('data', ($norma_lucrata->data ?? $data ?? '')) }}"
                            nume-camp-db="data"
                            tip="date"
                            value-type="YYYY-MM-DD"
                            format="DD-MM-YYYY"
                            :latime="{ width: '125px' }"
                            {{-- disabled --}}
                        ></vue2-datepicker>
                </div>
            @else
                <div class="col-lg-12 px-4 mb-4 ">
                    Angajat: <b>{{ $norma_lucrata->angajat->nume }}</b>
                    <input class="" type="hidden" name="angajat_id" value="{{ $norma_lucrata->angajat_id }}" />
                </div>
                <div class="col-lg-12 px-4 mb-4 ">
                    Data: <b>{{ $norma_lucrata->data ? \Carbon\Carbon::parse($norma_lucrata->data)->isoFormat('DD.MM.YYYY') : '' }}</b>
                    <input class="" type="hidden" name="data" value="{{ $norma_lucrata->data }}" />
                </div>
            @endif
            <div class="col-lg-12 mb-4">
                <label for="produs_id" class="mb-0 ps-3">Produs:</label>
                <select name="produs_id"
                    class="form-select form-select-sm rounded-3 {{ $errors->has('produs_id') ? 'is-invalid' : '' }}"
                    {{-- {{ ($norma_lucrata->angajat_id === null) ? '' : 'disabled' }} --}}
                >
                        <option value='' selected>Selectează</option>
                    @foreach ($produse as $produs)
                        <option
                            value='{{ $produs->id }}'
                            {{ ($produs->id == old('produs_id', $norma_lucrata->produs_operatie->produs->id ?? '')) ? 'selected' : '' }}
                        >{{ $produs->nume }} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12 mb-4">
                <label for="numar_de_faza" class="mb-0 ps-3">Număr de fază:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-3 {{ $errors->has('numar_de_faza') ? 'is-invalid' : '' }}"
                    name="numar_de_faza"
                    placeholder=""
                    value="{{ old('numar_de_faza', $norma_lucrata->produs_operatie->numar_de_faza ?? '') }}"
                    {{-- {{ ($norma_lucrata->angajat_id === null) ? '' : 'disabled' }} --}}
                    >
            </div>
            <div class="col-lg-12 mb-4 mx-auto">
                <label for="cantitate" class="mb-0 ps-3">Cantitate:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-3 {{ $errors->has('cantitate') ? 'is-invalid' : '' }}"
                    name="cantitate"
                    placeholder=""
                    value="{{ old('cantitate', $norma_lucrata->cantitate) }}">
            </div>
        </div>

        <div class="row py-2 justify-content-center">
            <div class="col-lg-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white me-2 rounded-3">{{ $buttonText }}</button>
                <a class="btn btn-secondary rounded-3" href="{{ Session::get('norme_lucrate_return_url') }}">Renunță</a>
            </div>
        </div>
    </div>
</div>
