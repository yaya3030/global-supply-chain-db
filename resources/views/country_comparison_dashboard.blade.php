@extends('layouts.app')

@section('title', 'Country Comparison — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Country Compare')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Cross-Country Performance</h1>
        <p class="page-subtitle">Benchmarking supply chain efficiency, risk, and stability across logistics hubs</p>
    </div>

    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px;">
        <!-- Left: Multi-Bar Chart -->
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Comparative Analysis</span>
            </div>
            <div style="position: relative; height: 340px;">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>

        <!-- Right: Score Table -->
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-table"></i> Indicator Scores</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-modern" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th style="text-align: center;">Efficiency</th>
                            <th style="text-align: center;">Risk</th>
                            <th style="text-align: center;">Stability</th>
                        </tr>
                    </thead>
                    <tbody id="comparison-table-body">
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 32px; color: var(--gray-400);">
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
    let comparisonChart = null;

    function fetchAndUpdateComparison() {
        fetch('/api/country-comparison-data')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const labels = [];
                    const efficiencyScores = [];
                    const riskScores = [];
                    const stabilityScores = [];
                    let tableRowsHtml = "";

                    data.results.forEach(item => {
                        labels.push(item.country_name);
                        efficiencyScores.push(item.efficiency_score);
                        riskScores.push(item.risk_score);
                        stabilityScores.push(item.currency_stability);

                        tableRowsHtml += `
                            <tr>
                                <td style="font-weight: 600; color: var(--violet-700);">${item.country_name} <span style="color: var(--gray-400); font-size: 11px;">(${item.currency_code})</span></td>
                                <td style="text-align: center; font-weight: 600; color: var(--success);">${item.efficiency_score}%</td>
                                <td style="text-align: center; font-weight: 600; color: var(--danger);">${item.risk_score}%</td>
                                <td style="text-align: center; font-weight: 600; color: var(--info);">${item.currency_stability}%</td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('comparison-table-body');
                    tbody.style.opacity = '0.5';
                    setTimeout(() => {
                        tbody.innerHTML = tableRowsHtml;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease';
                    }, 100);

                    if (!comparisonChart) {
                        const ctx = document.getElementById('comparisonChart').getContext('2d');
                        comparisonChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Efficiency',
                                        data: efficiencyScores,
                                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                        borderColor: 'rgba(16, 185, 129, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Risk',
                                        data: riskScores,
                                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                        borderColor: 'rgba(239, 68, 68, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Stability',
                                        data: stabilityScores,
                                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                                        borderColor: 'rgba(139, 92, 246, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: { font: { family: 'Inter', size: 12, weight: 600 }, padding: 16 }
                                    }
                                },
                                scales: {
                                    y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.04)' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    } else {
                        comparisonChart.data.labels = labels;
                        comparisonChart.data.datasets[0].data = efficiencyScores;
                        comparisonChart.data.datasets[1].data = riskScores;
                        comparisonChart.data.datasets[2].data = stabilityScores;
                        comparisonChart.update('active');
                    }
                }
            })
            .catch(error => {
                console.error("❌ Comparison Error:", error);
                document.getElementById('comparison-table-body').innerHTML = `
                    <tr><td colspan="4" style="text-align: center; color: var(--danger); padding: 24px;">Failed to process comparative analysis.</td></tr>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndUpdateComparison();
        setInterval(fetchAndUpdateComparison, 5000);
    });
</script>
@endsection