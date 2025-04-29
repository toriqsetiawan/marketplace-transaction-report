@extends('layouts.app')

@section('htmlheader_title')
    Ads Report
@stop

@section('contentheader_title')
    Ads Report
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
                            window.location.href = `{{ route('ads-report.index') }}?user=${this.user}`
                        }
                    }" style="display: flex;align-items: center;gap: 16px;">
                        <select name="user" id="user" class="form-control" x-model="user" @change="search()">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('ads-report.create') }}" class="btn btn-primary" style="margin: 1rem 0">
                            <i class="fa fa-plus-circle"></i> Create
                        </a>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Total</th>
                            <th>Note</th>
                        </tr>
                        @php
                            $totalPerRow = 0;
                            $debit = 0;
                            $credit = 0;
                        @endphp
                        @forelse($data as $key)
                            @php

                            @endphp
                            <tr>
                                <td>{{ !request()->has('page') || request('page') == 1 ? ++$i : (request('page') - 1) * $data->perPage() + ++$i }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($key['date'])->format('d F Y') }}</td>
                                <td>{{ $key->description }}</td>
                                <td class="text-right">{{ number_format($key['total']) }}</td>
                                <td>{{ $key['note'] }}</td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Tidak ada data yang ditampilkan
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
