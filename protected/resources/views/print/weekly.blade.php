<!DOCTYPE html>
<html>
    <head>
        <title>Rekap Mingguan - {{ dateIndonesia(strtotime(date('Y-m-d'))) }}</title>
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
                font-size:16px;
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
            /*.invoice-box table tr td:nth-child(5) {
                text-align:right
            }*/
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
                border: 1px 1px 0 0 solid #eee
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
                    <td style="width: 15%">
                        Nama
                    </td>
                    <td style="text-align: center; width: 15%">
                        Setor
                    </td>
                    <td style="text-align: center; width: 7%">
                        Kodi
                    </td>
                    <td style="text-align: center; width: 15%">
                        Bon
                    </td>
                    <td style="text-align: center; width: 18%">
                        Minggu Lalu
                    </td>
                    <td style="text-align: center; width: 15%">
                        Total
                    </td>
                </tr>

                @foreach($data as $key)
                <tr class="item">
                    <td>
                        {{ ++$i }}
                    </td>
                    <td>
                        {{ $key->nama }}
                    </td>
                    <td style="text-align: right;">
                        @php
                            $total_setor = $key->report->sum('total');
                        @endphp
                        {{ number_format($total_setor, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right">
                        <?php
                            $kodi = $key->report()->where('created_at', '>=', $startdate)->where('created_at', '<=', $enddate)->sum('kodi');
                            $kodi = number_format($kodi, 0, ',', '.');
                        ?>
                        {{ $kodi }}
                    </td>
                    <td style="text-align: right;">
                        @php
                            $total_bon = is_null($key->bon) ? 0 : $key->bon->detail->sum('sub_total');
                        @endphp
                        {{ is_null($key->bon) ? '0' : number_format($total_bon, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right">
                        @php
                            $weekly_status = false;
                            $sisa_uang = $sisa_bon = 0;

                            if ($key->log->type == "bon") {
                                $sisa_bon = $key->log->correction;
                            } else {
                                $sisa_uang = $key->log->correction;
                                $weekly_status = true;
                            }
                        @endphp
                        @if($weekly_status)
                            Sisa : {{ number_format($sisa_uang, 0, ',', '.') }}
                        @else
                            Bon : {{ number_format($sisa_bon, 0, ',', '.') }}
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @php
                            $total = 0;
                            if ($weekly_status) {
                                $total = ($total_setor + $sisa_uang) - $total_bon;
                            } else {
                                $total = $total_setor - ($total_bon + $sisa_bon);
                            }
                        @endphp
                        <b>{{ number_format($total, 0, ',', '.') }}</b>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </body>
</html>
