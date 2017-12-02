@extends('layouts.app')

@section('htmlheader_title')
    Tambah Bon
@endsection

@section('contentheader_title')
    Tambah Bon
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
                            <th>Action</th>
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
                                <td>
                                    <a href="{{ route('transaction.bon.edit', ['id' => $key->id]) }}"
                                       class="btn btn-xs btn-primary" title="Edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-xs btn-delete" data-href="{{ route('transaction.bon.delete', ['id' => $key->id]) }}">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </td>
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

        <form role="form" method="post" action="{{ route('transaction.bon.create') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="type" value="bon">
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
                                    <option value="{{ $item->id }}">{{ ucwords($item->nama.' - '.number_format($item->harga_satuan) . '/' .$item->taxonomi->nama) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Jumlah</label>
                            <input type="text" id="quantity" name="quantity" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="date_at">Tanggal Bon</label>
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
    <div class="example-modal">
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="deleteForm">
                        {{ csrf_field() }}
                        {{-- {{ method_field('DELETE') }} --}}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title">Delete Data</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure want to delete this data?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-infp pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@endsection
