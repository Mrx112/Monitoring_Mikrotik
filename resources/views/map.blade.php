@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- Info Boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
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
                <div class="col-lg-3 col-6">
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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $resource[0]['cpu-load'] ?? 'N/A' }}<sup style="font-size: 20px">%</sup></h3>
                            <p>CPU Load</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-flash"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
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
            </div>

            <!-- Log dan Notifikasi -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Mikrotik Log</h3>
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

                <div class="col-md-6">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Notifikasi Perangkat Mati</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($deadDevices as $device)
                                    <li class="list-group-item list-group-item-danger">
                                        {{ $device['name'] }} ({{ $device['ip'] }}) - <strong>OFFLINE</strong>
                                    </li>
                                @endforeach
                                @if(count($deadDevices) === 0)
                                    <li class="list-group-item">Semua perangkat online.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peta Mikrotik -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Peta Lokasi Perangkat Mikrotik</h3>
                        </div>
                        <div class="card-body">
                            <!-- Dropdown dan Search -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select id="deviceSelect" class="form-control">
                                        <option value="">-- Pilih Nama Mikrotik --</option>
                                        @foreach($devices as $device)
                                            <option value="{{ $device['name'] }}">{{ $device['name'] }} ({{ $device['ip'] }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex">
                                    <input type="text" id="deviceSearch" class="form-control" placeholder="Cari nama atau IP Mikrotik...">
                                </div>
                            </div>
                            <div id="map" style="height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('js-page')
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<!-- jQuery & jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
const devices = @json($devices);
const markers = {};
const map = L.map('map').setView([-7.005145, 110.438125], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Icon status
const iconOnline = new L.Icon({
    iconUrl: '/assets/stisla/img/notif/online.png',
    iconRetinaUrl: '/assets/stisla/img/notif/online.png',
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -30],
    shadowUrl: null,
    shadowSize: null,
    shadowAnchor: null,
    className: 'leaflet-online-icon'
});

const iconOffline = new L.Icon({
    iconUrl: '/assets/stisla/img/notif/offline.png',
    iconRetinaUrl: '/assets/stisla/img/notif/offline.png',
    iconSize: [35, 35],
    iconAnchor: [17, 35],
    popupAnchor: [0, -30],
    shadowUrl: null,
    shadowSize: null,
    shadowAnchor: null,
    className: 'leaflet-offline-icon'
});

const searchList = [];

// Buat semua marker
devices.forEach(device => {
    const icon = device.status === 'online' ? iconOnline : iconOffline;
    const marker = L.marker([device.latitude, device.longitude], { icon }).addTo(map);

    const statusHtml = device.status === 'online'
        ? '<span class="text-success">Online</span>'
        : '<span class="text-danger">Offline</span>';

    marker.bindPopup(`
        <b>${device.name}</b><br>
        IP: ${device.ip}<br>
        Status: ${statusHtml}<br>
        <a href="https://www.google.com/maps/dir/?api=1&destination=${device.latitude},${device.longitude}" target="_blank" class="btn btn-sm btn-primary mt-2">Rute</a>
    `);

    markers[device.name.toLowerCase()] = marker;
    markers[device.ip] = marker;

    searchList.push(device.name, device.ip);
});

// Event select dropdown
document.getElementById('deviceSelect').addEventListener('change', function () {
    const key = this.value.toLowerCase();
    if (markers[key]) {
        map.setView(markers[key].getLatLng(), 16);
        markers[key].openPopup();
    }
});

// Autocomplete pencarian
$('#deviceSearch').autocomplete({
    source: [...new Set(searchList)],
    select: function (event, ui) {
        const key = ui.item.value.toLowerCase();
        if (markers[key]) {
            map.setView(markers[key].getLatLng(), 16);
            markers[key].openPopup();
        }
    }
});
</script>
@endpush
