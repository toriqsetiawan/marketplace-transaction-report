@extends('layouts.app')

@section('htmlheader_title')
    Manajemen Produk
@stop

@section('contentheader_title')
    Manajemen Produk
@stop

@section('main-content')
    <style>
        table td.attribute > span:first-child::after {
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
                        <i class="fa fa-plus-circle"></i> Barang
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
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">Action</th>
                                <th>Product</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $product)
                                <!-- Parent Row -->
                                <tr>
                                    <td rowspan="{{ $product->variants->count() + 1 }}" class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs btn-delete"
                                            data-href="{{ route('product.destroy', $product->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-xs btn-info">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <p>Supplier: <i>{{ $product->supplier ? $product->supplier->nama : '-' }}</i></p>
                                        <p class="text-bold" style="margin: 0">{{ $product->nama }}</p>
                                    </td>
                                    <td style="vertical-align: middle">{{ !$product->variants->count() ? $product->harga_beli : '-' }}</td>
                                    <td style="vertical-align: middle">{{ !$product->variants->count() ? $product->harga_jual : '-' }}</td>
                                    <td style="vertical-align: middle"><span class="text-bold">{{ $product->variants->sum('stock') }}</span></td>
                                </tr>
                                @forelse ($product->variants as $variant)
                                    <!-- Sub Rows -->
                                    <tr>
                                        <td class="attribute">
                                            @foreach ($variant->attributeValues as $attribute)
                                                <span class="text-medium">{{ $attribute->value }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ number_format($variant->product->harga_beli) }}</td>
                                        <td>{{ number_format($variant->price) }}</td>
                                        <td>{{ $variant->stock }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            -
                                        </td>
                                    </tr>
                                @endforelse
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
