@extends('layouts.app')

@section('htmlheader_title')
    @lang('message.serviceunavailable')
@endsection

@section('contentheader_title')
    @lang('message.503error')
@endsection

@section('$contentheader_description')
@endsection

@section('main-content')

    <div class="error-page">
        <h2 class="headline text-red">503</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-red"></i> Oops! @lang('message.somethingwrong')</h3>
            <p>
                }}
                @lang('message.mainwhile') <a href='{{ url('/home') }}'>@lang('message.returndashboard')</a> @lang('message.usingsearch')
            </p>
            <form class='search-form'>
                <div class='input-group'>
                    <input type="text" name="search" class='form-control' placeholder="@lang('message.search')"/>
                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-danger btn-flat"><i class="fa fa-search"></i></button>
                    </div>
                </div><!-- /.input-group -->
            </form>
        </div>
    </div><!-- /.error-page -->
@endsection
