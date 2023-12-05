@extends('layouts.app')

@section('contentheader_title')
    Report Penjualan
@endsection

@section('htmlheader_title')
    Report Penjualan
@endsection

@section('main-content')
    <div>
        <div class="box">
            <div class="box-header">
                <div class="d-block my-3">
                    <input type="text" name="daterange" class="form-control" value=""
                        style="width: 17rem; margin: 1rem 0" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>Shopee</h3>
                    <h4>Rp.
                        {{ array_key_exists(1, $data['online']) ? number_format(collect($data['online'][1])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=shopee') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>Tokopedia</h3>
                    <h4>Rp.
                        {{ array_key_exists(3, $data['online']) ? number_format(collect($data['online'][3])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-bitcoin"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=tokopedia') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>Lazada</h3>
                    <h4>Rp.
                        {{ array_key_exists(4, $data['online']) ? number_format(collect($data['online'][4])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-euro"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=lazada') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-black">
                <div class="inner">
                    <h3>Tiktok</h3>
                    <h4>Rp.
                        {{ array_key_exists(2, $data['online']) ? number_format(collect($data['online'][2])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-yen text-muted"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=tiktok') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>Mitra</h3>
                    <h4>Rp.
                        @php
                            $totalMitra = 0;
                        @endphp
                        @if (array_key_exists('mitra', $data))
                            @foreach ($data['mitra'] as $item)
                                @php
                                    $totalMitra += collect($item)->sum();
                                @endphp
                            @endforeach
                        @endif
                        {{ number_format($totalMitra) }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-whatsapp"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=mitra') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>Offline</h3>
                    <h4>Rp.
                        {{ array_key_exists('offline', $data) ? number_format(collect($data['offline'])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=offline') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>Keuntungan</h3>
                    <h4> Rp.
                        @php
                            $keuntungan = 0;
                            foreach ($data as $item => $value) {
                                if (in_array($item, ['online', 'offline', 'mitra'])) {
                                    foreach ($value as $key) {
                                        $keuntungan += collect($key)->sum();
                                    }
                                }
                            }
                        @endphp
                        {{ number_format($keuntungan) }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode=trading') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @foreach ($supplier as $item)
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $item->nama }}</h3>
                    <h4>Total bayar Rp.
                        {{ array_key_exists($item->nama, $data) ? number_format(collect($data[$item->nama])->sum() ?? 0) : 0 }}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
                <a href="{{ url('report-penjualan/1?mode='. $item->nama) }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
@endsection
