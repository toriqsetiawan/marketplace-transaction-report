@extends('layouts.app')

@section('htmlheader_title')
    Penjualan Pending
@stop

@section('contentheader_title')
    Penjualan Pending
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
                        <input type="text" name="daterange" class="form-control" value="" style="width: 17rem; margin: 1rem 0"/>
                    </div>
                    <div class="box-tools">
                        <form action="?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" method="get" style="align-items: center; display: inline-flex; gap: 10px;width:35rem">
                            @if ($data->count() > 0)
                                <select name="mitra" id="mitra" class="form-control">
                                    <option value="">Semua Mitra</option>
                                    @foreach ($listMitra as $mitra)
                                        <option value="{{ $mitra->id_mitra }}" {{ request('mitra') == $mitra->id_mitra ? 'selected' : '' }}>{{ $mitra->nama }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <div class="input-group" style="width: 35rem;">
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
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Channel</th>
                                <th>Marketplace</th>
                                <th>Produk</th>
                                <th>Ukuran</th>
                                <th>Warna/Motif</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                    </td>
                                    <td>{{ $key->channel == 'mitra' ? $key->mitra->nama : $key->name }}</td>
                                    <td>{{ $key->channel }}</td>
                                    <td>{{ $key->channel == 'online' ? $key->configFee->marketplace : '-' }}</td>
                                    <td>{{ $key->product->nama }}</td>
                                    <td>{{ $key->ukuran }}</td>
                                    <td>{{ $key->motif }}</td>
                                    <td>{{ $key->jumlah }}</td>
                                    <td>{{ $key->status }}</td>
                                    <td>{{ $key->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('penjualan-pending.edit', $key->id) }}" class="btn btn-xs btn-info"
                                            title="Update">
                                            <i class="fa fa-edit"></i> Update
                                        </a>
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
@stop
