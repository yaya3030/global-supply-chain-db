@extends('layouts.app')

@section('title', 'Dashboard - Global Supply Chain Risk Intelligence')

@section('content')
<!-- Library Dependencies -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-page">
    <div class="dashboard-header">
        <div class="header-left">
            <h2 class="dashboard-title">Global pulse</h2>
            <p class="dashboard-subtitle">Real-time Supply Chain Intelligence</p>
        </div>
        <div class="header-right">
            <div class="search-box-container">
                <input type="text" id="countrySearch" class="country-search-input" 
                       placeholder="🔍 Cari negara...">
                <div class="search-dropdown" id="searchDropdown" style="display: none;">
                    <div class="search-results" id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="country-tabs mb-3" id="countryTabs">
        @foreach($countries as $c)
            <button type="button" class="ctab {{ $loop->first ? 'active' : '' }}" data-country="{{ $c }}">{{ $c }}</button>
        @endforeach
    </div>

    <div class="kpi-grid mb-2">
        <div class="kpi-card kpi-blue">
            <i class="ti ti-users"></i>
            <p class="kpi-label">Jumlah penduduk</p>
            <p class="kpi-value" id="v-population">-</p>
        </div>
        <div class="kpi-card kpi-pink">
            <i class="ti ti-chart-bar"></i>
            <p class="kpi-label">Kekayaan negara (GDP)</p>
            <p class="kpi-value" id="v-gdp">-</p>
        </div>
        <div class="kpi-card kpi-blue">
            <i class="ti ti-tag"></i>
            <p class="kpi-label">Kenaikan harga (inflasi)</p>
            <p class="kpi-value" id="v-inflation">-</p>
        </div>
    </div>

    <div class="kpi-grid mb-3">
        <div class="kpi-card kpi-pink">
            <i class="ti ti-currency-dollar"></i>
            <p class="kpi-label">Nilai tukar mata uang</p>
            <p class="kpi-value" id="v-currency">-</p>
        </div>
        <div class="kpi-card kpi-blue weather-card" id="weatherCard">
            <div class="rain-layer" id="rainLayer"></div>
            <div class="weather-card-inner">
                <div>
                    <p class="kpi-label">Cuaca sekarang</p>
                    <p class="kpi-value" id="v-weather-temp">-</p>
                    <p class="kpi-sub" id="v-weather-cond">-</p>
                </div>
                <i class="ti ti-cloud weather-icon" id="weatherIcon"></i>
            </div>
        </div>
        <div class="kpi-card kpi-pink">
            <i class="ti ti-shield-check"></i>
            <p class="kpi-label">Tingkat risiko</p>
            <p class="kpi-value" id="v-risk">-</p>
        </div>
    </div>

    <div class="panel-card mb-3">
        <p class="panel-title text-blue"><i class="ti ti-map-2"></i> Peta — <span id="v-country-map">-</span></p>
        <div id="mapContainer" style="height: 320px; border-radius: 12px; overflow: hidden;"></div>
    </div>

    <div class="panel-card mb-3">
        <p class="panel-title text-pink"><i class="ti ti-chart-line"></i> Tren risiko — <span id="v-country-chart">-</span></p>
        <div style="position: relative; height: 220px;">
            <canvas id="riskChart"></canvas>
        </div>
    </div>

    <div class="panel-card news-panel">
        <p class="panel-title text-pink"><i class="ti ti-news"></i> Berita terkait <span id="v-country-news">-</span></p>
        <div class="news-grid" id="newsGrid"></div>
    </div>
</div>

<script>
    window.DASHBOARD_COUNTRIES = @json($countries);
    window.DASHBOARD_API_BASE = "{{ url('/api') }}";
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('js/dashboard-realtime.js') }}"></script>
@endsection