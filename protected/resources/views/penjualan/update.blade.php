@extends('layouts.app')

@section('htmlheader_title')
    Edit Penjualan
@endsection

@section('contentheader_title')
    Edit Penjualan
@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    Data anda telah tersimpan.
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form role="form" method="post" action="{{ route('penjualan.update', $data->id) }}">
            {!! csrf_field() !!}
            {!! method_field('PUT') !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-body">
                        @php
                            $selectedChannel = request('channel') ??  $data->channel;
                        @endphp
                        <div class="form-group">
                            <label>Channel</label>
                            <select name="channel" id="channel" class="form-control select2">
                                @foreach (['online', 'offline', 'user'] as $channel)
                                    <option value="{{ $channel }}" {{ $selectedChannel == $channel ? 'selected' : '' }}>{{ $channel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group marketplace-block" style="display:{{ $selectedChannel == 'online' ? 'block':'none' }}">
                            <label>Marketplace</label>
                            <select name="marketplace" id="marketplace" class="form-control select2">
                                @foreach ($marketplaces as $marketplace)
                                    <option value="{{ $marketplace->id }}" {{ $data->marketplace == $marketplace->id ? 'selected' : '' }}>{{ $marketplace->marketplace }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mitra-block">
                            <label for="name">Nama customer</label>
                            @if ($selectedChannel == 'user')
                                <select name="name" id="name" class="form-control select2">
                                    @foreach ($user as $key)
                                        <option value="{{ $key->id_mitra }}" {{ $key->id_mitra == $data->name ? 'selected':'' }}>{{ $key->nama }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ $data->name ?? old('name') }}">
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Produk</label>
                            <select name="product_id" id="product_id" class="form-control select2">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $data->product_id == $product->id ? 'selected' : '' }}>{{ "$product->nama [$product->ukuran]" }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="int" id="jumlah" name="jumlah" class="form-control" min="1"
                                   value="{{ $data->jumlah ?? old('jumlah') }}">
                        </div>
                        <div class="form-group">
                            <label for="ukuran">Ukuran</label>
                            <select name="ukuran" id="ukuran" class="form-control select2">
                                @foreach (range(26,43) as $item)
                                    <option value="{{ $item }}" {{ $data->ukuran == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="motif">Warna / Motif</label>
                            <input type="text" id="motif" name="motif" class="form-control"
                                   value="{{ $data->motif ?? old('motif') }}">
                        </div>
                        <div class="form-group">
                            <label for="packing">Packing</label>
                            <select name="packing" id="packing" class="form-control select2">
                                <option value="1" {{ $data->biaya_tambahan == 1 ? 'selected':'' }}>Ya</option>
                                <option value="0" {{ $data->biaya_tambahan == 0 ? 'selected':'' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="insole">Insole</label>
                            <select name="insole" id="insole" class="form-control select2">
                                <option value="1" {{ $data->biaya_lain_lain == 1 ? 'selected':'' }}>Ya</option>
                                <option value="0" {{ $data->biaya_lain_lain == 0 ? 'selected':'' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="text" id="harga" name="harga" class="form-control money"
                                value="{{ $data->total_paid ?? old('harga') }}">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control select2">
                                <option value="1" {{ $data->status == 'Pending' ? 'selected':'' }}>Pending</option>
                                <option value="2" {{ $data->status == 'Lunas' ? 'selected':'' }}>Lunas</option>
                                <option value="3" {{ $data->status == 'Retur' ? 'selected':'' }}>Retur</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" class="form-control"
                                   value="{{ $data->keterangan ?? old('keterangan') }}">
                        </div>
                        <div class="form-group">
                            <label for="date_at">Tanggal</label>
                            <input type="date" id="date_at" name="date_at" class="form-control"
                                   value="{{ $data->created_at->format('Y-m-d') ?? date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
