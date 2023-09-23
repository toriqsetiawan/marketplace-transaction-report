@extends('layouts.app')

@section('htmlheader_title')
    Ubah Produk
@endsection

@section('contentheader_title')
    Ubah Produk
@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    Data anda telah diubah.
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
        <form role="form" method="post" action="{{ route('product.update', $data->id) }}">
            {!! csrf_field() !!}
            {!! method_field('put') !!}
            <div class="col-md-5">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="nama">SKU</label>
                            <input type="text" id="sku" name="sku" class="form-control" value="{{ $data->sku ?? old('sku') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="password">Nama produk</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="{{ $data->nama ?? old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="ukuran">Ukuran</label>
                            <select class="form-control" id="ukuran" name="ukuran">
                                <option hidden>Pilih ukuran</option>
                                @foreach($ukuran as $key)
                                    <option value="{{ $key }}" {{ $data->ukuran == $key ? 'selected':'' }}>{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga beli</label>
                            <input type="text" id="harga_beli" name="harga_beli" class="form-control money" value="{{ $data->harga_beli ?? old('harga_beli') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga packing</label>
                            <input type="text" id="harga_tambahan" name="harga_tambahan" class="form-control money" value="{{ $data->harga_tambahan ?? old('harga_tambahan') }}">
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
