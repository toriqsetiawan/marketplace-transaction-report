@extends('layouts.app')

@section('htmlheader_title')
    Manajemen User
@stop

@section('contentheader_title')
    Manajemen User
@stop

@section('main-content')
    <div class="row">
        <div class="col-xs-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('success') }}.
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Message!</h4>
                    {{ session('error') }}.
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <a href="{{ route('user.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> User
                    </a>
                    <div class="box-tools">
                        <form action="?" method="get">
                            <div class="input-group" style="width: 200px;">
                                <input type="text" name="search" class="form-control input-sm pull-right"
                                    placeholder="Search" value="{{ request('search') }}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-default"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <table class="table table-hover">
                            <tr>
                                <th class="text-center">Action</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                            @forelse($data as $key)
                                <tr>
                                    <td class="text-center">
                                        @if ($key->role_id != 1)
                                            <button type="button" class="btn btn-danger btn-xs btn-delete"
                                                data-href="{{ route('user.destroy', $key->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('user.edit', $key->id) }}" class="btn btn-xs btn-info">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>{{ $key->name }}</td>
                                    <td>{{ $key->role->name }}</td>
                                    <td>{{ $key->email }}</td>
                                    <td>{{ $key->created_at }}</td>
                                    <td>{{ $key->updated_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Tidak ada data yang ditampilkan
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer text-right">
                    <div class="pull-left" style="margin-top: 20px">
                        <strong>Total data : {!! $data->total() !!}</strong>
                    </div>
                    <div class="pull-right">
                        {!! $data->appends(request()->all())->links() !!}
                    </div>
                </div>
            </div><!-- /.box -->
        </div>
    </div>
    <div class="example-modal">
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="deleteForm">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Delete Data</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure want to delete this data?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-infp pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@stop
