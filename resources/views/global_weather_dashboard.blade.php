@extends('layouts.app')

@section('title', 'Weather Monitor — Peta Cuaca Negara Dunia')
@section('breadcrumb', 'Weather Monitor')

@section('extra_head')
<style>
    /* Weather Dashboard Styling */
    .weather-controls-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 24px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
    }

    .controls-grid {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .country-selector-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 280px;
    }

    .country-selector-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--gray-700);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .country-select-input {
        width: 100%;
        max-width: 340px;
        padding: 10px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--gray-300);
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--gray-800);
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .country-select-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .weather-filter-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .weather-filter-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 700;
        border: 1.5px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .weather-filter-btn:hover {
        transform: translateY(-1px);
    }

    .weather-filter-btn.active.rain {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }

    .weather-filter-btn.active.storm {
        background: #fcf4ff;
        border-color: #9333ea;
        color: #7e22ce;
    }

    .weather-filter-btn.active.strong_wind {
        background: #ecfeff;
        border-color: #0891b2;
        color: #0e7490;
    }

    .weather-filter-btn.active.clear {
        background: #ecfdf5;
        border-color: #10b981;
        color: #047857;
    }

    .filter-count-badge {
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 0.75rem;
        background: rgba(0, 0, 0, 0.08);
    }

    /* Weather Map Styling */
    .map-container-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--gray-200);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        margin-bottom: 24px;
        position: relative;
    }

    #weatherMap {
        height: 480px;
        width: 100%;
        z-index: 1;
    }

    /* Custom Map Marker Styling per Country */
    .custom-country-marker {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #ffffff;
        font-size: 18px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        border: 2px solid #ffffff;
        transition: transform 0.25s ease;
        cursor: pointer;
    }

    .custom-country-marker:hover {
        transform: scale(1.3);
    }

    .marker-rain { background: #3b82f6; }
    .marker-storm { background: #8b5cf6; animation: pulse-warning 1.5s infinite; }
    .marker-strong_wind { background: #06b6d4; }
    .marker-clear { background: #10b981; }

    @keyframes pulse-warning {
        0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
        70% { box-shadow: 0 0 0 16px rgba(139, 92, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
    }

    /* Map Legend Overlay */
    .map-legend {
        position: absolute;
        bottom: 16px;
        left: 16px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 16px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .table-row-clickable:hover {
        background: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <!-- PAGE HEADER -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="page-title"><i class="ti ti-world-cloud" style="color: #4f46e5;"></i> Country Weather Monitor</h1>
            <p class="page-subtitle">Peta Cuaca Dunia Berdasarkan Negara: Status Hujan, Badai, & Angin Kencang.</p>
        </div>
        <span id="sync-time" class="badge-modern badge-violet" style="font-size: 12px; padding: 6px 16px;">
            <i class="ti ti-refresh" style="font-size: 14px;"></i> Syncing weather data...
        </span>
    </div>

    <!-- CONTROLS & COUNTRY SELECTOR -->
    <div class="weather-controls-card animate-fade-up">
        <div class="controls-grid">
            <div class="country-selector-group">
                <label class="country-selector-label" for="country-select">
                    <i class="ti ti-flag" style="color: #4f46e5; font-size: 1.1rem;"></i> Pilih Negara:
                </label>
                <select id="country-select" class="country-select-input" onchange="onCountryChange()">
                    <option value="">🌍 Semua Negara (Global View)</option>
                </select>
            </div>

            <!-- WEATHER TYPE FILTER BADGES -->
            <div class="weather-filter-badges">
                <button onclick="toggleWeatherFilter('all')" id="filter-btn-all" class="weather-filter-btn active">
                    Semua <span class="filter-count-badge" id="count-all">0</span>
                </button>
                <button onclick="toggleWeatherFilter('rain')" id="filter-btn-rain" class="weather-filter-btn rain">
                    🌧️ Hujan <span class="filter-count-badge" id="count-rain">0</span>
                </button>
                <button onclick="toggleWeatherFilter('storm')" id="filter-btn-storm" class="weather-filter-btn storm">
                    ⚡ Badai <span class="filter-count-badge" id="count-storm">0</span>
                </button>
                <button onclick="toggleWeatherFilter('strong_wind')" id="filter-btn-strong_wind" class="weather-filter-btn strong_wind">
                    💨 Angin Kencang <span class="filter-count-badge" id="count-strong_wind">0</span>
                </button>
                <button onclick="toggleWeatherFilter('clear')" id="filter-btn-clear" class="weather-filter-btn clear">
                    ☀️ Cerah <span class="filter-count-badge" id="count-clear">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE MAP CONTAINER -->
    <div class="map-container-card animate-fade-up">
        <div id="weatherMap"></div>
        
        <!-- MAP LEGEND -->
        <div class="map-legend">
            <div class="legend-item"><span class="legend-dot" style="background:#3b82f6;"></span> Hujan</div>
            <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6;"></span> Badai</div>
            <div class="legend-item"><span class="legend-dot" style="background:#06b6d4;"></span> Angin Kencang</div>
            <div class="legend-item"><span class="legend-dot" style="background:#10b981;"></span> Cerah</div>
        </div>
    </div>

    <!-- COUNTRY WEATHER TABLE -->
    <div class="card-modern animate-fade-up">
        <div class="card-header-modern" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="card-title-modern"><i class="ti ti-cloud-storm"></i> Country Weather Status Overview</span>
            <span id="active-country-label" style="font-weight: 600; font-size: 0.88rem; color: var(--violet-600);">Global View (Semua Negara)</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah / Region</th>
                        <th style="text-align: center;">Kategori Cuaca</th>
                        <th style="text-align: center;">Suhu</th>
                        <th style="text-align: center;">Kecepatan Angin</th>
                        <th style="text-align: center;">Visibilitas</th>
                        <th style="text-align: center;">Status Navigasi</th>
                    </tr>
                </thead>
                <tbody id="weather-table-body">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--gray-400);">
                            <div class="loading-skeleton" style="height: 16px; width: 240px; margin: 0 auto;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    let map;
    let markersLayerGroup;
    let circlesLayerGroup;
    let markersMap = {};

    let allCountryWeatherData = [];
    let allCountriesOptions = [];
    let activeFilter = 'all';
    let selectedCountryId = '';

    // Initialize Map
    function initMap() {
        map = L.map('weatherMap', {
            center: [20.0, 10.0],
            zoom: 2,
            minZoom: 2,
            maxZoom: 12,
            zoomControl: true
        });

        // Tile layer CartoDB Voyager
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        markersLayerGroup = L.layerGroup().addTo(map);
        circlesLayerGroup = L.layerGroup().addTo(map);
    }

    // Fetch Country Weather Data from API
    function fetchWeatherData() {
        fetch('/api/global-weather-status')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    document.getElementById('sync-time').innerHTML = `<i class="ti ti-check" style="font-size: 14px;"></i> Updated: ${res.updated_at}`;
                    
                    allCountryWeatherData = res.data;

                    // Populate Country Select Dropdown once
                    if (allCountriesOptions.length === 0 && res.countries) {
                        allCountriesOptions = res.countries;
                        populateCountrySelect(allCountriesOptions);
                    }

                    // Update Badge Counts
                    document.getElementById('count-all').textContent = res.summary ? (res.summary.total_rain + res.summary.total_storm + res.summary.total_strong_wind + res.summary.total_clear) : allCountryWeatherData.length;
                    document.getElementById('count-rain').textContent = res.summary ? res.summary.total_rain : 0;
                    document.getElementById('count-storm').textContent = res.summary ? res.summary.total_storm : 0;
                    document.getElementById('count-strong_wind').textContent = res.summary ? res.summary.total_strong_wind : 0;
                    document.getElementById('count-clear').textContent = res.summary ? res.summary.total_clear : 0;

                    // Render Map & Table
                    renderMapAndTable();
                }
            })
            .catch(err => {
                console.error("❌ Country Weather API Error:", err);
            });
    }

    // Populate Select Options
    function populateCountrySelect(countries) {
        const select = document.getElementById('country-select');
        select.innerHTML = '<option value="">🌍 Semua Negara (Global View)</option>' + 
            countries.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    // Render Country Markers & Table
    function renderMapAndTable() {
        markersLayerGroup.clearLayers();
        circlesLayerGroup.clearLayers();
        markersMap = {};

        // Filter data based on weather type filter
        let filtered = allCountryWeatherData;
        if (activeFilter !== 'all') {
            filtered = allCountryWeatherData.filter(item => item.weather_type === activeFilter);
        }

        let tableRowsHtml = "";

        if (filtered.length === 0) {
            document.getElementById('weather-table-body').innerHTML = `
                <tr><td colspan="7" style="text-align: center; color: var(--gray-400); padding: 24px;">Tidak ada data negara untuk kategori cuaca ini.</td></tr>
            `;
            return;
        }

        filtered.forEach(item => {
            let iconClass = 'ti-sun';
            let markerBgClass = 'marker-clear';
            let circleColor = '#10b981';
            let weatherBadge = '<span class="badge-modern badge-success">☀️ Cerah</span>';

            if (item.weather_type === 'rain') {
                iconClass = 'ti-cloud-rain';
                markerBgClass = 'marker-rain';
                circleColor = '#3b82f6';
                weatherBadge = '<span class="badge-modern badge-info">🌧️ Hujan</span>';
            } else if (item.weather_type === 'storm') {
                iconClass = 'ti-bolt';
                markerBgClass = 'marker-storm';
                circleColor = '#8b5cf6';
                weatherBadge = '<span class="badge-modern badge-danger">⚡ Badai</span>';
            } else if (item.weather_type === 'strong_wind') {
                iconClass = 'ti-wind';
                markerBgClass = 'marker-strong_wind';
                circleColor = '#06b6d4';
                weatherBadge = '<span class="badge-modern badge-warning">💨 Angin Kencang</span>';
            }

            const iconHtml = `<div class="custom-country-marker ${markerBgClass}" title="${item.country_name}"><i class="ti ${iconClass}"></i></div>`;
            const customIcon = L.divIcon({
                html: iconHtml,
                className: '',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            if (item.latitude !== undefined && item.longitude !== undefined) {
                const marker = L.marker([item.latitude, item.longitude], { icon: customIcon });

                const popupContent = `
                    <div style="font-family: 'Inter', sans-serif; padding: 4px; min-width: 190px;">
                        <div style="font-weight: 700; font-size: 1.05rem; color: #111827; display: flex; align-items: center; gap: 6px;">
                            <span>🌐 ${item.country_name}</span>
                            <span style="font-size: 0.75rem; color: #6b7280;">(${item.iso2})</span>
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280; margin-bottom: 8px;">Wilayah: ${item.region}</div>
                        <div style="margin-bottom: 10px;">${weatherBadge}</div>
                        <div style="font-size: 0.85rem; line-height: 1.5; color: #374151;">
                            <div>🌡️ <strong>Suhu:</strong> ${item.temperature}</div>
                            <div>💨 <strong>Kecepatan Angin:</strong> ${item.wind_speed}</div>
                            <div>👁️ <strong>Visibilitas:</strong> ${item.visibility}</div>
                            <div>🌊 <strong>Kondisi:</strong> ${item.condition}</div>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent);
                markersLayerGroup.addLayer(marker);

                // Store marker reference for lookup
                markersMap[item.country_id] = marker;
                markersMap[item.country_name.toLowerCase()] = marker;

                const circle = L.circle([item.latitude, item.longitude], {
                    color: circleColor,
                    fillColor: circleColor,
                    fillOpacity: 0.22,
                    radius: item.weather_type === 'storm' ? 120000 : (item.weather_type === 'strong_wind' ? 90000 : 70000)
                });
                circlesLayerGroup.addLayer(circle);
            }

            let navBadgeClass = "badge-success";
            if (item.safety_status === 'Warning') navBadgeClass = "badge-warning";
            if (item.safety_status === 'Alert') navBadgeClass = "badge-danger";

            tableRowsHtml += `
                <tr onclick="focusCountryOnMap(${item.country_id})" class="table-row-clickable" style="cursor: pointer;" title="Klik untuk arahkan peta ke lokasi ${item.country_name}">
                    <td style="font-weight: 700; color: var(--violet-700);">🌐 ${item.country_name} (${item.iso2})</td>
                    <td style="font-weight: 500; color: var(--gray-600);">${item.region}</td>
                    <td style="text-align: center;">${weatherBadge}</td>
                    <td style="text-align: center; font-weight: 600;">${item.temperature}</td>
                    <td style="text-align: center; font-family: 'SF Mono', monospace; font-size: 13px;">${item.wind_speed}</td>
                    <td style="text-align: center; color: var(--gray-500); font-size: 13px;">${item.visibility}</td>
                    <td style="text-align: center;"><span class="badge-modern ${navBadgeClass}">${item.safety_status}</span></td>
                </tr>
            `;
        });

        document.getElementById('weather-table-body').innerHTML = tableRowsHtml;
    }

    // Country Change Event Handler from Dropdown
    function onCountryChange() {
        const select = document.getElementById('country-select');
        selectedCountryId = select.value;

        const label = document.getElementById('active-country-label');

        if (!selectedCountryId) {
            label.textContent = 'Global View (Semua Negara)';
            map.flyTo([20.0, 10.0], 2, { animate: true, duration: 1.2 });
            return;
        }

        const countryObj = allCountryWeatherData.find(c => c.country_id == selectedCountryId);

        if (countryObj && countryObj.latitude !== undefined && countryObj.longitude !== undefined) {
            label.textContent = `Negara: ${countryObj.country_name}`;
            
            // Fly map directly to accurate country coordinates
            map.flyTo([countryObj.latitude, countryObj.longitude], 5, {
                animate: true,
                duration: 1.2
            });

            // Open Popup after camera arrival
            setTimeout(() => {
                const targetMarker = markersMap[countryObj.country_id] || markersMap[countryObj.country_name.toLowerCase()];
                if (targetMarker) {
                    targetMarker.openPopup();
                }
            }, 600);
        }
    }

    // Focus map when clicking a country row in table
    function focusCountryOnMap(countryId) {
        document.getElementById('country-select').value = countryId;
        onCountryChange();
    }

    // Weather Type Filter Button Event Handler
    function toggleWeatherFilter(type) {
        activeFilter = type;
        document.querySelectorAll('.weather-filter-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`filter-btn-${type}`).classList.add('active');
        renderMapAndTable();
    }

    // DOM Ready Initialization
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        fetchWeatherData();
        setInterval(fetchWeatherData, 10000);
    });
</script>
@endsection