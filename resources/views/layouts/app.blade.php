<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Supply Chain Risk Intelligence Platform')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons (opsional, ikon ringan) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @yield('extra_head')
</head>
<body>

<div class="app-shell d-flex">

    <!-- SIDEBAR -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <i class="ti ti-anchor"></i>
            <span>Logistics ctrl</span>
        </div>
        <nav class="sidebar-nav">

            {{-- Menu ini SUDAH bisa dipencet, karena route 'dashboard' sudah dibuat --}}
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-home"></i> Dashboard
            </a>

            {{--
                Menu di bawah ini SENGAJA masih href="#" (belum bisa dipencet).
                Ini BUKAN error, cuma placeholder sementara.
                Kalau fitur "Risk scoring" kamu sudah jadi dan punya route sendiri
                (misal nama route-nya "risk.index"), ganti baris href="#" jadi:
                href="{{ route('risk.index') }}"
                Lakukan hal yang sama untuk menu lain di bawah sesuai nama route kamu.
            --}}

            <a href="#" class="nav-item">
                <i class="ti ti-alert-triangle"></i> Risk scoring
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-cloud"></i> Weather
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-currency-dollar"></i> Currency
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-news"></i> News
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-anchor"></i> Ports
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-scale"></i> Comparison
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-star"></i> Favorites
            </a>
            <a href="#" class="nav-item">
                <i class="ti ti-settings"></i> Admin panel
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="app-main flex-grow-1">
        @yield('content')
    </main>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@yield('extra_scripts')

</body>
</html>