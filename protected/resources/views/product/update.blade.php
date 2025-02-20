@extends('layouts.app')

@section('htmlheader_title')
    Edit Product
@endsection

@section('contentheader_title')
    Edit Product
@endsection

@section('main-content')
    @livewire('product.edit', ['product' => $data])
@endsection
