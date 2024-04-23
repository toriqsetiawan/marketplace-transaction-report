@extends('layouts.app')

@section('htmlheader_title')
    Tambah Produk
@endsection

@section('contentheader_title')
    Tambah Produk
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
        <form role="form" method="post" action="{{ route('product.store') }}">
            {!! csrf_field() !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    {{-- <div class="box-header with-border">
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                            Harga per satuan tidak menggunakan titik atau koma.<br>
                            contoh : 10000
                        </div>
                    </div><!-- /.box-header --> --}}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control" id="supplier_id" name="supplier_id">
                                <option hidden>Pilih supplier</option>
                                @foreach($supplier as $key)
                                    <option value="{{ $key->id }}" {{ old('supplier_id') == $key->id ? 'selected':'' }}>{{ $key->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama">SKU</label>
                            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}">
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama produk</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="ukuran">Ukuran</label>
                            <select class="form-control" id="ukuran" name="ukuran">
                                <option hidden>Pilih ukuran</option>
                                @foreach($ukuran as $key)
                                    <option value="{{ $key }}" {{ old('ukuran') == $key ? 'selected':'' }}>{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga beli</label>
                            <input type="text" id="harga_beli" name="harga_beli" class="form-control money" value="{{ old('harga_beli') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga packing</label>
                            <input type="text" id="harga_tambahan" name="harga_tambahan" class="form-control money" value="{{ old('harga_tambahan') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga online</label>
                            <input type="text" id="harga_online" name="harga_online" class="form-control money" value="{{ old('harga_online') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga offline</label>
                            <input type="text" id="harga_offline" name="harga_offline" class="form-control money" value="{{ old('harga_offline') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga mitra</label>
                            <input type="text" id="harga_mitra" name="harga_mitra" class="form-control money" value="{{ old('harga_mitra') }}">
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
