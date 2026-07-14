@extends('layouts.app')

@section('title', 'Risk Scoring — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Risk Scoring')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Risk Scoring Engine</h1>
        <p class="page-subtitle">Multi-dimensional supply chain risk analytics across monitored countries</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Left: Risk Chart -->
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Risk Score Comparison</span>
            </div>
            <div style="position: relative; height: 320px;">
                <canvas id="riskComparisonChart"></canvas>
            </div>
        </div>

        <!-- Right: Risk Matrix Table -->
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-table"></i> Country Risk Matrix</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-modern" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th style="text-align: center;">Infrastructure</th>
                            <th style="text-align: center;">Financial</th>
                            <th style="text-align: center;">Total Score</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="risk-table-body">
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
    let riskComparisonChart = null;

    function fetchAndUpdateRiskData() {
        fetch('/api/risk-scoring')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const countriesLabels = [];
                    const scoresData = [];
                    let tableRowsHtml = "";

                    data.results.forEach(item => {
                        countriesLabels.push(item.country_name);
                        scoresData.push(item.total_risk_score);

                        let badgeClass = "badge-success";
                        if (item.risk_level === 'Medium Risk') badgeClass = "badge-warning";
                        if (item.risk_level === 'High Risk') badgeClass = "badge-danger";

                        tableRowsHtml += `
                            <tr>
                                <td style="font-weight: 600; color: var(--gray-800);">${item.country_name} <span style="color: var(--gray-400); font-size: 12px;">(${item.iso3})</span></td>
                                <td style="text-align: center; color: var(--gray-600);">${item.infrastructure_risk}</td>
                                <td style="text-align: center; color: var(--gray-600);">${item.financial_risk}</td>
                                <td style="text-align: center; font-weight: 700; color: var(--violet-700);">${item.total_risk_score} / 100</td>
                                <td style="text-align: center;"><span class="badge-modern ${badgeClass}">${item.risk_level}</span></td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('risk-table-body');
                    tbody.style.opacity = '0.5';
                    setTimeout(() => {
                        tbody.innerHTML = tableRowsHtml;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease';
                    }, 100);

                    // Chart colors using violet palette
                    const barColors = scoresData.map(score => {
                        if (score >= 70) return 'rgba(239, 68, 68, 0.7)';
                        if (score >= 40) return 'rgba(245, 158, 11, 0.7)';
                        return 'rgba(139, 92, 246, 0.7)';
                    });

                    if (!riskComparisonChart) {
                        const ctx = document.getElementById('riskComparisonChart').getContext('2d');
                        riskComparisonChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: countriesLabels,
                                datasets: [{
                                    label: 'Total Risk Score',
                                    data: scoresData,
                                    backgroundColor: barColors,
                                    borderColor: barColors.map(c => c.replace('0.7', '1')),
                                    borderWidth: 2,
                                    borderRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.04)' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    } else {
                        riskComparisonChart.data.labels = countriesLabels;
                        riskComparisonChart.data.datasets[0].data = scoresData;
                        riskComparisonChart.data.datasets[0].backgroundColor = barColors;
                        riskComparisonChart.update('active');
                    }
                }
            })
            .catch(error => {
                console.error("❌ Risk Scoring Error:", error);
                document.getElementById('risk-table-body').innerHTML = `
                    <tr><td colspan="5" style="text-align: center; color: var(--danger); padding: 24px;">Failed to calculate risk matrix.</td></tr>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndUpdateRiskData();
        setInterval(fetchAndUpdateRiskData, 5000);
    });
</script>
@endsection