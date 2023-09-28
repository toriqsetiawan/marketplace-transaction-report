@extends('layouts.app')

@section('htmlheader_title')
    Tambah Konfigurasi biaya admin
@endsection

@section('contentheader_title')
    Tambah Konfigurasi biaya admin
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
        <form role="form" method="post" action="{{ route('config-fee.store') }}">
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
                            <label for="marketplace">Marketplace</label>
                            <select class="form-control" id="marketplace" name="marketplace">
                                <option hidden>Pilih marketplace</option>
                                @foreach(['shopee', 'tiktok', 'tokopedia', 'lazada'] as $key)
                                    <option value="{{ $key }}" {{ old('marketplace') == $key ? 'selected':'' }}>{{ $key }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Biaya admin</label>
                            <div style="display: flex">
                                <input type="text" placeholder="Biaya admin %" name="persentase" class="form-control" value="0.101">
                            </div>
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
