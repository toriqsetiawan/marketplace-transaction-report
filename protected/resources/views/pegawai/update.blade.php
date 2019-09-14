@extends('layouts.app')

@section('htmlheader_title')
    Ubah Pegawai
@endsection

@section('contentheader_title')
    Ubah Pegawai
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
        <form role="form" method="post" action="{{ route('employee.update', $data->id) }}">
            {!! csrf_field() !!}
            {!! method_field('put') !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                            Nomor handphone hanya menggunakan angka dan jangan ada spasi.<br>
                            contoh: 085234149966
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" class="form-control" value="{{ $data->nama or old('nama') }}">
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="form-control" value="{{ $data->alamat or old('alamat') }}">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ $data->phone or old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label for="golongan">Golongan</label>
                            <select class="form-control" name="golongan" id="golongan">
                                <option hidden>Pilih golongan</option>
                                <option value="bulanan" {{ $data->golongan == 'bulanan' ? 'selected':'' }}>Bulanan</option>
                                <option value="mingguan" {{ $data->golongan == 'mingguan' ? 'selected':'' }}>Mingguan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="trx_type">Tipe Setor/Bon</label>
                            <select class="form-control" name="trx_type" id="trx_type">
                                <option hidden>Pilih Setor/Bon</option>
                                <option value="setor" {{ $employee_log->type == 'setor' ? 'selected':'' }}>Setor</option>
                                <option value="bon" {{ $employee_log->type == 'bon' ? 'selected':'' }}>Bon</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Aktif / Non-aktif</label>
                            <select class="form-control" name="status" id="status">
                                <option hidden>Pilih Setor/Bon</option>
                                <option value="1" {{ $data->is_active == 1 ? 'selected':'' }}>Aktif</option>
                                <option value="0" {{ $data->is_active == 0 ? 'selected':'' }}>Non-aktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="total_trx">Total</label>
                            <input type="text" id="total_trx" name="total_trx" class="form-control money" value="{{ $employee_log->amount or old('total_trx') }}">
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
