@extends('layouts.app')

@section('htmlheader_title')
    Stock Management
@stop

@section('contentheader_title')
    Stock Management
@stop

@section('main-content')
    <style>
        table td.attribute>span:first-child::after {
            content: ' - ';
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>td {
            border: 1px solid #000000;
        }
    </style>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header" style="margin-bottom: 2rem">
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
                <div class="box-body table-responsive">
                    @php
                        $supplierSummary = [];
                    @endphp
                    <div class="row">
                        @foreach ($data as $product)
                            @php
                                // Collect all attribute values for this product
                                $attributeValues = collect();
                                foreach ($product->variants as $variant) {
                                    foreach ($variant->attributeValues as $attributeValue) {
                                        // Group attribute values by their attribute name
                                        $attributeValues->put(
                                            $attributeValue->attribute->name,
                                            $attributeValues
                                                ->get($attributeValue->attribute->name, collect())
                                                ->push($attributeValue->value),
                                        );
                                    }
                                }

                                // Remove duplicates and sort
                                $attributeValues = $attributeValues->map(fn($values) => $values->unique()->sort());

                                $pricePerPcs = $product->harga_beli ?? 0;

                                // Calculate the total price across all stock
                                $totalStock = $product->variants->sum('stock');
                                $totalPrice = $totalStock * $pricePerPcs;

                                if (strtoupper($product->nama) == 'PACKING' || strtoupper($product->nama == 'INSOLE')) {
                                } else {
                                    // Add to supplier summary
                                    $supplierName = $product->supplier->name ?? 'Unknown Supplier';
                                    if (!isset($supplierSummary[$supplierName])) {
                                        $supplierSummary[$supplierName] = [
                                            'totalProduct' => 0,
                                            'totalHarga' => 0,
                                        ];
                                    }
                                    $supplierSummary[$supplierName]['totalProduct'] += $totalStock;
                                    $supplierSummary[$supplierName]['totalHarga'] += $totalPrice;
                                }
                            @endphp

                            <div class="col-xs-12 col-md-6" style="{{ $loop->iteration % 2 == 1 ? 'clear: both' : '' }}">
                                <!-- Product Header -->
                                <h4 class="text-center text-bold"
                                    style="background-color: #99cc99; padding: 1rem; margin-bottom: 0">
                                    {{ strtoupper($product->nama) }}</h4>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="background-color: lightgrey; width: 12rem">VARIANT</th>
                                            @foreach ($attributeValues->first() ?? [] as $columnHeader)
                                                <th class="text-center" style="background-color: lightgrey">
                                                    {{ strtoupper($columnHeader) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAllColumnsStock = 0;
                                        @endphp
                                        <!-- Dynamic Row Headers -->
                                        @foreach ($attributeValues->skip(1)->first() ?? [] as $rowHeader)
                                            <tr>
                                                <td class="text-bold" style="background-color: lightgrey">
                                                    {{ $rowHeader }}</td>
                                                @foreach ($attributeValues->first() ?? [] as $columnHeader)
                                                    @php
                                                        // Calculate stock for the current row and column combination
                                                        $stock = $product->variants
                                                            ->filter(function ($variant) use (
                                                                $rowHeader,
                                                                $columnHeader,
                                                                $attributeValues,
                                                            ) {
                                                                $attributes = $variant->attributeValues->pluck(
                                                                    'value',
                                                                    'attribute.name',
                                                                );
                                                                $rowAttribute = $attributeValues
                                                                    ->keys()
                                                                    ->skip(1)
                                                                    ->first(); // Row attribute name
                                                                $columnAttribute = $attributeValues->keys()->first(); // Column attribute name
                                                                return $attributes[$rowAttribute] === $rowHeader &&
                                                                    $attributes[$columnAttribute] === $columnHeader;
                                                            })
                                                            ->sum('stock');

                                                        if (
                                                            $stock == 0 &&
                                                            $product->supplier_id == 2 &&
                                                            auth()->user()->hasRole('reseller')
                                                        ) {
                                                            $stock = 1;
                                                        }
                                                    @endphp
                                                    <td class="text-center"
                                                        style="background-color: {{ $stock == 0 ? '#ff6666' : 'transparent' }}">
                                                        {{ number_format($stock) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach

                                        @if (auth()->user()->hasRole('administrator') || auth()->user()->hasRole('admin'))
                                            <!-- Total Row -->
                                            <tr>
                                                <td class="text-bold" style="background-color: #99cc99;">SUB TOTAL</td>
                                                @foreach ($attributeValues->first() ?? [] as $columnHeader)
                                                    @php
                                                        $totalColumnStock = $product->variants
                                                            ->filter(function ($variant) use (
                                                                $columnHeader,
                                                                $attributeValues,
                                                            ) {
                                                                $attributes = $variant->attributeValues->pluck(
                                                                    'value',
                                                                    'attribute.name',
                                                                );
                                                                $columnAttribute = $attributeValues->keys()->first(); // Column attribute name
                                                                return $attributes[$columnAttribute] === $columnHeader;
                                                            })
                                                            ->sum('stock');

                                                        $totalAllColumnsStock += $totalColumnStock;
                                                    @endphp
                                                    <td class="text-center text-bold"
                                                        style="background-color: {{ $totalColumnStock == 0 ? '#ff6666' : '#99cc99' }};">
                                                        {{ number_format($totalColumnStock) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td class="text-bold" style="background-color: #99cc99;">TOTAL</td>
                                                <td colspan="{{ count($attributeValues->first() ?? []) }}" class="text-bold"
                                                    style="background-color: #99cc99;">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        {{ number_format($totalAllColumnsStock, 0, ',', '.') }} pasang

                                                        @php
                                                            $kodi = floor($totalAllColumnsStock / 20);
                                                            $pasang = $totalAllColumnsStock % 20;
                                                        @endphp

                                                        @if ($kodi > 0)
                                                            <span>{{ $kodi }} kd {{ $pasang > 0 ? " $pasang ps" : '' }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif

                                        @if (auth()->user()->hasRole('administrator'))
                                            <!-- Harga per Pcs Row -->
                                            <tr>
                                                <td class="text-bold" style="background-color: #99cc99;">HARGA</td>
                                                <td colspan="{{ count($attributeValues->first() ?? []) }}"
                                                    class="text-bold" style="background-color: #99cc99;">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        <span>{{ number_format($pricePerPcs, 0, ',', '.') }} / ps</span>
                                                        <span>{{ number_format($pricePerPcs * 20, 0, ',', '.') }} / kd</span>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Total Harga Row -->
                                            <tr>
                                                <td class="text-bold" style="background-color: #99cc99;">TOTAL Rp</td>
                                                <td colspan="{{ count($attributeValues->first() ?? []) }}"
                                                    class="text-bold" style="background-color: #99cc99;">
                                                    {{ number_format($totalPrice, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                    @if (auth()->user()->hasRole('administrator'))
                        <hr>
                        <!-- Supplier Summary Table -->
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="text-center text-bold"
                                    style="background-color: #99cc99; padding: 1rem; margin-bottom: 0;">
                                    Supplier Summary</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="background-color: lightgrey">SUPPLIER</th>
                                            <th style="background-color: lightgrey" class="text-center">TOTAL PRODUCT</th>
                                            <th style="background-color: lightgrey" class="text-center">TOTAL HARGA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAllProducts = 0;
                                            $totalAllHarga = 0;
                                        @endphp
                                        @foreach ($supplierSummary as $supplierName => $summary)
                                            <tr>
                                                <td>{{ $supplierName }}</td>
                                                <td class="text-center">{{ number_format($summary['totalProduct']) }}</td>
                                                <td class="text-center">
                                                    {{ number_format($summary['totalHarga'], 0, ',', '.') }}</td>
                                            </tr>
                                            @php
                                                $totalAllProducts += $summary['totalProduct'];
                                                $totalAllHarga += $summary['totalHarga'];
                                            @endphp
                                        @endforeach
                                        @if (count($supplierSummary) > 0)
                                            <tr>
                                                <td class="text-bold" style="background-color: #99cc99;">TOTAL</td>
                                                <td class="text-bold text-center" style="background-color: #99cc99;">
                                                    {{ number_format($totalAllProducts) }}
                                                </td>
                                                <td class="text-bold text-center" style="background-color: #99cc99;">
                                                    {{ number_format($totalAllHarga, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
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
