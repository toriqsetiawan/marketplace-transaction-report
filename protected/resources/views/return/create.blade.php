@extends('layouts.app')

@section('htmlheader_title')
    Add Return Order
@endsection

@section('contentheader_title')
    Add Return Order
@endsection

@section('main-content')
    @livewire('return.create', [], key('return-create'))
@endsection
