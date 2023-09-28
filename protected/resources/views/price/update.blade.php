@extends('layouts.app')

@section('htmlheader_title')
    Ubah Konfigurasi biaya admin
@endsection

@section('contentheader_title')
    Ubah Konfigurasi biaya admin
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
        <form role="form" method="post" action="{{ route('config-fee.update', $data->id) }}">
            {!! csrf_field() !!}
            {!! method_field('put') !!}
            <div class="col-md-5">
                <div class="box box-warning">
                    <div class="box-body">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="marketplace">Marketplace</label>
                                <select class="form-control" id="marketplace" name="marketplace">
                                    <option hidden>Pilih marketplace</option>
                                    @foreach(['shopee', 'tiktok', 'tokopedia', 'lazada'] as $key)
                                        <option value="{{ $key }}" {{ $data->marketplace == $key ? 'selected':'' }}>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="harga">Biaya admin</label>
                                <div style="display: flex">
                                    <input type="text" placeholder="Biaya admin %" name="persentase" class="form-control" value="{{ $data->persentase }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary pull-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
