@extends('layouts.master')
@section('title', 'Monitoring Access Link')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Monitoring Access Link</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card card-info">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Link</th>
                            <th>Status</th>
                            <th>Latency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($links as $link)
                        <tr>
                            <td>{{ $link['name'] }}</td>
                            <td>
                                @if($link['status'] === 'up')
                                    <span class="badge badge-success">Up</span>
                                @else
                                    <span class="badge badge-danger">Down</span>
                                @endif
                            </td>
                            <td>{{ $link['latency'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
