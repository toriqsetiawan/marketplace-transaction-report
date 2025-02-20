@extends('layouts.app')

@section('htmlheader_title')
    Edit Return Order
@endsection

@section('contentheader_title')
    Edit Return Order
@endsection

@section('main-content')
    @livewire('return.edit', ['transactionId' => $transactionId], key('return-edit'))
@endsection
