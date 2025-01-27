@extends('layouts.app')

@section('htmlheader_title')
    Tambah Penjualan
@endsection

@section('contentheader_title')
    Tambah Penjualan
@endsection

@section('main-content')
    @livewire('transaction.create', [], key('transaction-create'))
@endsection
