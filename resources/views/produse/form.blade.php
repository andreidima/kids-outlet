@csrf

<div class="row mb-0 d-flex border-radius: 0px 0px 40px 40px">
    <div class="col-lg-12 px-2 mb-0">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <label for="nume" class="mb-0 ps-3">Nume:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nume') ? 'is-invalid' : '' }}"
                    name="nume"
                    placeholder=""
                    value="{{ old('nume', $produs->nume) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="cantitate" class="mb-0 ps-3">Cantitate:*</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('cantitate') ? 'is-invalid' : '' }}"
                    name="cantitate"
                    placeholder=""
                    value="{{ old('cantitate', $produs->cantitate) }}"
                    required>
            </div>
            <div class="col-lg-3 mb-4">
                <label for="sectia" class="mb-0 ps-3">Sectia:*</label>
                <select name="sectia" class="form-select" aria-label="Sectia">
                    <option selected></option>
                    <option value="Sectie" {{ (old('sectia', $produs->sectia) == 'Sectie') ? 'selected' : '' }}>
                        Sectie
                    </option>
                    <option value="Mostre" {{ (old('sectia', $produs->sectia) == 'Mostre') ? 'selected' : '' }}>
                        Mostre
                    </option>
                </select>
            </div>
            <div class="col-lg-3 mb-4 mx-auto d-flex align-items-center justify-content-center">
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


        {{-- Gestionarea operatiilor produsului --}}
        <div class="row" id="produs">
            <script type="application/javascript">
                operatii={!! json_encode(old('operatii', $produs->produse_operatii->pluck('id')->toArray() ?? [] )) !!}
                // operatiiNumereDeFazaVechi={!! json_encode(\Illuminate\Support\Arr::flatten(old('operatii.numereDeFaza', ($produs->produse_operatii['numereDeFaza'] ?? [])))) !!}
                // operatiiNumeVechi={!! json_encode(\Illuminate\Support\Arr::flatten(old('operatii.nume', ($produs->produse_operatii['nume'] ?? [])))) !!}
                nrOperatii = 5;
            </script>
            <div class="col-lg-12">
                <label for="nrOperatii" class="mb-0 ps-3">Nr. operații:</label>
                <input
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('nrOperatii') ? 'is-invalid' : '' }}"
                    name="nrOperatii"
                    placeholder=""
                    v-model="nrOperatii"
                    required>
                <label for="nrOperatii" class="mb-0 ps-3">Excel:</label>
                <textarea class="form-control {{ $errors->has('xls') ? 'is-invalid' : '' }}"
                    name="xls" v-model="xls" rows="2"></textarea>
                {{-- <textarea
                    type="text"
                    class="form-control form-control-sm rounded-pill {{ $errors->has('xls') ? 'is-invalid' : '' }}"
                    name="xls"
                    placeholder=""
                    v-model="xls"
                    required> --}}

            <table>
                <tr v-for="(operatie, index) in nrOperatii">
                    <td>
                        <input type="text">
                        <input type="text">
                        <input type="text">
                        <input type="text">
                        <input type="text">
                        <input type="text">

                    </td>
                </tr>
            </table>
            {{-- <div v-for="(pontator, index) in angajat_pontatori.length" class="col-lg-6">
                <div class="row rounded-3 p-2">
                    <div class="col-lg-6" style="background-color: darkcyan">
                        <label :for="'angajat_pontatori[' + index + ']'" class="mb-0 ps-3 text-white">Pontator @{{ index+1 }}:</label>
                        <select class="form-select rounded-pill mb-2 {{ $errors->has('angajat_pontatori') ? 'is-invalid' : '' }}"
                            :name="'angajat_pontatori[' + index + ']'"
                            v-model="angajat_pontatori[index]"
                            >
                            <option
                                v-for='angajat in angajati'
                                :value='angajat.id'
                                >
                                    @{{angajat.nume}}
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 d-flex align-items-end" style="background-color: darkcyan">
                        <button  type="button" class="btn m-0 p-0 mb-1" @click="angajat_pontatori.splice(index, 1)">
                            <span class="px-1" style="background-color:red; color:white; border-radius:20px">
                                Șterge pontatorul
                            </span>
                        </button>
                    </div>
                </div>
            </div> --}}
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
