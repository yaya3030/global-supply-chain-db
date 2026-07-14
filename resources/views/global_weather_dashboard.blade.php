@extends('layouts.app')

@section('title', 'Weather Monitor — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Weather Monitor')

@section('content')
<div class="dashboard-page">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="page-title">Maritime Weather Monitor</h1>
            <p class="page-subtitle">Live weather conditions across international port stations</p>
        </div>
        <span id="sync-time" class="badge-modern badge-violet" style="font-size: 12px; padding: 6px 16px;">
            <i class="ti ti-refresh" style="font-size: 14px;"></i> Syncing...
        </span>
    </div>

    <div class="card-modern animate-fade-up">
        <div class="card-header-modern">
            <span class="card-title-modern"><i class="ti ti-cloud-storm"></i> Port Weather Status</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Port</th>
                        <th>Country</th>
                        <th style="text-align: center;">Temperature</th>
                        <th style="text-align: center;">Wind Speed</th>
                        <th style="text-align: center;">Visibility</th>
                        <th>Condition</th>
                        <th style="text-align: center;">Navigation</th>
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
    function fetchAndUpdateWeather() {
        fetch('/api/global-weather-status')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    document.getElementById('sync-time').innerHTML = `<i class="ti ti-check" style="font-size: 14px;"></i> Updated: ${result.updated_at}`;
                    let rowsHtml = "";

                    result.data.forEach(item => {
                        let badgeClass = "badge-success";
                        if (item.safety_status === 'Warning') badgeClass = "badge-warning";
                        if (item.safety_status === 'Alert') badgeClass = "badge-danger";

                        rowsHtml += `
                            <tr>
                                <td style="font-weight: 600; color: var(--violet-700);">⚓ ${item.port_name}</td>
                                <td>${item.country_name}</td>
                                <td style="text-align: center; font-weight: 600;">${item.temperature}</td>
                                <td style="text-align: center; font-family: 'SF Mono', monospace; font-size: 13px;">${item.wind_speed}</td>
                                <td style="text-align: center; color: var(--gray-500); font-size: 13px;">${item.visibility}</td>
                                <td style="color: var(--gray-500);">${item.condition}</td>
                                <td style="text-align: center;"><span class="badge-modern ${badgeClass}">${item.safety_status}</span></td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('weather-table-body');
                    tbody.style.opacity = '0.5';
                    setTimeout(() => {
                        tbody.innerHTML = rowsHtml;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease';
                    }, 100);
                }
            })
            .catch(error => {
                console.error("❌ Weather Engine Error:", error);
                document.getElementById('weather-table-body').innerHTML = `
                    <tr><td colspan="7" style="text-align: center; color: var(--danger); padding: 24px;">Failed to scan maritime weather stations.</td></tr>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndUpdateWeather();
        setInterval(fetchAndUpdateWeather, 5000);
    });
</script>
@endsection