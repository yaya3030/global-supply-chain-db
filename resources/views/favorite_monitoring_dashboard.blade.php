@extends('layouts.app')

@section('title', 'Favorites — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Favorites')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Favorite Monitoring</h1>
        <p class="page-subtitle">Priority items watchlist for quick access and status tracking</p>
    </div>

    <div id="favorites-list" class="stat-grid stagger-children">
        <!-- Loading placeholders -->
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 60%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 80%; margin-bottom: 8px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 40%;"></div>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 60%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 80%; margin-bottom: 8px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 40%;"></div>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="loading-skeleton" style="height: 16px; width: 60%; margin-bottom: 12px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 80%; margin-bottom: 8px;"></div>
            <div class="loading-skeleton" style="height: 12px; width: 40%;"></div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    fetch('/api/favorite-monitoring')
        .then(res => res.json())
        .then(data => {
            let html = "";
            data.data.forEach((item, index) => {
                let badgeClass = "badge-info";
                if (item.risk_level && item.risk_level.toLowerCase().includes('high')) badgeClass = "badge-danger";
                else if (item.risk_level && item.risk_level.toLowerCase().includes('medium')) badgeClass = "badge-warning";
                else if (item.risk_level && item.risk_level.toLowerCase().includes('low')) badgeClass = "badge-success";

                html += `
                    <div class="stat-card animate-fade-up" style="animation-delay: ${index * 80}ms;">
                        <div class="stat-card-icon violet">
                            <i class="ti ti-star"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--violet-700); margin-bottom: 8px;">⚓ ${item.name}</h3>
                        <p style="font-size: 13px; color: var(--gray-600); margin-bottom: 4px;">Status: <strong style="color: var(--gray-800);">${item.status}</strong></p>
                        <p style="font-size: 13px; color: var(--gray-600); margin-bottom: 8px;">Risk: <span class="badge-modern ${badgeClass}">${item.risk_level}</span></p>
                        <p style="font-size: 12px; color: var(--gray-400); margin-top: 8px;">
                            <i class="ti ti-clock" style="font-size: 13px;"></i> ${item.last_update}
                        </p>
                    </div>
                `;
            });
            document.getElementById('favorites-list').innerHTML = html;
        })
        .catch(error => {
            console.error("❌ Favorites Error:", error);
            document.getElementById('favorites-list').innerHTML = `
                <div class="card-modern" style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--danger);">
                    Failed to load favorites data.
                </div>
            `;
        });
</script>
@endsection