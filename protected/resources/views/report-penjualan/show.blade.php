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
                            <th>Harga beli</th>
                            <th>Biaya Admin</th>
                            <th>Total</th>
                            <th>Margin</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                        @forelse($data as $key)
                            @php
                                $color = '';

                                if (strtolower($key->status) == 'pending') {
                                    $color = 'bg-info';
                                } elseif (strtolower($key->status) == 'retur') {
                                    $color = 'bg-red';
                                }
                            @endphp
                            <tr class="{{ $color }}">
                                <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                </td>
                                <td>{{ $key->channel == 'user' ? $key->mitra->nama : $key->name }}</td>
                                <td>{{ $key->channel }}</td>
                                <td>{{ $key->channel == 'online' ? $key->configFee->marketplace : '-' }}</td>
                                <td>{{ $key->product->nama }}</td>
                                <td>{{ $key->ukuran }}</td>
                                <td>{{ $key->motif }}</td>
                                <td>{{ $key->jumlah }}</td>
                                <td>
                                    <p class="text-right">{{ number_format($key->harga_beli) }}</p>
                                    @if ($key->biaya_tambahan)
                                        <p class="text-right">Pack {{ number_format($key->biaya_tambahan) }}</p>
                                    @endif
                                    @if ($key->biaya_lain_lain)
                                        <p class="text-right">Insole {{ number_format($key->biaya_lain_lain) }}</p>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($key->pajak) }}</td>
                                <td class="text-right">{{ $key->channel == 'online' ? number_format($key->total_paid) : number_format($key->total_paid * $key->jumlah) }}</td>
                                <td class="text-right">{{ $key->channel == 'online' ? number_format($key->total_paid - (($key->harga_beli + $key->biaya_lain_lain + $key->biaya_tambahan) * $key->jumlah) - $key->pajak) : number_format(($key->total_paid * $key->jumlah) - (($key->harga_beli + $key->biaya_lain_lain + $key->biaya_tambahan) * $key->jumlah) - $key->pajak) }}</td>
                                <td>{{ $key->status }}</td>
                                <td>{{ $key->created_at->format('d-m-Y') }}</td>
                                {{-- <td>
                                        <a href="{{ route('penjualan.edit', $key->id) }}" class="btn btn-xs btn-info"
                                            title="Update">
                                            <i class="fa fa-edit"></i> Update
                                        </a>
                                    </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Tidak ada data yang ditampilkan
                                </td>
                            </tr>
                        @endforelse
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
