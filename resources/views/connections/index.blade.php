@extends('layouts.master')
@section('title', 'Mikrotik Connections')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Mikrotik Connections</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      {{-- FORM LOGIN --}}
      <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Login Mikrotik</h3></div>
        <form action="{{ route('connections.login') }}" method="POST" class="card-body row g-2">
          @csrf
          <div class="col-md-4">
            <label>IP / Host</label>
            <input type="text" name="host" class="form-control" required
                   value="{{ old('host', session('mk_host')) }}">
          </div>
          <div class="col-md-3">
            <label>Username</label>
            <input type="text" name="user" class="form-control" required
                   value="{{ old('user', session('mk_user')) }}">
          </div>
          <div class="col-md-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required
                   value="{{ old('password', session('mk_password')) }}">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Connect &amp; Fetch</button>
          </div>
        </form>
      </div>

      {{-- TABEL CONNECTION --}}
      @if($connections)
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">/ip/firewall/connection ({{ count($connections) }})</h3>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Src&nbsp;→&nbsp;Dst</th>
                  <th>Protocol</th>
                  <th>Port</th>
                  <th>Timeout</th>
                  <th style="width:80px">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($connections as $c)
                <tr>
                  <td>{{ $c['src-address'] ?? '' }} → {{ $c['dst-address'] ?? '' }}</td>
                  <td>{{ $c['protocol'] ?? '' }}</td>
                  <td>{{ $c['dst-port'] ?? '' }}</td>
                  <td>{{ $c['timeout'] ?? '' }}</td>
                  <td>
                    <form action="{{ route('connections.destroy', $c['.id']) }}"
                          method="POST" onsubmit="return confirm('Remove connection?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger btn-xs">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

    </div>
  </section>
</div>
@endsection
