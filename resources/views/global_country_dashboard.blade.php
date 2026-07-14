@extends('layouts.app')

@section('title', 'Global Countries — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Global Countries')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Global Country Analytics</h1>
        <p class="page-subtitle">Country inventory, port distribution, and logistics infrastructure overview</p>
    </div>

    <!-- Top Stats -->
    <div class="stat-grid stagger-children mb-4">
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon violet">
                <i class="ti ti-globe"></i>
            </div>
            <p class="stat-card-label">Registered Countries</p>
            <p class="stat-card-value" id="metric-countries">—</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon success">
                <i class="ti ti-anchor"></i>
            </div>
            <p class="stat-card-label">Maritime Port Hubs</p>
            <p class="stat-card-value" id="metric-ports">—</p>
        </div>
    </div>

    <!-- Chart + Table -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Port Distribution by Country</span>
            </div>
            <div style="position: relative; height: 340px;">
                <canvas id="portsDistributionChart"></canvas>
            </div>
        </div>

        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-table"></i> Country Inventory</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-modern" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th style="text-align: center;">ISO2</th>
                            <th style="text-align: center;">ISO3</th>
                            <th style="text-align: center;">Currency</th>
                            <th style="text-align: center;">Ports</th>
                        </tr>
                    </thead>
                    <tbody id="country-table-body">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--gray-400);">
                                <div class="loading-skeleton" style="height: 16px; width: 180px; margin: 0 auto;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/api/countries-summary')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    document.getElementById('metric-countries').innerText = `${result.summary.total_countries} Countries`;
                    document.getElementById('metric-ports').innerText = `${result.summary.total_monitored_ports} Hubs`;

                    const labelNegara = [];
                    const dataJumlahPort = [];
                    let tableRowsHtml = "";

                    result.data.forEach(country => {
                        labelNegara.push(country.name);
                        dataJumlahPort.push(country.ports_count);

                        tableRowsHtml += `
                            <tr>
                                <td style="font-weight: 600; color: var(--gray-800);">${country.name}</td>
                                <td style="text-align: center; color: var(--gray-500); font-size: 13px;">${country.iso2 ?? '-'}</td>
                                <td style="text-align: center; color: var(--gray-500); font-size: 13px;">${country.iso3 ?? '-'}</td>
                                <td style="text-align: center;"><span class="badge-modern badge-violet" style="font-family: monospace;">${country.currency_code ?? '-'}</span></td>
                                <td style="text-align: center; font-weight: 700; color: var(--violet-600);">${country.ports_count}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('country-table-body').innerHTML = tableRowsHtml;

                    const ctx = document.getElementById('portsDistributionChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labelNegara,
                            datasets: [{
                                label: 'Number of Ports',
                                data: dataJumlahPort,
                                backgroundColor: 'rgba(139, 92, 246, 0.6)',
                                borderColor: 'rgba(139, 92, 246, 1)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error("❌ Country Analytics Error:", error);
                document.getElementById('country-table-body').innerHTML = `
                    <tr><td colspan="5" style="text-align: center; color: var(--danger); padding: 24px;">Failed to load country data.</td></tr>
                `;
            });
    });
</script>
@endsection