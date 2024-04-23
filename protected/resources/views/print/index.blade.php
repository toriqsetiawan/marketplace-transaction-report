@extends('layouts.app')

@section('htmlheader_title')
    Cetak Laporan
@stop

@section('contentheader_title')
    Cetak Laporan
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
                    <a href="{{ route('print.index', 'type=all') }}" class="btn btn-info">
                        <i class="fa fa-search"></i> All
                    </a>
                    <a href="{{ route('print.index', 'type=bulanan') }}" class="btn btn-success">
                        <i class="fa fa-search"></i> Month
                    </a>
                    <a href="{{ route('print.index', 'type=mingguan') }}" class="btn btn-warning">
                        <i class="fa fa-search"></i> Week
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
                                <th class="text-center">Action</th>
                                {{-- <th>No</th> --}}
                                <th>Nama</th>
                                <th>Golongan</th>
                                <th>Setor</th>
                                <th>Bon</th>
                                <th>Updated</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td class="text-center">
                                        @if($key->reportPrint)
                                        <button type="button" class="btn btn-primary btn-xs btn-print" data-href="{{ route('print.edit', $key->reportPrint->id) }}">
                                            <i class="fa fa-print"></i> {{-- Cetak --}}
                                        </button>
                                        @endif
                                        <a href="{{ route('print.show', $key->id) }}"
                                           class="btn btn-xs btn-info" title="Detail">
                                            <i class="fa fa-bar-chart-o"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs btn-delete" data-href="{{ route('transaction.recovery', ['id' => $key->id]) }}">
                                            {{-- <i class="fa fa-trash"></i> --}} Recovery
                                        </button>
                                    </td>
                                    <td>{{ $key->nama }}</td>
                                    <td>{{ ucfirst($key->golongan) }}</td>
                                    <td>{{ number_format($key->report->sum('total'), 0, ',', '.') }}</td>
                                    <td>{{ is_null($key->bon) ? '0' : number_format($key->bon->detail->sum('sub_total'), 0, ',', '.') }}</td>
                                    <td>{{ $key->updated_at }}</td>
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
                        {{-- {{ method_field('DELETE') }} --}}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title">Recovery Data</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure want to recovery this data?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-infp pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Recovery</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <div class="example-modal">
        <div class="modal" id="myModalPrint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="get" id="printForm">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Cetak Data</h4>
                        </div>
                        <div class="modal-body">
                            <p>Silahkan tentukan enter untuk merapikan hasil cetakan.</p>
                            <div class="form-group">
                                <label for="phone">Enter Setor ke Bon</label>
                                <select name="first_enter" class="form-control">
                                    <option value="no">Tidak</option>
                                    <option value="yes">Ya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone">Enter Bon ke Detail</label>
                                <select name="second_enter" class="form-control">
                                    <option value="no">Tidak</option>
                                    <option value="yes">Ya</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-infp pull-left" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Cetak</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@stop
