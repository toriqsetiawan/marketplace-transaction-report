@extends('layouts.app')

@section('htmlheader_title')
    Add Purchase Order
@endsection

@section('contentheader_title')
    Add Purchase Order
@endsection

@section('main-content')
    @livewire('purchase.create', [], key('purchase-create'))
@endsection
