@extends('layouts.app')

@section('htmlheader_title')
    Edit Transaction
@endsection

@section('contentheader_title')
    Edit Transaction
@endsection

@section('main-content')
    @livewire('transaction.edit', ['transactionId' => $transactionId], key('transaction-edit'))
@endsection
