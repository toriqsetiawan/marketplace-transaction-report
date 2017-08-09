@extends('layouts.app')

@section('htmlheader_title')
    Detail Print
@stop

@section('contentheader_title')
    Detail Print - {{ $employee->nama }}
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
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tunai</th>
                                <th>Giro</th>
                                <th>Tanggal Rekap</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : ((request('page') - 1) * 10) + ++$i }}</td>
                                    <td>{{ $key->employee->nama }}</td>
                                    <td>{{ number_format($key->tunai, 0, ',', '.') }}</td>
                                    <td>{{ number_format($key->giro, 0, ',', '.') }}</td>
                                    <td>{{ dateIndonesia(strtotime($key->created_at)) }}</td>
                                    <td>
                                        <a href="{{ route('print.edit', $key->id) }}" class="btn btn-xs btn-primary">
                                            <i class="fa fa-print"></i> Cetak
                                        </a>
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
@stop
