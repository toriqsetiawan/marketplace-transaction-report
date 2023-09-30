@extends('layouts.app')

@section('htmlheader_title')
    Tambah Penjualan
@endsection

@section('contentheader_title')
    Tambah Penjualan
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

        <form role="form" method="post" action="{{ route('penjualan.store') }}">
            {!! csrf_field() !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Channel</label>
                            <select name="channel" id="channel" class="form-control select2">
                                @foreach (['online', 'offline', 'mitra'] as $channel)
                                    <option value="{{ $channel }}">{{ $channel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group marketplace-block">
                            <label>Marketplace</label>
                            <select name="marketplace" id="marketplace" class="form-control select2">
                                @foreach ($marketplaces as $marketplace)
                                    <option value="{{ $marketplace->id }}">{{ $marketplace->marketplace }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Nama customer</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label>Produk</label>
                            <select name="product_id" id="product_id" class="form-control select2">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ "$product->nama [$product->ukuran]" }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="int" id="jumlah" name="jumlah" class="form-control" min="1"
                                   value="{{ old('jumlah') }}">
                        </div>
                        <div class="form-group">
                            <label for="ukuran">Ukuran</label>
                            <select name="ukuran" id="ukuran" class="form-control select2">
                                @foreach (range(26,43) as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="motif">Warna / Motif</label>
                            <input type="text" id="motif" name="motif" class="form-control"
                                   value="{{ old('motif') }}">
                        </div>
                        <div class="form-group">
                            <label for="insole">Insole</label>
                            <select name="insole" id="insole" class="form-control select2">
                                <option value="1" {{ old('insole') == 1 ? 'selected':'' }}>Ya</option>
                                <option value="0" {{ old('insole') == 0 ? 'selected':'' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_at">Tanggal</label>
                            <input type="date" id="date_at" name="date_at" class="form-control"
                                   value="{{ date('Y-m-d') }}">
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
