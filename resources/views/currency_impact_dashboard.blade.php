@extends('layouts.app')

@section('title', 'Currency Impact — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Currency Impact')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Currency Impact Analytics</h1>
        <p class="page-subtitle">Exchange rate volatility and financial logistics risk assessment</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Left: Chart -->
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Currency Risk Index</span>
            </div>
            <div style="position: relative; height: 320px;">
                <canvas id="currencyChart"></canvas>
            </div>
        </div>

        <!-- Right: Table -->
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-table"></i> Financial Cost Impact Matrix</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-modern" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th style="text-align: center;">Currency</th>
                            <th style="text-align: center;">Risk Score</th>
                            <th style="text-align: center;">Cost Surge</th>
                            <th style="text-align: center;">Impact</th>
                        </tr>
                    </thead>
                    <tbody id="currency-table-body">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--gray-400);">
                                <div class="loading-skeleton" style="height: 16px; width: 200px; margin: 0 auto;"></div>
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
    let currencyChart = null;

    function fetchAndUpdateCurrency() {
        fetch('/api/currency-impact-analysis')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const labels = [];
                    const riskScores = [];
                    let tableRowsHtml = "";

                    data.results.forEach(item => {
                        labels.push(item.country_name);
                        riskScores.push(item.currency_risk_score);

                        let badgeClass = "badge-success";
                        if (item.impact_level === 'Moderate Impact') badgeClass = "badge-warning";
                        if (item.impact_level === 'High Impact') badgeClass = "badge-danger";

                        tableRowsHtml += `
                            <tr>
                                <td style="font-weight: 600;">${item.country_name}</td>
                                <td style="text-align: center;"><span class="badge-modern badge-violet" style="font-family: monospace;">${item.currency_code}</span></td>
                                <td style="text-align: center; font-weight: 700; color: var(--gray-800);">${item.currency_risk_score} / 100</td>
                                <td style="text-align: center; font-weight: 600; color: var(--danger);">+${item.cost_surge_estimate}</td>
                                <td style="text-align: center;"><span class="badge-modern ${badgeClass}">${item.impact_level}</span></td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('currency-table-body');
                    tbody.style.opacity = '0.5';
                    setTimeout(() => {
                        tbody.innerHTML = tableRowsHtml;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease';
                    }, 100);

                    if (!currencyChart) {
                        const ctx = document.getElementById('currencyChart').getContext('2d');
                        currencyChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Financial Risk Score',
                                    data: riskScores,
                                    backgroundColor: 'rgba(139, 92, 246, 0.6)',
                                    borderColor: 'rgba(139, 92, 246, 1)',
                                    borderWidth: 2,
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.04)' } },
                                    y: { grid: { display: false } }
                                }
                            }
                        });
                    } else {
                        currencyChart.data.labels = labels;
                        currencyChart.data.datasets[0].data = riskScores;
                        currencyChart.update('active');
                    }
                }
            })
            .catch(error => {
                console.error("❌ Currency Engine Error:", error);
                document.getElementById('currency-table-body').innerHTML = `
                    <tr><td colspan="5" style="text-align: center; color: var(--danger); padding: 24px;">Failed to process currency impact data.</td></tr>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndUpdateCurrency();
        setInterval(fetchAndUpdateCurrency, 5000);
    });
</script>
@endsection