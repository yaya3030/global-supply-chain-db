@extends('layouts.app')

@section('title', 'Admin Panel — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Admin Panel')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Admin Control Center</h1>
        <p class="page-subtitle">System monitoring, traffic analytics, and operational metrics</p>
    </div>

    <!-- Stats Cards -->
    <div id="stats-container" class="stat-grid stagger-children mb-4">
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 50%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 24px; width: 70%;"></div>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 50%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 24px; width: 70%;"></div>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 50%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 24px; width: 70%;"></div>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 50%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 24px; width: 70%;"></div>
        </div>
    </div>

    <!-- Traffic Chart -->
    <div class="card-modern animate-fade-up">
        <div class="card-header-modern">
            <span class="card-title-modern"><i class="ti ti-chart-line"></i> System Traffic</span>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="trafficChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    fetch('/api/admin-stats')
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('stats-container').innerHTML = `
                <div class="stat-card animate-fade-up">
                    <div class="stat-card-icon violet"><i class="ti ti-users"></i></div>
                    <p class="stat-card-label">Active Users</p>
                    <p class="stat-card-value">${data.summary.active_users}</p>
                </div>
                <div class="stat-card animate-fade-up" style="animation-delay: 60ms;">
                    <div class="stat-card-icon success"><i class="ti ti-heart-rate-monitor"></i></div>
                    <p class="stat-card-label">System Health</p>
                    <p class="stat-card-value">${data.summary.system_health}</p>
                </div>
                <div class="stat-card animate-fade-up" style="animation-delay: 120ms;">
                    <div class="stat-card-icon info"><i class="ti ti-activity"></i></div>
                    <p class="stat-card-label">API Latency</p>
                    <p class="stat-card-value">${data.summary.api_load}</p>
                </div>
                <div class="stat-card animate-fade-up" style="animation-delay: 180ms;">
                    <div class="stat-card-icon warning"><i class="ti ti-plug"></i></div>
                    <p class="stat-card-label">Active Modules</p>
                    <p class="stat-card-value">${data.summary.active_modules}</p>
                </div>
            `;

            new Chart(document.getElementById('trafficChart'), {
                type: 'line',
                data: {
                    labels: ['1h', '2h', '3h', '4h', '5h', '6h', '7h'],
                    datasets: [{
                        label: 'System Traffic',
                        data: data.traffic_data,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { font: { family: 'Inter', size: 12, weight: 600 } }
                        }
                    },
                    scales: {
                        y: { grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        })
        .catch(error => {
            console.error("❌ Admin Panel Error:", error);
        });
</script>
@endsection