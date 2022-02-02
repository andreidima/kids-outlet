<!DOCTYPE  html>
<html lang="ro">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pontaj</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        * {
            /* padding: 0;
            text-indent: 0; */
        }
        table{
            border-collapse:collapse;
            /* margin: 0px 0px; */
            /* margin-left: 5px; */
            margin-top: 0px;
            border-style: solid;
            border-width:0px;
            width: 100%;
            word-wrap:break-word;
            /* word-break: break-all; */
            /* table-layout: fixed; */
            /* page-break-inside: avoid; */
        }
        th, td {
            padding: 5px 5px;
            border-width:1px;
            border-style: solid;
            table-layout:fixed;
            font-weight: normal;
        }
        tr {
            /* text-align:; */
            /* border-style: solid;
            border-width:1px; */
        }
        hr {
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 0.5px;
        }
        /* tr:nth-child(even) {background-color:lightgray;} */
    </style>
</head>

<body>
            <div style="
                width:1030px;
                min-height:600px;
                padding: 0px 0px 0px 0px;
                margin:0px 0px;
                    -moz-border-radius: 10px;
                    -webkit-border-radius: 10px;
                    border-radius: 10px;">

                <div style="border:dashed #999; border-radius: 25px; padding:0px 20px">
                    <h3 style="">

                    </h3>


                    <h2 style="text-align: center">
                        Raport Pontaj
                        <br>
                        {{ $search_data_inceput = \Carbon\Carbon::parse($search_data_inceput)->isoFormat('DD.MM.YYYY') }}
                        -
                        {{ $search_data_sfarsit = \Carbon\Carbon::parse($search_data_sfarsit)->isoFormat('DD.MM.YYYY') }}
                    </h2>
                </div>

                <br><br><br><br>

            <table class="">
                <tr class="" style="padding:2rem">
                    <th style="min-width: 170px;">Nume</th>
                    @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                        <th class="text-center" style="min-width: 120px;">
                            {{ \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isoFormat('DD') }}
                        </th>
                    @endfor
                    <th style="">Total</th>
                </tr>
                </thead>
                <tbody>

                @forelse ($angajati as $angajat)
                    @php
                        $timp_total = \Carbon\Carbon::today();
                    @endphp
                    <tr>
                        <td style="">
                            {{ $angajat->nume }}
                        </td>
                        @for ($ziua = 0; $ziua <= \Carbon\Carbon::parse($search_data_sfarsit)->diffInDays($search_data_inceput); $ziua++)
                            <td style="text-align:center">
                                @if (\Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->isWeekday())
                                    @forelse ($angajat->pontaj->where('data', \Carbon\Carbon::parse($search_data_inceput)->addDays($ziua)->toDateString()) as $pontaj)
                                        @switch($pontaj->concediu)
                                            @case(0)
                                                @if ($pontaj->ora_sosire && $pontaj->ora_plecare)
                                                    @php
                                                        // se calculaeaza secundele lucrate
                                                        $secunde = \Carbon\Carbon::parse($pontaj->ora_plecare)->diffInSeconds(\Carbon\Carbon::parse($pontaj->ora_sosire));
                                                        // daca sunt mai mult de 8 ore, se reduce la 8 ore
                                                        ($secunde > 28800) ? $secunde = 28800 : '';
                                                        // se aduna la timpul total
                                                        $timp_total->addSeconds($secunde)
                                                    @endphp
                                                    {{
                                                        \Carbon\Carbon::parse($secunde)->isoFormat('HH:mm')
                                                    }}
                                                {{-- @else --}}
                                                    {{-- 00:00 --}}
                                                @endif
                                                @break
                                            @case(1)
                                                    C.M.
                                                @break
                                            @case(2)
                                                    C.O.
                                                @break
                                            @case(3)
                                                    C.F.P.
                                                @break
                                        @endswitch
                                    @empty
                                    @endforelse
                                @endif
                            </td>
                        @endfor
                        <td style="text-align:center">
                            {{
                                number_format(\Carbon\Carbon::parse($timp_total)->floatDiffInHours(\Carbon\Carbon::today()), 4)
                            }}
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>


                {{-- Here's the magic. This MUST be inside body tag. Page count / total, centered at bottom of page --}}
                <script type="text/php">
                    if (isset($pdf)) {
                        $text = "Pagina {PAGE_NUM} / {PAGE_COUNT}";
                        $size = 10;
                        $font = $fontMetrics->getFont("DejaVu Sans");
                        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                        $x = ($pdf->get_width() - $width) / 2;
                        $y = $pdf->get_height() - 35;
                        $pdf->page_text($x, $y, $text, $font, $size);
                    }
                </script>


            </div>




</body>

</html>
