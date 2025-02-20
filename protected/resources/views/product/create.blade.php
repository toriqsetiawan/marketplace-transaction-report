@extends('layouts.app')

@section('htmlheader_title')
    Add Product
@endsection

@section('contentheader_title')
    Add Product
@endsection

@section('main-content')
    @livewire('product.create')
@endsection
