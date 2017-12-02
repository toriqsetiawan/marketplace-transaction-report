<!DOCTYPE html>
<html>
    <head>
        <title>Rekap {{ $data->employee->nama.' - '. dateIndonesia(strtotime(date('Y-m-d'))) }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css" media="all">
            table {
                border-collapse: collapse;
                border-spacing: 0;
                background-color: transparent;
                width: 100%;
            }
            .invoice-box {
                width: 100%;
                padding:5px;
                /*border:1px solid #eee;*/
                font-size:12px;
                font-family:'Helvetica Neue',Helvetica,Helvetica,Arial,sans-serif;
                color:#000;
            }
            span.title {
                font-weight: bold;
                font-size: 24px;
            }
            p.employee {
                font-weight: bold;
                font-size: 18px;
            }
            .header {
                width: 100%;
                text-align: center;
                display: block;
                clear: both;
                margin-bottom: 50px
            }
            hr {
                margin:10px 0 0;
                border: 0;
                border-top: 1px solid #eee;
                border-bottom: 0;
                box-sizing: content-box;
                height: 0;
            }
            .invoice-box table {
                text-align:left
            }
            .invoice-box table td {
                padding:5px;
                vertical-align:top
            }
            .invoice-box table tr td:nth-child(5) {
                text-align:right
            }
            .invoice-box table tr.information table td {
                padding-bottom:20px;
                border-bottom: 1px solid #ddd;
            }
            .invoice-box table tr.heading td {
                background:#eee;
                border-bottom:1px solid #ddd;
                font-weight:700
            }
            .invoice-box table tr.item td {
                border-bottom:1px solid #eee
            }
            .invoice-box table tr.item.last td {
                border-bottom:none
            }
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        <div class="invoice-box">
            <div class="header">
                <span class="title">UD.SONY JAYA</span>    
                <div>
                    <div style="width: 50%; text-align: left; float: left;">
                        <p class="employee">{{ $data->employee->nama }}</p>
                    </div>
                    <div style="width: 50%; text-align: right; float: left;">
                        <p>{{ dateIndonesia(strtotime(date('Y-m-d'))) }}</p>
                    </div>
                </div>
            </div>
            <table cellpadding="0" cellspacing="0">
                <tr class="heading">
                    <td style="width: 2%">
                        No
                    </td>
                    <td style="width: 22%">
                        Tanggal Setor
                    </td>
                    <td style="width: 22%">
                        Setoran ke
                    </td>
                    <td style="width: 22%">
                        Jumlah
                    </td>
                    <td style="text-align: center; width: 22%">
                        Total
                    </td>
                </tr>

                @php
                    $total_setor = $total_kodi = 0;
                @endphp
                @foreach($setor as $key)
                <tr class="item">
                    <td>
                        {{ ++$i }}
                    </td>
                    <td>
                        {{ dateIndonesia(strtotime($key->date_at)) }}
                    </td>
                    <td>
                        {{ $key->count }}
                    </td>
                    <td>
                        {{ $key->kodi + 0 }} Kodi
                    </td>
                    <td style="text-align: right;">
                        Rp. {{ number_format($key->total, 0, ',', '.') }}
                        @php
                            $total_kodi += $key->kodi;
                            $total_setor += $key->total;
                        @endphp
                    </td>
                </tr>
                @endforeach
                <tr class="item last">
                    <td colspan="2">
                        <strong style="padding-left: 84%">Total </strong>
                    </td>
                    <td></td>
                    <td>
                       <strong>{{ $total_kodi + 0 }} Kodi</strong>
                    </td>
                    <td style="text-align: right;">
                       <strong>Rp. {{ number_format($total_setor, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
            <br>
            @if($first_enter)
            <div class="page-break"></div>
            @endif
            <table cellpadding="0" cellspacing="0">
                <tr class="heading">
                    <td style="width: 2%">
                        No
                    </td>
                    <td style="width: 20%">
                        Tanggal Bon
                    </td>
                    <td style="width: 25%">
                        Nama Barang
                    </td>
                    <td style="width: 15%">
                        Harga
                    </td>
                    <td style="width: 15%">
                        Jumlah
                    </td>
                    <td style="text-align: center; width: 20%">
                        Total
                    </td>
                </tr>

                @php
                    $total_bon = $i = 0;
                @endphp
                <tr class="item">
                    <td>
                        {{ ++$i }}
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        @if($trx_log['type'] == "bon")
                            Sisa Bon Sebelum
                        @elseif($trx_log['type'] == "setor")
                            Sisa Uang Sebelum
                        @endif
                    </td>
                    <td>
                        -
                    </td>
                    <td>
                        -
                    </td>
                    <td style="text-align: right;">
                        Rp. {{ number_format($trx_log['amount'], 0, ',', '.') }}
                    </td>
                </tr>
                @foreach($bon as $key)
                <tr class="item">
                    <td>
                        {{ ++$i }}
                    </td>
                    <td>
                        {{ dateIndonesia(strtotime($key->date_at)) }}
                    </td>
                    <td>
                        {{ $key->varian->nama }}
                    </td>
                    <td>
                        {{ $key->varian->kode == "OT" ? '-' : 'Rp. '.number_format($key->price_history, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ $key->varian->kode == "OT" ? '-' : ($key->quantity + 0) . ' ' . $key->varian->taxonomi->nama }}
                    </td>
                    <td style="text-align: right;">
                        Rp. {{ number_format($key->sub_total, 0, ',', '.') }}
                        @php
                            $total_bon +=$key->sub_total;
                        @endphp
                    </td>
                </tr>
                @endforeach

                @if($trx_log['type'] == "bon")
                    @php
                        $total_bon += $trx_log['amount'];
                    @endphp
                @elseif($trx_log['type'] == "setor")
                    @php
                        $total_bon -= $trx_log['amount'];
                    @endphp
                @endif
                <tr class="item last">
                    <td colspan="5">
                        <strong style="padding-left: 80%">Total </strong>
                    </td>
                    <td style="text-align: right;">
                       <strong>Rp. {{ number_format($total_bon, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
            <br>
            @php
                $i = 0;
            @endphp
            @if($second_enter)
            <div class="page-break"></div>
            @endif
            <table cellpadding="0" cellspacing="0">
                <tr class="heading">
                    <td style="width: 15px">
                        No
                    </td>
                    <td>
                        Item
                    </td>
                    <td style="text-align: center">
                        Jumlah
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="item">
                    <td style="width: 15px">
                        <strong>{{ ++$i }}</strong>
                    </td>
                    <td>
                        Total Setor - Total Bon
                    </td>
                    <td style="text-align: right;">
                        <strong>Rp. {{ number_format($total_setor - $total_bon, 0, ',', '.') }}</strong>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="item">
                    <td style="width: 15px">
                        <strong>{{ ++$i }}</strong>
                    </td>
                    <td>
                        Tunai
                    </td>
                    <td style="text-align: right;">
                        <strong>Rp. {{ number_format($data->tunai, 0, ',', '.') }}</strong>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="item">
                    <td style="width: 15px">
                        <strong>{{ ++$i }}</strong>
                    </td>
                    <td>
                        Giro
                    </td>
                    <td style="text-align: right;">
                        <strong>Rp. {{ number_format($data->giro, 0, ',', '.') }}</strong>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="item last">
                    @php
                        $sisa = ($total_setor - $total_bon) - ($data->tunai + $data->giro);
                    @endphp
                    <td colspan="2" style="text-align: center">
                        <strong>{{ $sisa < 0 ? 'Sisa Bon' : 'Sisa Uang' }}</strong>
                    </td>
                    <td style="text-align: right;">
                        <strong>Rp. {{ number_format(abs($sisa), 0, ',', '.') }}</strong>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </body>
</html>
