@extends('layouts.app')

@section('htmlheader_title')
    Ubah Produk
@endsection

@section('contentheader_title')
    Ubah Produk
@endsection

@section('main-content')
    @livewire('product.edit', ['product' => $data])
@endsection
