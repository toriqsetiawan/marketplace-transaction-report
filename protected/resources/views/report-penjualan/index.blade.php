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
                    {{ session('error') }}
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <input type="text" name="daterange" class="form-control" value=""
                        style="width: 17rem; margin: 1rem 0" />
                    <div class="box-tools" x-data="{
                        user: '{{ request('user') }}',
                        search() {
                            window.location.href = `{{ route('report-penjualan.index') }}?user=${this.user}`
                        }
                    }">
                        <select name="user" id="user" class="form-control" x-model="user" @change="search()">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
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
                        @php
                            $totalPrice = 0;
                            $totalBuyPrice = 0;
                            $totalQuantity = 0;
                            $totalProfit = 0;
                        @endphp
                        @forelse($data as $key)
                            @php
                                $totalPrice += $key['total_price'];
                                $totalBuyPrice += $key['total_buy_price'];
                                $totalQuantity += $key['total_quantity'];
                                $totalProfit += $key['total_price'] - $key['total_buy_price'];
                            @endphp
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
                                        'user' => request('user'),
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
                        @if (count($data))
                            <tr style="background: lightgrey">
                                <td colspan="2"><b>Total</b></td>
                                <td class="text-right"><b>{{ number_format($totalPrice) }}</b></td>
                                <td class="text-right"><b>{{ number_format($totalBuyPrice) }}</b></td>
                                <td class="text-center"><b>{{ number_format($totalQuantity) }}</b></td>
                                <td class="text-right"><b>{{ number_format($totalProfit) }}</b></td>
                                <td></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="box">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th>Supplier Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-right">Total</th>
                        </tr>
                        @php
                            $totalData = [];
                        @endphp
                        @foreach (collect($supplierSales)->sort() as $supplierName => $summary)
                            <tr>
                                <td>{{ $supplierName }}</td>
                                <td class="text-center">{{ number_format($summary['totalQuantity']) }}</td>
                                <td class="text-right">
                                    {{ number_format($summary['totalPrice'], 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $totalData[$supplierName]['totalQuantity'] = $summary['totalQuantity'];
                                $totalData[$supplierName]['totalPrice'] = $summary['totalPrice'];
                            @endphp
                        @endforeach
                        <tr>
                            <th colspan="3">Return</th>
                        </tr>
                        @foreach (collect($supplierReturn)->sort() as $supplierName => $return)
                            <tr>
                                <td>{{ $supplierName }}</td>
                                <td class="text-center">{{ number_format($return['totalQuantity']) }}</td>
                                <td class="text-right">
                                    {{ number_format($return['totalPrice'], 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $totalData[$supplierName]['totalQuantity'] -= $return['totalQuantity'];
                                $totalData[$supplierName]['totalPrice'] -= $return['totalPrice'];
                            @endphp
                        @endforeach

                        <tr>
                            <th colspan="3">Total</th>
                        </tr>

                        @foreach ($totalData as $supplierName => $summary)
                            <tr>
                                <td>{{ $supplierName }}</td>
                                <td class="text-center">{{ number_format($summary['totalQuantity']) }}</td>
                                <td class="text-right">
                                    {{ number_format($summary['totalPrice'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div><!-- /.box-body -->
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
