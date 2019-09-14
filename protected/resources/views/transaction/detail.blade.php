@extends('layouts.app')

@section('htmlheader_title')
    Detail Rekapan
@endsection

@section('contentheader_title')
    Detail Rekapan - {{ $employee->nama }}
@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    Data anda telah tersimpan.
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#panel-laporan" data-toggle="tab">LAPORAN</a>
                    </li>
                    <li>
                        <a href="#panel-rekapan" data-toggle="tab">REKAPAN</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="panel-laporan">
                        <div class="box-header text-center">
                            <h3 class="box-title">Daftar Setoran</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Setor</th>
                                    <th>Setoran ke</th>
                                    <th>Jumlah</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                @php
                                    $total_setor = $total_kodi = 0;
                                @endphp
                                @forelse($setor as $key)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ dateIndonesia(strtotime($key->date_at)) }}</td>
                                        <td>{{ $key->count }}</td>
                                        <td>{{ $key->kodi + 0 }} Kodi</td>
                                        <td class="text-right">{{ number_format($key->total, 0, ',', '.') }}</td>
                                        @php
                                            $total_setor += $key->total;
                                            $total_kodi += $key->kodi;
                                        @endphp
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Tidak ada data yang ditampilkan
                                        </td>
                                    </tr>
                                @endforelse
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>{{ $total_kodi }} Kodi</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ number_format($total_setor, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <!-- /.box-body -->
                        <div class="box-header text-center">
                            <h3 class="box-title">Daftar Bon</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Bon</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                @php
                                    $i = 0;
                                    $total_bon = 0;
                                @endphp
                                @forelse($bon as $key)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ dateIndonesia(strtotime($key->date_at)) }}</td>
                                        <td>{{ ucwords($key->varian->nama) }}</td>
                                        <td>{{ $key->varian->kode == "OT" ? '-' : number_format($key->price_history, 0, ',', '.') }}</td>
                                        <td>{{ $key->varian->kode == "OT" ? '-' : ($key->quantity + 0).' '.$key->varian->taxonomi->nama }}</td>
                                        <td class="text-right">{{ number_format($key->sub_total, 0, ',', '.') }}</td>
                                        @php
                                            $total_bon += $key->sub_total;
                                        @endphp
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Tidak ada data yang ditampilkan
                                        </td>
                                    </tr>
                                @endforelse
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ number_format($total_bon, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="panel-rekapan">
                        <div class="row">
                            <form role="form" method="post" action="{{ route('transaction.rekap') }}">
                                {!! csrf_field() !!}
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="box-header with-border">
                                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4><i class="icon fa fa-info"></i> Catatan!</h4>
                                            Total tidak menggunakan titik atau koma.<br>
                                            contoh : 10000
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama">Nama Karyawan</label>
                                                    <input type="text" id="nama" name="nama" class="form-control" value="{{ $employee->nama }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    @if($employee_log)
                                                        @if($employee_log->type == "bon")
                                                        <label for="last_trx">SISA BON SEBELUM</label>
                                                        @elseif($employee_log->type == "setor")
                                                        <label for="last_trx">SISA UANG SEBELUM</label>
                                                        @endif
                                                        <input type="text" id="last_trx" name="last_trx" class="form-control" value="{{ number_format($employee_log->correction, 0, ',', '.') }}" disabled>
                                                    @else
                                                    <label for="last_trx">Transaksi Awal</label>
                                                    <input type="text" id="last_trx" name="last_trx" class="form-control" value="0" disabled>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_setor">Total Setor</label>
                                                    <input type="text" id="total_setor" name="total_setor" class="form-control" value="{{ number_format($total_setor, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_bon">Total Bon Sekarang</label>
                                                    <input type="text" id="total_bon" name="total_bon" class="form-control" value="{{ number_format($total_bon, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    @php
                                                        $total_global = $total_setor - $total_bon;
                                                        if ($employee_log) :
                                                            if ($employee_log->type == "bon") :
                                                                $total_global -= $employee_log->correction;
                                                            elseif ($employee_log->type == "setor") :
                                                                $total_global += $employee_log->correction;
                                                            endif;
                                                        endif;
                                                    @endphp
                                                    <label for="global">{{ $total_global < 0 ? 'SISA BON' : 'SISA UANG' }}</label>
                                                    <input type="text" id="global" name="global" class="form-control" value="{{ number_format($total_global, 0, ',', '.') }}" disabled>
                                                    <input type="hidden" name="total_global" class="form-control" value="{{ $total_global }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tunai">Tunai</label>
                                                    <input type="text" id="tunai" name="tunai" class="form-control money" value="" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tunai">Giro</label>
                                                    <input type="text" id="giro" name="giro" class="form-control money" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary pull-right">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
