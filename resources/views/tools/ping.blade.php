@extends('layouts.master')
@section('title', 'Tools - Ping')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ping Tools</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-primary">
            <div class="card-body">
                <form method="POST" action="{{ url('/tools/ping') }}">
                    @csrf
                    <div class="form-group">
                        <label for="host">Host/IP</label>
                        <input type="text" class="form-control" id="host" name="host" value="{{ old('host', $host ?? '') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ping</button>
                </form>
                @if(isset($output))
                    <hr>
                    <h5>Hasil Ping ke: <b>{{ $host }}</b></h5>
                    <pre>{{ $output }}</pre>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
