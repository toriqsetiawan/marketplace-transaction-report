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
                <div class="box-body table-responsive no-padding">
                    @php
                        // Initialize summary variables
                        $totalPrice = 0;
                        $totalBuyPrice = 0;
                        $totalQuantity = 0;
                    @endphp

                    @foreach ($transactions as $transaction)
                        <div>
                            <h4>Transaction ID: {{ $transaction->id }}</h4>
                            <p>User: {{ $transaction->user->name ?? 'N/A' }}</p>
                            <p>Total Price: {{ $transaction->total_price }}</p>
                            <p>Created At: {{ $transaction->created_at->format('d/m/Y H:i') }}</p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Buy Price</th>
                                        <th>Sell Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->items as $item)
                                        <tr>
                                            <td>{{ $item->variant->product->nama ?? 'N/A' }}</td>
                                            <td>{{ $item->variant->product->harga_beli ?? 0 }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                        @php
                                            // Add to summary
                                            $totalPrice += $item->price * $item->quantity;
                                            $totalBuyPrice +=
                                                ($item->variant->product->harga_beli ?? 0) * $item->quantity;
                                            $totalQuantity += $item->quantity;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                    @endforeach

                    <!-- Display Summary -->
                    <h3>Summary for the Filtered Date Range</h3>
                    <p>Total Price: {{ $totalPrice }}</p>
                    <p>Total Buy Price: {{ $totalBuyPrice }}</p>
                    <p>Total Quantity: {{ $totalQuantity }}</p>

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
@stop
