@extends('layouts.app')

@section('title', 'Favorites — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Favorites')

@section('extra_head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .fav-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 30px;
    }
    
    .fav-actions {
        display: flex;
        gap: 12px;
        background: white;
        padding: 16px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-100);
    }

    .fav-select {
        min-width: 250px;
        border-radius: var(--radius-md);
        border: 1px solid var(--gray-200);
        padding: 10px 16px;
        font-weight: 500;
        outline: none;
    }

    .btn-add {
        background: var(--violet-600);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover {
        background: var(--violet-700);
    }

    .btn-remove {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        position: absolute;
        top: 16px;
        right: 16px;
    }

    .btn-remove:hover {
        background: var(--danger);
        color: white;
    }

    .stat-card {
        position: relative;
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--radius-lg);
        border: 1px dashed var(--gray-300);
        color: var(--gray-500);
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: 16px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="fav-header">
        <div>
            <h1 class="page-title">Favorite Monitoring</h1>
            <p class="page-subtitle">Priority countries watchlist for quick access and status tracking</p>
        </div>
        
        <div class="fav-actions animate-fade-up">
            <select id="countrySelect" class="fav-select">
                <option value="" disabled selected>-- Select a Country to Monitor --</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->iso2 }})</option>
                @endforeach
            </select>
            <button class="btn-add" onclick="addFavorite()">
                <i class="ti ti-plus"></i> Add to Watchlist
            </button>
        </div>
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
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    function loadFavorites() {
        fetch('/api/favorite-monitoring')
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('favorites-list');
                if (data.data.length === 0) {
                    list.innerHTML = `
                        <div class="empty-state animate-fade-up">
                            <i class="ti ti-star"></i>
                            <h3>No countries being monitored yet</h3>
                            <p>Select a country from the dropdown above to add it to your watchlist.</p>
                        </div>
                    `;
                    return;
                }

                let html = "";
                data.data.forEach((item, index) => {
                    let badgeClass = "badge-info";
                    if (item.risk_level === 'High') badgeClass = "badge-danger";
                    else if (item.risk_level === 'Medium') badgeClass = "badge-warning";
                    else if (item.risk_level === 'Low') badgeClass = "badge-success";

                    html += `
                        <div class="stat-card animate-fade-up" style="animation-delay: ${index * 50}ms;">
                            <button class="btn-remove" onclick="removeFavorite(${item.id})" title="Remove from watchlist">
                                <i class="ti ti-trash"></i>
                            </button>
                            <div style="display:flex; align-items:center; gap: 12px; margin-bottom: 16px;">
                                <img src="https://flagcdn.com/w40/${item.iso2}.png" alt="${item.iso2}" style="border-radius:4px; border:1px solid #eee; width: 40px; height: 30px; object-fit:cover;">
                                <h3 style="font-size: 18px; font-weight: 700; color: var(--gray-800); margin:0;">${item.name}</h3>
                            </div>
                            
                            <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 6px;">Status: <strong style="color: var(--gray-800);">${item.status}</strong></p>
                            <p style="font-size: 14px; color: var(--gray-600); margin-bottom: 12px;">Risk Level: <span class="badge-modern ${badgeClass}">${item.risk_level}</span></p>
                            <div style="font-size: 12px; color: var(--gray-400); margin-top: auto; padding-top:12px; border-top:1px solid #f1f5f9;">
                                <i class="ti ti-clock" style="font-size: 13px;"></i> Last updated: ${item.last_update}
                            </div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            })
            .catch(error => {
                console.error("❌ Favorites Error:", error);
                document.getElementById('favorites-list').innerHTML = `
                    <div class="card-modern" style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--danger);">
                        Failed to load watchlist data.
                    </div>
                `;
            });
    }

    function addFavorite() {
        const countryId = document.getElementById('countrySelect').value;
        if (!countryId) {
            alert("Please select a country first.");
            return;
        }

        const btn = document.querySelector('.btn-add');
        btn.innerHTML = '<i class="ti ti-loader"></i> Adding...';
        btn.disabled = true;

        fetch('/api/favorite-monitoring', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ country_id: countryId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                loadFavorites();
                document.getElementById('countrySelect').value = '';
            }
        })
        .catch(err => console.error(err))
        .finally(() => {
            btn.innerHTML = '<i class="ti ti-plus"></i> Add to Watchlist';
            btn.disabled = false;
        });
    }

    function removeFavorite(countryId) {
        if (!confirm('Are you sure you want to remove this country from your watchlist?')) return;
        
        fetch(`/api/favorite-monitoring/${countryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                loadFavorites();
            }
        })
        .catch(err => console.error(err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Since we don't have a layout CSRF by default on some pages, let's inject a fake one if missing
        // just to prevent console errors if the layout doesn't have it (though standard Laravel layout usually does).
        // Actually Laravel API routes in this template might not have web middleware, but it's good practice.
        loadFavorites();
    });
</script>
@endsection