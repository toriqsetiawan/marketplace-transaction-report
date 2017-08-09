@extends('layouts.app')

@section('htmlheader_title')
    Tambah Varian
@endsection

@section('contentheader_title')
    Tambah Varian
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
        <form role="form" method="post" action="{{ route('varian.store') }}">
            {!! csrf_field() !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                            Harga per satuan tidak menggunakan titik atau koma.<br>
                            contoh : 10000
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="nama">Nama varian</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="{{ old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga satuan</label>
                            <input type="text" id="harga" name="harga_satuan" class="form-control" value="{{ old('harga_satuan') }}">
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan</label>
                            <select class="form-control" id="satuan" name="taxonomi_id">
                                <option hidden>Pilih satuan</option>
                                @foreach($satuan as $key)
                                    <option value="{{ $key->id }}" {{ old('taxonomi_id') == $key->id ? 'selected':'' }}>{{ ucfirst($key->nama) }}</option>
                                @endforeach
                            </select>
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
