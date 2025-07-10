@extends('layouts.master')

@section('title', 'User List')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">User List</h1>
          </div>
        </div>
      </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <form method="GET" action="" class="form-inline">
                            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari user..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-info btn-sm">Cari</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm ml-2">Reset</a>
                            <a href="{{ route('user.export') }}" class="btn btn-success btn-sm ml-2">Export CSV</a>
                        </form>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
