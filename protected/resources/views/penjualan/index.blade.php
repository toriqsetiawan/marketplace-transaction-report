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
                    <a href="{{ route('penjualan.index', 'type=all') }}" class="btn btn-info">
                        <i class="fa fa-search"></i> Semua
                    </a>
                    <a href="{{ route('penjualan.index', 'type=bulanan') }}" class="btn btn-success">
                        <i class="fa fa-search"></i> Bulanan
                    </a>
                    <a href="{{ route('penjualan.index', 'type=mingguan') }}" class="btn btn-warning">
                        <i class="fa fa-search"></i> Mingguan
                    </a>
                    <div class="box-tools">
                        <form action="?" method="get">
                            <div class="input-group" style="width: 200px;">
                                <input type="text" name="search" class="form-control input-sm pull-right"
                                       placeholder="Search" value="{{ request('search') }}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Golongan</th>
                                <th>Updated</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : ((request('page') - 1) * 10) + ++$i }}</td>
                                    <td>{{ $key->nama }}</td>
                                    <td>{{ ucfirst($key->golongan) }}</td>
                                    <td>{{ $key->updated_at }}</td>
                                    <td>
                                        <a href="{{ route('penjualan.show', $key->id) }}"
                                           class="btn btn-xs btn-info" title="Cicilan">
                                            <i class="fa fa-table"></i> Cicilan
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
