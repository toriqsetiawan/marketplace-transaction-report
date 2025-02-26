@extends('layouts.app')

@section('htmlheader_title')
    Report penjualan - {{ $ownerName ?? request()->mode }}
@stop

@section('contentheader_title')
    Report penjualan - {{ $ownerName ?? request()->mode }}
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
                    <div class="d-block my-3">
                        <input type="text" name="daterange" class="form-control" value=""
                            style="width: 17rem; margin: 1rem 0" />
                    </div>
                    <div class="box-tools">
                        <form action="?" method="get">
                            <div class="input-group" style="width: 200px;">
                                <input type="text" name="search" class="form-control input-sm pull-right"
                                    placeholder="Search" value="{{ request('search') }}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-default"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                @php
                    $summaryTotal = 0;
                    $summaryHpp = 0;
                    $summaryProfit = 0;
                @endphp
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Customer</th>
                            <th>Product / Variant / Quantity / Sub Total / Hpp</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Hpp</th>
                            <th class="text-center">Profit</th>
                            <th class="text-center">Status</th>
                            <th>Date</th>
                        </tr>
                        @foreach ($transactions as $transaction)
                            @php
                                $hpp = 0;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->transaction_code }}</td>
                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                <td>
                                    <table style="width: 100%;">
                                        @foreach ($transaction->items as $item)
                                            <tr>
                                                <td style="width: 35%">{{ $item->variant?->product?->nama }}</td>
                                                <td style="width: 27%">
                                                    {{ $item->variant?->product?->variants()->where('id', $item->variant_id)->first()->attributeValues()->implode('value', '/') }}
                                                </td>
                                                <td style="width: 3%">{{ $item->quantity }}</td>
                                                <td class="text-right" style="width: 13%">
                                                    {{ number_format($item->price) }}
                                                </td>
                                                <td class="text-right" style="width: 15%">
                                                    Hpp:
                                                    @if ($item->variant?->product?->harga_jual == 0)
                                                        {{ number_format($item->variant?->price) }}
                                                    @else
                                                        {{ number_format($item->variant?->product?->harga_beli) }}
                                                    @endif
                                                </td>
                                                @php
                                                    if ($item->variant?->product?->harga_jual == 0) {
                                                        $hpp += $item->variant?->price * $item->quantity;
                                                    } else {
                                                        $hpp += $item->variant?->product?->harga_beli * $item->quantity;
                                                    }
                                                @endphp
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td class="text-right">{{ number_format($transaction->total_price) }}</td>
                                <td class="text-right">{{ number_format($hpp) }}</td>
                                <td class="text-right">{{ number_format($transaction->total_price - $hpp) }}</td>
                                <td class="text-center">
                                    @if ($transaction->status == 'pending')
                                        <span class="label label-warning text-uppercase">{{ $transaction->status }}</span>
                                    @elseif ($transaction->status == 'paid')
                                        <span class="label label-success text-uppercase">{{ $transaction->status }}</span>
                                    @elseif ($transaction->status == 'cancel')
                                        <span class="label label-default text-uppercase">{{ $transaction->status }}</span>
                                    @else
                                        <span class="label label-danger text-uppercase">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->created_at->format('d-m-Y') }}</td>
                            </tr>

                            @php
                                $summaryTotal += $transaction->total_price;
                                $summaryHpp += $hpp;
                                $summaryProfit += $transaction->total_price - $hpp;
                            @endphp
                        @endforeach
                        @if ($transactions->count() > 0)
                            <tr>
                                <td colspan="3" class="text-right"><b>Grand Total</b></td>
                                <td class="text-right"><b>{{ number_format($summaryTotal) }}</b></td>
                                <td class="text-right"><b>{{ number_format($summaryHpp) }}</b></td>
                                <td class="text-right"><b>{{ number_format($summaryProfit) }}</b></td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
@stop
