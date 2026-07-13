<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Supply Chain Risk Intelligence Platform')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-professional.css') }}">

    @yield('extra_head')

    <style>
        /* CSS Wajib agar Sidebar berfungsi sebagai Link */
        .app-shell { display: flex; min-height: 100vh; }
        .app-sidebar { width: 250px; background: #ffffff; border-right: 1px solid #dee2e6; flex-shrink: 0; }
        .sidebar-brand { padding: 20px; font-weight: bold; font-size: 1.2rem; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px; }
        .sidebar-nav { padding: 10px; display: flex; flex-direction: column; gap: 5px; }
        .nav-item { 
            display: flex; align-items: center; gap: 10px; padding: 12px 15px; 
            text-decoration: none; color: #333; border-radius: 8px; transition: 0.2s; 
        }
        .nav-item:hover { background-color: #f1f3f5; color: #0d6efd; }
        .nav-item.active { background-color: #e7f1ff; color: #0d6efd; font-weight: 600; }
        .app-main { padding: 20px; background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <i class="ti ti-anchor"></i>
            <span>Logistics ctrl</span>
        </div>
        <nav class="sidebar-nav">

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-home"></i> Dashboard
            </a>

            <a href="{{ route('risk.index') }}" class="nav-item {{ request()->routeIs('risk.*') ? 'active' : '' }}">
                <i class="ti ti-alert-triangle"></i> Risk scoring
            </a>

            <a href="{{ route('weather.index') }}" class="nav-item {{ request()->routeIs('weather.*') ? 'active' : '' }}">
                <i class="ti ti-cloud"></i> Weather
            </a>

            <a href="{{ route('currency.index') }}" class="nav-item {{ request()->routeIs('currency.*') ? 'active' : '' }}">
                <i class="ti ti-currency-dollar"></i> Currency
            </a>

            <a href="{{ route('news.index') }}" class="nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                <i class="ti ti-news"></i> News
            </a>

            <a href="{{ route('ports.index') }}" class="nav-item {{ request()->routeIs('ports.*') ? 'active' : '' }}">
                <i class="ti ti-anchor"></i> Ports
            </a>

            <a href="{{ route('comparison.index') }}" class="nav-item {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
                <i class="ti ti-scale"></i> Comparison
            </a>

            <a href="{{ route('favorites.index') }}" class="nav-item {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                <i class="ti ti-star"></i> Favorites
            </a>

            <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="ti ti-settings"></i> Admin panel
            </a>

        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="app-main">
        @yield('content')
    </main>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Universal Realtime Engine -->
<script src="{{ asset('js/universal-realtime.js') }}"></script>

@yield('extra_scripts')

</body>
</html>