@extends('layouts.app')

@section('title', 'News Intelligence — Supply Chain Risk Intelligence')
@section('breadcrumb', 'News Intelligence')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">News Intelligence</h1>
        <p class="page-subtitle">Event-driven sentiment analysis and supply chain disruption monitoring</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Left: Sentiment Pie -->
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-pie"></i> Impact Distribution</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: center; height: 280px;">
                <canvas id="sentimentPieChart"></canvas>
            </div>
        </div>

        <!-- Right: News Feed Table -->
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-news"></i> Global News Feed</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-modern" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Logistics News Topic</th>
                            <th>Source</th>
                            <th style="text-align: center;">System Impact</th>
                        </tr>
                    </thead>
                    <tbody id="news-table-body">
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 32px; color: var(--gray-400);">
                                <div class="loading-skeleton" style="height: 16px; width: 220px; margin: 0 auto;"></div>
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
    let sentimentPieChart = null;

    function fetchAndUpdateNews() {
        fetch('/api/news-intelligence')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let rowsHtml = "";

                    data.articles.forEach(article => {
                        let badgeClass = "badge-info";
                        if (article.badge_color === 'success') badgeClass = "badge-success";
                        if (article.badge_color === 'warning') badgeClass = "badge-warning";
                        if (article.badge_color === 'danger') badgeClass = "badge-danger";

                        rowsHtml += `
                            <tr>
                                <td style="font-weight: 600; color: var(--gray-800); max-width: 400px;">${article.title}</td>
                                <td style="color: var(--gray-500); font-size: 13px; font-family: monospace;">${article.source}</td>
                                <td style="text-align: center;">
                                    <span class="badge-modern ${badgeClass}" style="text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px;">
                                        ${article.impact_category}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('news-table-body');
                    tbody.style.opacity = '0.5';
                    setTimeout(() => {
                        tbody.innerHTML = rowsHtml;
                        tbody.style.opacity = '1';
                        tbody.style.transition = 'opacity 0.3s ease';
                    }, 100);

                    const dist = data.sentiment_distribution;
                    if (!sentimentPieChart) {
                        const ctx = document.getElementById('sentimentPieChart').getContext('2d');
                        sentimentPieChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Positive', 'Neutral', 'Disruption'],
                                datasets: [{
                                    data: [dist.Positive, dist.Neutral, dist.Disruption],
                                    backgroundColor: [
                                        'rgba(16, 185, 129, 0.8)',
                                        'rgba(139, 92, 246, 0.6)',
                                        'rgba(239, 68, 68, 0.8)'
                                    ],
                                    borderColor: '#fff',
                                    borderWidth: 3,
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '55%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { padding: 16, font: { family: 'Inter', size: 12, weight: 600 } }
                                    }
                                }
                            }
                        });
                    } else {
                        sentimentPieChart.data.datasets[0].data = [dist.Positive, dist.Neutral, dist.Disruption];
                        sentimentPieChart.update('active');
                    }
                }
            })
            .catch(error => {
                console.error("❌ News Intelligence Error:", error);
                document.getElementById('news-table-body').innerHTML = `
                    <tr><td colspan="3" style="text-align: center; color: var(--danger); padding: 24px;">Failed to process news feed.</td></tr>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndUpdateNews();
        setInterval(fetchAndUpdateNews, 5000);
    });
</script>
@endsection