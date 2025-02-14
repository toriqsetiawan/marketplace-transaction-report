@extends('layouts.app')

@section('htmlheader_title')
    Report
@stop

@section('contentheader_title')
    Report
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
                    <input type="text" name="daterange" class="form-control" value=""
                        style="width: 17rem; margin: 1rem 0" />
                    <div class="box-tools">
                        {{-- <a href="{{ route('penjualan.create') }}" class="btn btn-primary" style="margin: 1rem 0">
                            <i class="fa fa-plus-circle"></i> Create
                        </a> --}}
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th class="text-right">Total Sell</th>
                            <th class="text-right">Total Buy</th>
                            <th class="text-center">Total Quantity</th>
                            <th class="text-right">Total Profit</th>
                            <th class="text-center">Action</th>
                        </tr>
                        @forelse($data as $key)
                            <tr>
                                <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($key['date'])->format('d F Y') }}</td>
                                <td class="text-right">{{ number_format($key['total_price']) }}</td>
                                <td class="text-right">{{ number_format($key['total_buy_price']) }}</td>
                                <td class="text-center">{{ number_format($key['total_quantity']) }}</td>
                                <td class="text-right">{{ number_format($key['total_price'] - $key['total_buy_price']) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('report-penjualan.show', [
                                        'report_penjualan' => 1,
                                        'start_date' => \Carbon\Carbon::parse($key['date'])->format('d/m/Y'),
                                        'end_date' => \Carbon\Carbon::parse($key['date'])->format('d/m/Y'),
                                    ]) }}"
                                        class="btn btn-xs btn-info" title="Detail">
                                        <i class="fa fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    Tidak ada data yang ditampilkan
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div><!-- /.box-body -->
                {{-- <div class="box-footer text-right">
                    <div class="pull-left" style="margin-top: 20px">
                        <strong>Total data : {!! $data->total() !!}</strong>
                    </div>
                    <div class="pull-right">
                        {!! $data->appends(request()->all())->links() !!}
                    </div>
                </div> --}}
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
