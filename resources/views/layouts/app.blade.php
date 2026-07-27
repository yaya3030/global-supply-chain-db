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

            <a href="{{ route('analyzer.index') }}" class="nav-item {{ request()->routeIs('analyzer.*') ? 'active' : '' }}">
                <i class="ti ti-scale"></i> Import Analyzer
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

            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.index') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="ti ti-settings-cog"></i> Admin Dashboard
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user" style="display:flex; align-items:center;">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <span class="sidebar-user-role">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin-left:auto;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:var(--text-secondary); cursor:pointer; padding:5px;" title="Logout">
                        <i class="ti ti-logout" style="font-size:18px;"></i>
                    </button>
                </form>
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
                    <input type="text" id="globalSearchInput" placeholder="Search menu (e.g. Weather)..." autocomplete="off" />
                    <div id="globalSearchDropdown" class="search-dropdown-menu"></div>
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
    // Ripple Effect Logic
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement("span");
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.getBoundingClientRect().left - radius}px`;
        circle.style.top = `${event.clientY - button.getBoundingClientRect().top - radius}px`;
        circle.classList.add("ripple-effect");

        const ripple = button.getElementsByClassName("ripple-effect")[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    // Attach Ripple to buttons and nav items
    document.addEventListener('DOMContentLoaded', () => {
        const rippleElements = document.querySelectorAll('.nav-item, .btn, .stat-card, .sidebar-user');
        rippleElements.forEach(el => {
            el.classList.add('ripple-container');
            // For lighter elements like stat-card, we want a dark ripple
            if(el.classList.contains('stat-card') || el.classList.contains('btn-light')) {
                el.classList.add('ripple-dark');
            }
            el.addEventListener('mousedown', createRipple);
        });

        // Global Quick Navigation Search
        const searchInput = document.getElementById('globalSearchInput');
        const searchDropdown = document.getElementById('globalSearchDropdown');
        
        // Extract all navigation items
        const navItems = Array.from(document.querySelectorAll('.sidebar-nav .nav-item')).map(el => {
            return {
                name: el.textContent.trim(),
                url: el.getAttribute('href'),
                icon: el.querySelector('i').className
            };
        });

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            if (query === '') {
                searchDropdown.style.display = 'none';
                return;
            }

            const results = navItems.filter(item => item.name.toLowerCase().includes(query));
            
            searchDropdown.innerHTML = '';
            if (results.length > 0) {
                results.forEach(item => {
                    const a = document.createElement('a');
                    a.href = item.url;
                    a.className = 'search-dropdown-item';
                    a.innerHTML = `<i class="${item.icon}"></i> ${item.name}`;
                    searchDropdown.appendChild(a);
                });
            } else {
                searchDropdown.innerHTML = '<div class="search-dropdown-empty">No menus found</div>';
            }
            searchDropdown.style.display = 'block';
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.style.display = 'none';
            }
        });
    });
</script>

@yield('extra_scripts')

</body>
</html>