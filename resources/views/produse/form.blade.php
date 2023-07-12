@csrf

<script type="application/javascript">
    xls =  {!! json_encode(old('xls') ?? '') !!}
</script>

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


    {{-- @if (str_contains(url()->current(), '/adauga')) --}}
    {{-- @if (old('operatii'))
        @forelse (old('operatii') as $operatie)
            {{ $operatie[1] }} . {{ $operatie[2] }} <br>
        @empty
        @endforelse
    @endif --}}
        {{-- Gestionarea operatiilor produsului --}}
        <div class="row" id="produs">
            <script type="application/javascript">
                operatii={!! json_encode(old('operatii', $produs->produse_operatii->pluck('id')->toArray() ?? [] )) !!}
            </script>
            <div class="col-lg-12 my-2 mb-5">
                <label for="nrOperatii" class="mb-0 ps-3">
                    @if (str_contains(url()->current(), '/modifica'))
                        Dacă doriți să faceți modificări și la faze, adaugați aici fazele.
                        <br>
                    @endif
                    Excel (copiați din excel și după inserați aici doar fazele, fără cap de tabel sau totaluri):
                </label>
                <textarea class="form-control {{ $errors->has('xls') ? 'is-invalid' : '' }}"
                    name="xls" v-model="xls" rows="4"></textarea>
                <div class="my-2 text-center">
                    <button type="button" v-on:click="formatCells" class="btn btn-success text-white me-2 rounded-pill">
                        Generează Operații
                    </button>
                </div>

                <div v-if="operatii.length" class="table-responsive rounded">
                    <table class="table table-info table-sm table-borderless rounded-3" width="1270">
                        <tr class="">
                            <th class="text-center" width="3%">nr crt</th>
                            <th class="text-center" width="12%">Descrierea operației</th>
                            <th class="text-center" width="4%">Timp</th>
                            <th class="text-center" width="4%">Preț</th>
                            <th class="text-center" width="4%"><small>Preț pe minut</small></th>
                            <th class="text-center" width="4%">Timp total</th>
                            <th class="text-center" width="6%">Norma</th>
                            <th class="text-center" width="6%"><small>Preț 100% pe minut</small></th>
                            <th class="text-center" width="6%"><small>Preț 100% pe faze</small></th>
                            <th class="text-center" width="4%"></th>
                        </tr>
                        {{-- <tr v-for="(operatie, index) in nrOperatii"> --}}
                        <tr v-for="(index) in operatii.length">
                            {{-- <td v-for="i in 10"> --}}
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][1]'" v-model="operatii[index-1][0]" style="width: 100%;"></td>
                            <td><input type="text" class="text-start" :name="'operatii[' + index + '][2]'" v-model="operatii[index-1][1]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][3]'" v-model="operatii[index-1][2]" @keyup="updateTotaluri()" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][4]'" v-model="operatii[index-1][3]" @keyup="updateTotaluri()" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][5]'" v-model="operatii[index-1][4]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][6]'" v-model="operatii[index-1][5]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][7]'" v-model="operatii[index-1][6]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][8]'" v-model="operatii[index-1][7]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][9]'" v-model="operatii[index-1][8]" style="width: 100%"></td>
                            <td><input type="text" class="text-end" :name="'operatii[' + index + '][10]'" v-model="operatii[index-1][9]" style="width: 100%"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input type="text" class="text-end" v-model="timp_total" style="width: 100%" readonly disabled></td>
                            <td><input type="text" class="text-end" v-model="pret_total" style="width: 100%" readonly disabled></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    {{-- @endif --}}

        <div class="row py-2 justify-content-center">
            <div class="col-lg-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary text-white btn-sm me-2 rounded-pill">{{ $buttonText }}</button>
                {{-- <a class="btn btn-secondary btn-sm mr-4 rounded-pill" href="{{ $client_neserios->path() }}">Renunță</a>  --}}
                <a class="btn btn-secondary btn-sm rounded-pill" href="/produse">Renunță</a>
            </div>
        </div>
    </div>
</div>
