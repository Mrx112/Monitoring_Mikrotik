@extends('layouts.master')

@section('title', 'Queue List')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Queue List</h1>
          </div>
        </div>
      </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mikrotik Queue List</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Target</th>
                                <th>Max Limit</th>
                                <th>Comment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($queues as $queue)
                            <tr>
                                <td>{{ $queue['name'] ?? '-' }}</td>
                                <td>{{ $queue['target'] ?? '-' }}</td>
                                <td>{{ $queue['max-limit'] ?? '-' }}</td>
                                <td>{{ $queue['comment'] ?? '-' }}</td>
                                @php
                                  $status = isset($trafic[$queue['target']][0]['status']) ? strtolower($trafic[$queue['target']][0]['status']) : null;
                                @endphp
                                <td>
                                  @if($status === 'up')
                                    <span class="badge badge-success"><i class="fas fa-circle" style="color:#28a745;"></i> CONNECT</span>
                                  @elseif($status === 'down')
                                    <span class="badge badge-danger"><i class="fas fa-circle" style="color:#dc3545;"></i> DOWN</span>
                                  @else
                                    <span class="badge badge-secondary">Unknown</span>
                                  @endif
                                </td>
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
