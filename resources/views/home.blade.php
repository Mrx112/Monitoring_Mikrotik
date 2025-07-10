@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/home">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ $totalUser }}</h3>
  
                  <p>Total User</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-stalker"></i>
                </div>
                <a href="{{ route('user.list') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ $totalAktif }}</h3>
  
                  <p>User Online</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
                <a href="{{ route('connections.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ $resource[0]['cpu-load'] }}<sup style="font-size: 20px">%</sup></h3>
  
                  <p>CPU Load</p>
                </div>
                <div class="icon">
                  <i class="ion ion-flash"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>65</h3>
  
                  <p>Unique Visitors</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- Line chart -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="far fa-chart-bar"></i>
                            Trafic
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label for="interface-select"><b>Pilih Interface:</b></label>
                            <select id="interface-select" class="form-control form-control-sm" style="width:auto; display:inline-block;">
                                <option value="ether1">ether1</option>
                                <option value="ether2">ether2</option>
                                <option value="ether3">ether3</option>
                                <option value="ether4">ether4</option>
                                <option value="ether5">ether5</option>
                            </select>
                            <button id="pause-traffic" class="btn btn-warning btn-sm ml-2">Pause</button>
                            <button id="resume-traffic" class="btn btn-success btn-sm ml-1" style="display:none;">Resume</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="single-traffic-chart" style="height: 250px;"></div>
                                <div id="traffic-static" class="traffic-static-anim mt-3">
                                    <span>Rx: <span id="traffic-rx">-</span></span> |
                                    <span>Tx: <span id="traffic-tx">-</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="chart-legend">
                            <span><span class="legend-dot legend-download"></span> Download (Rx)</span>
                            <span><span class="legend-dot legend-upload"></span> Upload (Tx)</span>
                        </div>
                    </div>
                    <!-- /.card-body-->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- Mikrotik Log -->
                <div class="card card-secondary card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-clipboard-list"></i>
                      Mikrotik Log
                    </h3>
                  </div>
                  <div class="card-body" style="max-height:300px; overflow:auto;">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th>Time</th>
                          <th>Topics</th>
                          <th>Message</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($log as $entry)
                        <tr>
                          <td>{{ $entry['time'] ?? '-' }}</td>
                          <td>{{ $entry['topics'] ?? '-' }}</td>
                          <td>{{ $entry['message'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('js-page')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.2/jquery.flot.min.js"></script>
<script>
var interfaces = ['ether1','ether2','ether3','ether4','ether5'];
var trafficData = {};
var maxPoints = 60;
var chartInterval = null;
var paused = false;
var currentIface = 'ether1';

// Init data for each interface
interfaces.forEach(function(iface) {
    trafficData[iface] = { rx: [], tx: [] };
});

function formatUnit(val) {
    if (val >= 1000000) return (val/1000000).toFixed(2) + ' Mbps';
    if (val >= 1000) return (val/1000).toFixed(2) + ' Kbps';
    return val + ' bps';
}

function updateTrafficChart(iface, rx, tx) {
    var now = (new Date()).getTime() / 1000;
    if (trafficData[iface].rx.length >= maxPoints) trafficData[iface].rx.shift();
    if (trafficData[iface].tx.length >= maxPoints) trafficData[iface].tx.shift();
    trafficData[iface].rx.push([now, rx]);
    trafficData[iface].tx.push([now, tx]);
    var yMax = Math.max(
        ...trafficData[iface].rx.map(p=>p[1]),
        ...trafficData[iface].tx.map(p=>p[1])
    ) * 1.2;
    $.plot('#single-traffic-chart', [
        { data: trafficData[iface].rx, color: '#28a745', label: 'Download (Rx)', lines: { fill: true, fillColor: { colors: [ { opacity: 0.2 }, { opacity: 0.05 } ] } } },
        { data: trafficData[iface].tx, color: '#007bff', label: 'Upload (Tx)', lines: { fill: true, fillColor: { colors: [ { opacity: 0.2 }, { opacity: 0.05 } ] } } }
    ], {
        grid: {
            hoverable: true,
            borderColor: '#b3d8fd',
            borderWidth: 1,
            tickColor: '#e3f0ff'
        },
        series: {
            shadowSize: 0,
            lines: { show: true, fill: true },
            points: { show: false }
        },
        yaxis: { show: true, max: yMax > 0 ? yMax : null, color: '#b3d8fd' },
        xaxis: { show: false }
    });
    $('#traffic-rx').text(formatUnit(rx));
    $('#traffic-tx').text(formatUnit(tx));
    // Trigger animasi
    $('#traffic-static').removeClass('traffic-static-anim');
    setTimeout(function() {
        $('#traffic-static').addClass('traffic-static-anim');
    }, 10);
}

function fetchTraffic(iface) {
    if(paused) return;
    $.ajax({
        url: '/api/traffic?interface=' + iface,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateTrafficChart(iface, data.rx, data.tx);
        }
    });
}

$('#pause-traffic').on('click', function() {
    paused = true;
    clearInterval(chartInterval);
    $(this).hide();
    $('#resume-traffic').show();
});
$('#resume-traffic').on('click', function() {
    paused = false;
    chartInterval = setInterval(function(){ fetchTraffic(currentIface); }, 1000);
    $(this).hide();
    $('#pause-traffic').show();
});
$('#interface-select').on('change', function() {
    currentIface = $(this).val();
    // Reset chart data for new interface
    $('#traffic-rx').text('-');
    $('#traffic-tx').text('-');
    $.plot('#single-traffic-chart', []);
    fetchTraffic(currentIface);
    if(chartInterval) clearInterval(chartInterval);
    chartInterval = setInterval(function(){ fetchTraffic(currentIface); }, 1000);
});

$(function() {
    fetchTraffic(currentIface); // initial
    chartInterval = setInterval(function(){ fetchTraffic(currentIface); }, 1000);
});
</script>
<style>
.traffic-static-anim {
    animation: trafficFade 0.7s;
}
@keyframes trafficFade {
    0% { background: #e3f0ff; color: #007bff; }
    50% { background: #b3d8fd; color: #222; }
    100% { background: transparent; color: inherit; }
}
</style>
@endpush