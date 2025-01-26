@extends('layouts.app')

@section('htmlheader_title')
    Ubah User
@endsection

@section('contentheader_title')
    Ubah User
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
        <form role="form" method="post" action="{{ route('user.update', $user->id) }}">
            {!! csrf_field() !!}
            {!! method_field('put') !!}
            <div class="col-md-6">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control">
                                <option hidden>Pilih role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected':''}}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $user->name ?? old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" email="email" class="form-control" value="{{ $user->email ?? old('email') }}">
                        </div>
                        <div class="box-header with-border">
                            <div class="alert alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-info"></i> Ubah Password!</h4>
                                Kosongkan password jika tidak ingin mengubah password
                            </div>
                        </div><!-- /.box-header -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
