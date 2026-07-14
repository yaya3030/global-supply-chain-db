@extends('layouts.app')

@section('title', 'Dashboard — Global Supply Chain Risk Intelligence')
@section('breadcrumb', 'Global Pulse')

@section('content')
<div class="dashboard-page">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1 class="page-title">Global Pulse</h1>
            <p class="page-subtitle">Real-time supply chain intelligence overview</p>
        </div>
        <div class="header-right">
            <div class="search-box-container">
                <input type="text" id="countrySearch" class="country-search-input"
                       placeholder="🔍 Search country...">
                <div class="search-dropdown" id="searchDropdown" style="display: none;">
                    <div class="search-results" id="searchResults"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Tabs -->
    <div class="country-tabs mb-3" id="countryTabs">
        @foreach($countries as $c)
            <button type="button" class="ctab {{ $loop->first ? 'active' : '' }}" data-country="{{ $c }}">{{ $c }}</button>
        @endforeach
    </div>

    <!-- KPI Row 1 -->
    <div class="kpi-grid mb-3 stagger-children">
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon violet">
                <i class="ti ti-users"></i>
            </div>
            <p class="stat-card-label">Population</p>
            <p class="stat-card-value" id="v-population">-</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon success">
                <i class="ti ti-chart-bar"></i>
            </div>
            <p class="stat-card-label">GDP (National Wealth)</p>
            <p class="stat-card-value" id="v-gdp">-</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon warning">
                <i class="ti ti-trending-up"></i>
            </div>
            <p class="stat-card-label">Inflation Rate</p>
            <p class="stat-card-value" id="v-inflation">-</p>
        </div>
    </div>

    <!-- KPI Row 2 -->
    <div class="kpi-grid mb-3 stagger-children">
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon info">
                <i class="ti ti-currency-dollar"></i>
            </div>
            <p class="stat-card-label">Exchange Rate</p>
            <p class="stat-card-value" id="v-currency">-</p>
        </div>
        <div class="stat-card weather-card animate-fade-up" id="weatherCard">
            <div class="rain-layer" id="rainLayer"></div>
            <div class="weather-card-inner">
                <div>
                    <p class="stat-card-label">Current Weather</p>
                    <p class="stat-card-value" id="v-weather-temp">-</p>
                    <p class="stat-card-sub" id="v-weather-cond">-</p>
                </div>
                <i class="ti ti-cloud weather-icon" id="weatherIcon"></i>
            </div>
        </div>
        <div class="stat-card stat-violet animate-fade-up">
            <div class="stat-card-icon">
                <i class="ti ti-shield-check"></i>
            </div>
            <p class="stat-card-label">Risk Level</p>
            <p class="stat-card-value" id="v-risk">-</p>
        </div>
    </div>

    <!-- Map Panel -->
    <div class="panel-card mb-3 animate-fade-up">
        <p class="panel-title text-blue"><i class="ti ti-map-2"></i> Map — <span id="v-country-map">-</span></p>
        <div id="mapContainer" style="height: 340px; border-radius: 12px; overflow: hidden;"></div>
    </div>

    <!-- Risk Trend Chart -->
    <div class="panel-card mb-3 animate-fade-up">
        <p class="panel-title text-pink"><i class="ti ti-chart-line"></i> Risk Trend — <span id="v-country-chart">-</span></p>
        <div style="position: relative; height: 240px;">
            <canvas id="riskChart"></canvas>
        </div>
    </div>

    <!-- News Panel -->
    <div class="panel-card news-panel animate-fade-up">
        <p class="panel-title text-blue"><i class="ti ti-news"></i> Related News — <span id="v-country-news">-</span></p>
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