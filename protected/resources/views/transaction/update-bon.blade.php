@extends('layouts.app')

@section('htmlheader_title')
    Update Bon
@endsection

@section('contentheader_title')
    Update Bon
@endsection

@section('main-content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">List Bon</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control pull-right"
                                   placeholder="Search">

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Harga Sekarang</th>
                            <th>Harga Lama</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                        @forelse($data as $key)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ dateIndonesia(strtotime($key->date_at)) }}</td>
                                <td>{{ ucwords($key->varian->nama) }}</td>
                                <td>{{ $key->varian->kode == "OT" ? '-' : number_format($key->varian->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $key->varian->kode == "OT" ? '-' : number_format($key->price_history, 0, ',', '.') }}</td>
                                <td>{{ $key->varian->kode == "OT" ? '-' : ($key->quantity + 0).' '.$key->varian->taxonomi->nama }}</td>
                                <td>{{ number_format($key->sub_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Tidak ada data yang ditampilkan
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ $success }}.
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

        <form role="form" method="post" action="{{ route('transaction.bon.update') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="type" value="bon">
            <input type="hidden" name="id" value="{{ $update->id }}">
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                            Total tidak menggunakan titik atau koma.<br>
                            contoh : 10000
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="nama">Nama Karyawan</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                   value="{{ $employee->nama }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="golongan">Golongan</label>
                            <input type="text" id="golongan" name="golongan" class="form-control"
                                   value="{{ ucfirst($employee->golongan) }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="variant_id">Barang</label>
                            <select class="form-control select2" name="variant_id" id="variant_id">
                                <option hidden>Pilih barang</option>
                                @foreach($barang as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $update->varian_id ? "selected":""  }}>{{ ucwords($item->nama.' - '.number_format($item->harga_satuan) . '/' .$item->taxonomi->nama) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Jumlah</label>
                            <input type="text" id="quantity" name="quantity" class="form-control" value="{{ $update->quantity or old('quantity')}}">
                        </div>
                        <div class="form-group">
                            <label for="date_at">Tanggal Bon</label>
                            <input type="date" id="date_at" name="date_at" class="form-control"
                                   value="{{ $update->date_at or date('Y-m-d') }}">
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
