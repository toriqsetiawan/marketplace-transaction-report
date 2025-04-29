@extends('layouts.app')

@section('htmlheader_title')
    PDF parser
@endsection

@section('contentheader_title')
    PDF parser
@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    Data anda telah tersimpan.
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form role="form" method="post" action="{{ route('pdf-parse.store') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                        <div class="form-group">
                            <label for="pdf">Resi</label>
                            <input type="file" id="pdf" name="pdf" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
