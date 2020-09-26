@extends('app')

@section('htmlheader_title')
    Edit Profile
@endsection

@section('contentheader_title')
    Edit Profile
@endsection

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    Sukses merubah data.
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
        <form role="form" method="post" action="{{ url('profile/update') }}">
            <div class="col-md-7">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ auth()->user()->name }}</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <div class="form-group">
                            <label for="group">Group</label>
                            <select id="group" class="form-control" disabled>
                                @foreach($group as $key)
                                    <option value="{{ $key->id }}" {{ $key->id == $user->group_id ? 'selected':'' }}>
                                        {{ ucfirst($key->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Username</label>
                            <input type="text" id="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $user->profile->phone }}">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" rows="3">{{ $user->profile->address }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="bonus">Bonus</label>
                            <input type="text" id="bonus" class="form-control" value="{{ number_format($user->profile->bonus, 0, '.',',') }}" disabled>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Detail Bank</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="bank_type">Bank Type</label>
                            <select id="bank_type" name="bank_type" class="form-control" required>
                                <option value="bca" {{ $user->profile->bank_type == 'bca' ? 'selected':'' }}>
                                    BCA
                                </option>
                                <option value="bni" {{ $user->profile->bank_type == 'bni' ? 'selected':'' }}>
                                    BNI
                                </option>
                                <option value="bri" {{ $user->profile->bank_type == 'bri' ? 'selected':'' }}>
                                    BRI
                                </option>
                                <option value="mandiri" {{ $user->profile->bank_type == 'mandiri' ? 'selected':'' }}>
                                    Mandiri
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name" class="form-control" value="{{ $user->profile->bank_name ?? old('bank_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="bank_number">Bank Number</label>
                            <input type="text" id="bank_number" name="bank_number" class="form-control" value="{{ $user->profile->bank_number ?? old('bank_number') }}" required>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
            <div class="col-md-5">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Change Password</h3>
                        <div class="alert alert-info alert-dismissable" style="margin-top: 20px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Note!</h4>
                            Leave password blank if dont want to change.
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right">Change</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
