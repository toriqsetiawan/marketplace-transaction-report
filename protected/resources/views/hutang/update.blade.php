@extends('layouts.app')

@section('htmlheader_title')
    Edit Hutang
@endsection

@section('contentheader_title')
    Edit Hutang
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

        <form role="form" method="post" action="{{ route('cicilan.update', $data->id) }}">
            {!! csrf_field() !!}
            {!! method_field('PUT') !!}
            <input type="hidden" name="employee" value="{{ $employee->id }}">
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                            Harga tidak menggunakan titik atau koma.<br>
                            contoh : 10000
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label>Nama Karyawan</label>
                            <input type="text" class="form-control" value="{{ $employee->nama }}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Golongan</label>
                            <input type="text" class="form-control"
                                   value="{{ ucfirst($employee->golongan) }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nama">Rincian Hutang</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                   value="{{ $data->nama ?? old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="text" id="harga" name="harga" class="form-control"
                                   value="{{  $data->harga ?? old('harga') }}">
                        </div>
                        <div class="form-group">
                            <label for="angsuran">Jumlah Angsuran</label>
                            <input type="int" id="angsuran" name="angsuran" class="form-control"
                                   value="{{ $data->angsuran ?? old('angsuran') }}">
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
