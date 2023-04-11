@extends ('layouts.app')

@section('content')
<div class="container card" style="border-radius: 40px 40px 40px 40px;">
        <div class="row card-header align-items-center" style="border-radius: 40px 40px 0px 0px;">
            <div class="col-lg-12">
                <h4 class="mb-0">Produse operatii update din excel</a></h4>
            </div>
        </div>

        <div class="card-body px-0 py-3">

            @include ('errors')

            @foreach ($produseOperatiiDeUpdatat->groupBy('produs_id') as $produseOperatiiDeUpdatatPerProdus)
                <div class="table-responsive rounded">
                    <table class="table table-striped table-hover table-sm rounded">
                        <thead class="text-white rounded" style="background-color:#e66800;">
                            <tr class="" style="padding:2rem">
                                <th colspan="4">
                                    Produs:
                                    Id = {{ $produseOperatiiDeUpdatatPerProdus->first()->produsOperatie->produs->id }}
                                    /
                                    Nume = {{ $produseOperatiiDeUpdatatPerProdus->first()->produsOperatie->produs->nume }}
                                </th>
                            <tr class="" style="padding:2rem">
                                <th>Nr. fază</th>
                                <th>Operație</th>
                                <th>Preț vechi</th>
                                <th>Preț nou</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $pret_total_vechi = 0;
                                $pret_total_nou = 0;
                            @endphp
                            @forelse ($produseOperatiiDeUpdatatPerProdus as $operatie)
                                @php
                                    $pret_total_vechi += $operatie->produsOperatie->pret ?? 0;
                                    $pret_total_nou += $operatie->pret;
                                @endphp
                                <tr>
                                    <td align="">
                                        {{ $operatie->produsOperatie->numar_de_faza ?? ''}}
                                    </td>
                                    <td>
                                        @if ($operatie->nume === ($operatie->produsOperatie->nume ?? ''))
                                            {{ $operatie->nume }}
                                        @else
                                            <p class="bg-dark text-danger">
                                            {{-- @forelse ($operatie->produsOperatie->istoricuri as $istoric)
                                                {{ $istoric->nume }}
                                            @empty
                                            @endforelse --}}
                                                {{ $operatie->produsOperatie->nume ?? '' }}
                                                ->
                                                {{ $operatie->nume }}
                                            </p>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $operatie->produsOperatie->pret ?? '' }}
                                    </td>
                                    <td>
                                        {{ $operatie->pret ?? '' }}
                                    </td>
                                </tr>
                            @empty
                                {{-- <div>Nu s-au gasit rezervări în baza de date. Încearcă alte date de căutare</div> --}}
                            @endforelse
                                <tr>
                                    <td colspan="2" class="text-end">
                                        Total
                                    </td>
                                    <td>
                                        {{ $pret_total_vechi }}
                                    </td>
                                    <td>
                                        {{ $pret_total_nou }}
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach


            <div class="row">
                <div class="col-lg-12 text-center">
                    <a class="btn btn-primary rounded-3" href="/import/update-faze-produse/update">Actualizează</a>
                </div>
            </div>

        </div>
    </div>
@endsection
