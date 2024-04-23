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
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : ((request('page') - 1) * $data->perPage()) + ++$i }}</td>
                                    <td>{{ $key->employee->nama }}</td>
                                    <td>{{ number_format($key->tunai, 0, ',', '.') }}</td>
                                    <td>{{ number_format($key->giro, 0, ',', '.') }}</td>
                                    <td>{{ dateIndonesia(strtotime($key->created_at)) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs btn-delete"
                                                data-href="{{ route('print.edit', $key->id) }}">
                                            <i class="fa fa-print"></i> Cetak
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
