@extends('layouts.app')

@section('htmlheader_title')
    Edit Purchase Order
@endsection

@section('contentheader_title')
    Edit Purchase Order
@endsection

@section('main-content')
    @livewire('purchase.edit', ['purchaseId' => $purchaseId], key('purchase-edit'))
@endsection
