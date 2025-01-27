@extends('layouts.app')

@section('htmlheader_title')
    Input Penjualan
@stop

@section('contentheader_title')
    Input Penjualan
@stop

@section('main-content')
    <div class="row">
        <div class="col-xs-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('success') }}.
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('error') }}.
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <input type="text" name="daterange" class="form-control" value="" style="width: 17rem; margin: 1rem 0"/>
                    <div class="box-tools">
                        <a href="{{ route('penjualan.create') }}" class="btn btn-primary" style="margin: 1rem 0">
                            <i class="fa fa-plus-circle"></i> Tambah
                        </a>
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Channel</th>
                                <th>Marketplace</th>
                                <th>Produk</th>
                                <th>Ukuran</th>
                                <th>Warna/Motif</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                    </td>
                                    <td>{{ $key->channel == 'user' ? $key->mitra->nama : $key->name }}</td>
                                    <td>{{ $key->channel }}</td>
                                    <td>{{ $key->channel == 'online' ? $key->configFee->marketplace : '-' }}</td>
                                    <td>{{ $key->product->nama }}</td>
                                    <td>{{ $key->ukuran }}</td>
                                    <td>{{ $key->motif }}</td>
                                    <td>{{ $key->jumlah }}</td>
                                    <td>{{ $key->status }}</td>
                                    <td>{{ $key->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('penjualan.edit', $key->id) }}" class="btn btn-xs btn-info"
                                            title="Update">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs btn-delete"
                                                data-href="{{ route('penjualan.destroy', $key->id) }}">
                                            <i class="fa fa-trash"></i>
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
                        </table>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer text-right">
                    <div class="pull-left" style="margin-top: 20px">
                        <strong>Total data : {!! $data->total() !!}</strong>
                    </div>
                    <div class="pull-right">
                        {!! $data->appends(request()->all())->links() !!}
                    </div>
                </div>
            </div><!-- /.box -->
        </div>
    </div>
    <div class="example-modal">
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="deleteForm">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
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
@stop
