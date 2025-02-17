@extends('layouts.app')

@section('htmlheader_title')
    Product Management
@stop

@section('contentheader_title')
    Product Management
@stop

@section('main-content')
    <style>
        table td.attribute>span:first-child::after {
            content: ' - ';
        }
    </style>
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
                    <a href="{{ route('product.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Product
                    </a>
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Action</th>
                                <th>Product</th>
                                <th style="text-align: right">Harga Beli</th>
                                <th style="text-align: right">Harga Jual</th>
                                <th style="text-align: right">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $product)
                                <!-- Parent Row -->
                                <tr>
                                    <td rowspan="{{ $product->variants->take(3)->count() + 1 }}" class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs btn-delete"
                                            data-href="{{ route('product.destroy', $product->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-xs btn-info">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <p class="text-bold text-uppercase" style="margin: 0">{{ $product->nama }}</p>
                                        <p>Supplier: <i>{{ $product->supplier ? $product->supplier->name : '-' }}</i></p>
                                    </td>
                                    <td style="vertical-align: middle; text-align:right">
                                        {{ !$product->variants->count() ? $product->harga_beli : '-' }}</td>
                                    <td style="vertical-align: middle; text-align:right">
                                        {{ !$product->variants->count() ? $product->harga_jual : '-' }}</td>
                                    <td style="vertical-align: middle; text-align:right"><span
                                            class="text-bold">{{ number_format($product->variants->sum('stock')) }}</span>
                                    </td>
                                </tr>

                                @forelse ($product->variants->take(3) as $variant)
                                    <!-- Sub Rows -->
                                    <tr class="{{ $variant->stock <= 0 ? 'bg-danger' : '' }}">
                                        <td class="attribute">
                                            {{ $variant->attributeValues->pluck('value')->implode(' / ') }}
                                        </td>
                                        <td style="text-align:right">Rp. {{ number_format($variant->product->harga_beli) }}
                                        </td>
                                        <td style="text-align:right">Rp. {{ number_format($variant->price) }}</td>
                                        <td style="text-align:right">{{ number_format($variant->stock) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            -
                                        </td>
                                    </tr>
                                @endforelse
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-link"><i>load more...</i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5">Tidak ada data yang di tampilkan</td>
                                </tr>
                            @endforelse
                        </tbody>
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
