<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Supply Chain Risk Intelligence Platform')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Design System -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    @yield('extra_head')
</head>
<body>

<div class="app-shell">

    <!-- SIDEBAR OVERLAY (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="ti ti-world"></i>
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">LogisticsCtrl</span>
                <span class="sidebar-brand-subtitle">Intelligence Hub</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i> Dashboard
            </a>

            <div class="nav-section">Analytics</div>

            <a href="{{ route('risk.index') }}" class="nav-item {{ request()->routeIs('risk.*') ? 'active' : '' }}">
                <i class="ti ti-shield-exclamation"></i> Risk Scoring
            </a>

            <a href="{{ route('weather.index') }}" class="nav-item {{ request()->routeIs('weather.*') ? 'active' : '' }}">
                <i class="ti ti-cloud-storm"></i> Weather Monitor
            </a>

            <a href="{{ route('currency.index') }}" class="nav-item {{ request()->routeIs('currency.*') ? 'active' : '' }}">
                <i class="ti ti-currency-dollar"></i> Currency Impact
            </a>

            <a href="{{ route('news.index') }}" class="nav-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                <i class="ti ti-news"></i> News Intelligence
            </a>

            <div class="nav-section">Explore</div>

            <a href="{{ route('ports.index') }}" class="nav-item {{ request()->routeIs('ports.*') ? 'active' : '' }}">
                <i class="ti ti-anchor"></i> Port Locations
            </a>

            <a href="{{ route('comparison.index') }}" class="nav-item {{ request()->routeIs('comparison.*') ? 'active' : '' }}">
                <i class="ti ti-arrows-exchange"></i> Country Compare
            </a>

            <a href="{{ route('global.country') }}" class="nav-item {{ request()->routeIs('global.*') ? 'active' : '' }}">
                <i class="ti ti-globe"></i> Global Countries
            </a>

            <a href="{{ route('visualization.index') }}" class="nav-item {{ request()->routeIs('visualization.*') ? 'active' : '' }}">
                <i class="ti ti-chart-dots-3"></i> Data Visualization
            </a>

            <div class="nav-section">System</div>

            <a href="{{ route('favorites.index') }}" class="nav-item {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                <i class="ti ti-star"></i> Favorites
            </a>

            <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="ti ti-settings-cog"></i> Admin Panel
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">A</div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">Admin User</span>
                    <span class="sidebar-user-role">Operator</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="app-main">
        <!-- TOPBAR -->
        <header class="app-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="ti ti-menu-2"></i>
                </button>
                <div class="topbar-breadcrumb">
                    <a href="{{ route('dashboard') }}">Home</a>
                    <span class="separator"><i class="ti ti-chevron-right"></i></span>
                    <span class="current">@yield('breadcrumb', 'Dashboard')</span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-search">
                    <i class="ti ti-search"></i>
                    <input type="text" placeholder="Search anything..." />
                </div>
                <div class="topbar-clock" id="topbarClock">--:--</div>
                <div class="topbar-notification">
                    <i class="ti ti-bell"></i>
                    <span class="badge-dot"></span>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

</div>

<!-- Bootstrap JS (utilities only) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Universal Realtime Engine -->
<script src="{{ asset('js/universal-realtime.js') }}"></script>

<!-- Sidebar Toggle & Clock Script -->
<script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }

    // Close sidebar when clicking outside (mobile)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            document.getElementById('appSidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
    });

    // Live Clock
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('topbarClock');
        if (el) el.textContent = `${h}:${m}:${s}`;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

@yield('extra_scripts')

</body>
</html>