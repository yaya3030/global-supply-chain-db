@extends('layouts.app')

@section('title', 'Data Visualization — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Data Visualization')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Supply Chain Command Center</h1>
        <p class="page-subtitle">Centralized analytics dashboard for efficiency metrics and disruption tracking</p>
    </div>

    <!-- Summary Cards -->
    <div class="stat-grid stagger-children mb-4">
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon violet">
                <i class="ti ti-globe"></i>
            </div>
            <p class="stat-card-label">Country Coverage</p>
            <p class="stat-card-value" id="metric-countries">...</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon success">
                <i class="ti ti-anchor"></i>
            </div>
            <p class="stat-card-label">Port Nodes</p>
            <p class="stat-card-value" id="metric-ports">...</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon info">
                <i class="ti ti-trending-up"></i>
            </div>
            <p class="stat-card-label">Efficiency Index</p>
            <p class="stat-card-value" id="metric-efficiency">...</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon danger">
                <i class="ti ti-alert-triangle"></i>
            </div>
            <p class="stat-card-label">Active Incidents</p>
            <p class="stat-card-value" id="metric-disruptions">...</p>
        </div>
    </div>

    <!-- Charts -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-line"></i> Distribution Efficiency Trend</span>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="efficiencyChart"></canvas>
            </div>
        </div>
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Disruption Incident Frequency</span>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="disruptionChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/data-visualization-metrics')
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.payload;

                    document.getElementById('metric-countries').innerText = data.summary.total_countries;
                    document.getElementById('metric-ports').innerText = data.summary.total_ports;
                    document.getElementById('metric-efficiency').innerText = data.summary.global_efficiency_score;
                    document.getElementById('metric-disruptions').innerText = data.summary.active_disruptions;

                    // Efficiency Line Chart
                    const ctxLine = document.getElementById('efficiencyChart').getContext('2d');
                    new Chart(ctxLine, {
                        type: 'line',
                        data: {
                            labels: data.monthly_trends.labels,
                            datasets: [{
                                label: 'Efficiency Index',
                                data: data.monthly_trends.efficiency_index,
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
                                legend: { labels: { font: { family: 'Inter', size: 12, weight: 600 } } }
                            },
                            scales: {
                                y: { grid: { color: 'rgba(0,0,0,0.04)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });

                    // Disruption Bar Chart
                    const ctxBar = document.getElementById('disruptionChart').getContext('2d');
                    new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: data.monthly_trends.labels,
                            datasets: [{
                                label: 'Incident Count',
                                data: data.monthly_trends.disruption_incidents,
                                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { font: { family: 'Inter', size: 12, weight: 600 } } }
                            },
                            scales: {
                                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error("❌ Command Center Error:", error));
    });
</script>
@endsection