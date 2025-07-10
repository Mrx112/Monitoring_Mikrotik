<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" href="{{ '/logout' }}" onclick="event.preventDefault(); document.getElementById('submit-form').submit()">
            <i class="fas fa-sign-out-alt" style="color: red"></i>
        </a>
      </li>
      <form id="submit-form" action="{{ '/logout' }}" method="POST" class="hidden">
        @csrf
      </form>
      {{-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li> --}}
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4" style="background: #e3f0ff;">
    <!-- Brand Logo -->
    <a href="{{ '/home' }}" class="brand-link text-sm" style="background:#e3f0ff;">
      <img src="{{ asset('assets/stisla/img/logo/logo.png') }}" alt="BMPNet Logo" class="brand-image img-circle elevation-3" style="opacity: .95; width:40px; height:40px; object-fit:cover; background:#fff;">
      <span class="brand-text font-weight-bold" style="color:#007bff;">PT.BMPNet</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('assets/stisla/img/avatar/avatar-1.png') }}" class="img-circle elevation-2" alt="User Image" style="background:#fff;object-fit:cover;">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->username ?? 'PT.BMPNet' }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ '/home' }}" class="nav-link active" style="background: #b3d8fd; color: #222;">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item mt-2">
            <a href="{{ '/user' }}" class="nav-link active" style="background: #b3d8fd; color: #222;">
              <i class="fas fa-users"></i>
              <p>
                User
              </p>
            </a>
          </li>
          <li class="nav-item mt-2">
            <a href="{{ route('queue.list') }}" class="nav-link">
              <i class="fas fa-stream"></i>
              <p>Queue List <span id="queue-status-indicator" style="margin-left:8px;"><span class="dot-status" style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#aaa;"></span></span></p>
            </a>
          </li>
          <li class="nav-item mt-2">
            <a href="{{ url('/tools/ping') }}" class="nav-link">
              <i class="fas fa-network-wired"></i>
              <p>Ping</p>
            </a>
          </li>
          <li class="nav-item mt-2">
            <a href="{{ url('/tools/access-link') }}" class="nav-link">
              <i class="fas fa-link"></i>
              <p>Access Link</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  @yield('content')
  <footer class="main-footer text-sm">
    <strong>Copyright &copy; 2025 <a href="https://bmp.net.id/">BMPNet Team</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Admin Panel Mikrotik Version</b> 3.0.5
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
@stack('js-page')
@push('js-page')
<script>
function updateQueueStatus() {
  fetch('/api/queue-status')
    .then(res => res.json())
    .then(data => {
      const dot = document.querySelector('#queue-status-indicator .dot-status');
      if (dot) {
        if (data.status === 'up') {
          dot.style.background = '#28a745'; // green
        } else if (data.status === 'down') {
          dot.style.background = '#dc3545'; // red
        } else {
          dot.style.background = '#aaa'; // gray
        }
      }
    });
}
setInterval(updateQueueStatus, 5000);
document.addEventListener('DOMContentLoaded', updateQueueStatus);
</script>
@endpush
</body>
</html>
