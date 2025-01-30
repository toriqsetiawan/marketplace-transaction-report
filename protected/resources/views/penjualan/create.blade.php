@extends('layouts.app')

@section('htmlheader_title')
    Add Transaction
@endsection

@section('contentheader_title')
    Add Transaction
@endsection

@section('main-content')
    @livewire('transaction.create', [], key('transaction-create'))
@endsection
