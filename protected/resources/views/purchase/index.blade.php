@extends('layouts.app')

@section('htmlheader_title')
    Purchase Order
@stop

@section('contentheader_title')
    Purchase Order
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
                        <a href="{{ route('purchase.create') }}" class="btn btn-primary" style="margin: 1rem 0">
                            <i class="fa fa-plus-circle"></i> Create
                        </a>
                    </div>

                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>User</th>
                                <th>Product / Variant / Quantity / Sub Total</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                    </td>
                                    <td>{{ $key->purchase_code }}</td>
                                    <td>{{ $key->user->name }}</td>
                                    <td>
                                        <table style="width: 100%;">
                                            @foreach ($key->items->take(3) as $item)
                                                <tr>
                                                    <td style="width: 50%">{{ $item->variant?->product?->nama }}</td>
                                                    <td style="width: 32%">
                                                        {{ $item->variant?->product?->variants()->where('id', $item->variant_id)->first()->attributeValues()->implode('value', '/') }}
                                                    </td>
                                                    <td style="width: 5%">{{ $item->quantity }}</td>
                                                    <td class="text-right" style="width: 13%">
                                                        {{ number_format($item->price) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if ($key->items->count() > 3)
                                                <tr>
                                                    <td colspan="4" class="text-center"><i>load more...</i></td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                    <td>{{ number_format($key->total_price) }}</td>
                                    <td>
                                        @if ($key->status == 'pending')
                                            <span class="label label-warning text-uppercase">{{ $key->status }}</span>
                                        @elseif ($key->status == 'complete')
                                            <span class="label label-success text-uppercase">{{ $key->status }}</span>
                                        @elseif ($key->status == 'cancel')
                                            <span class="label label-default text-uppercase">{{ $key->status }}</span>
                                        @else
                                            <span class="label label-danger text-uppercase">{{ $key->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $key->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('purchase.edit', $key->id) }}" class="btn btn-xs btn-info"
                                            title="Update">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs btn-delete"
                                            data-href="{{ route('purchase.destroy', $key->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
