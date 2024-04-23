@extends('layouts.app')

@section('htmlheader_title')
    Daftar Cicilan
@stop

@section('contentheader_title')
    Daftar Cicilan - {{ $employee->nama }}
@stop

@section('main-content')
    <div class="row">
        <div class="col-xs-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('success') }}.
                </div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('error') }}.
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <a href="{{ route('cicilan.create').'?employee='.$employee->id }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Tambah Hutang
                    </a>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis Hutang</th>
                                <th>Status</th>
                                <th>Jumlah Hutang</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : ((request('page') - 1) * $data->perPage()) + ++$i }}</td>
                                    <td>{{ $key->employee->nama }}</td>
                                    <td>{{ $key->nama }}</td>
                                    <td>{{ $key->status }}</td>
                                    <td>{{ number_format($key->harga, 0, ',', '.') }}</td>
                                    <td>{{ dateIndonesia(strtotime($key->created_at)) }}</td>
                                    <td>
                                        <a type="button" class="btn btn-primary btn-xs"
                                                href="{{ route('cicilan.edit', $key->id) }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-info btn-xs btn-delete"
                                                data-href="{{ url('cicilan/print').'?id='.$key->id }}">
                                            <i class="fa fa-table"></i> Detail
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
                    <form action="" method="get" id="deleteForm">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Detail Pembayaran</h4>
                        </div>
                        <div class="modal-body">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-infp pull-left" data-dismiss="modal">Kembali</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@stop
