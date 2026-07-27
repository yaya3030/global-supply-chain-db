@extends('layouts.app')

@section('title', 'News Intelligence — Logistics, Trade, Shipping & Economy')
@section('breadcrumb', 'News Intelligence')

@section('extra_head')
<style>
    /* ─── Country Selector Banner ─── */
    .news-country-banner {
        background: linear-gradient(135deg, var(--violet-950) 0%, var(--violet-900) 100%);
        border-radius: 16px;
        padding: 22px 28px;
        margin-bottom: 24px;
        border: 1px solid rgba(236, 72, 153, 0.2);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .news-country-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 160px;
        background: radial-gradient(ellipse at 20% 0%, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .banner-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .banner-title { font-size: 1.3rem; font-weight: 900; color: #fff; display: flex; align-items: center; gap: 10px; }
    .banner-subtitle { font-size: 0.88rem; color: var(--violet-200); margin-top: 4px; font-weight: 500; }

    .country-select-news {
        padding: 11px 18px;
        border-radius: 12px;
        border: 1.5px solid rgba(255,255,255,0.25);
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        background: rgba(255,255,255,0.12);
        outline: none;
        min-width: 280px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .country-select-news:focus {
        border-color: var(--violet-400);
        background: rgba(255,255,255,0.18);
    }

    .country-select-news option { background: var(--violet-950); color: #fff; }

    /* ─── Selected Country Banner ─── */
    .selected-country-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: rgba(255,255,255,0.15);
        border-radius: 9999px;
        font-size: 0.92rem;
        font-weight: 800;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        margin-top: 8px;
    }

    /* ─── Stats Row ─── */
    .news-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 900px) { .news-stats-row { grid-template-columns: repeat(2, 1fr); } }

    .news-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .news-stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

    .news-stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }

    .news-stat-icon.violet { background: #f3e8ff; }
    .news-stat-icon.blue   { background: #dbeafe; }
    .news-stat-icon.green  { background: #dcfce7; }
    .news-stat-icon.amber  { background: #fef3c7; }

    .news-stat-label { font-size: 0.75rem; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.4px; }
    .news-stat-value { font-size: 1.6rem; font-weight: 900; color: var(--gray-900); line-height: 1; margin-top: 2px; }

    /* ─── Category Tabs ─── */
    .news-tabs-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .news-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid var(--gray-200);
        background: #fff;
        color: var(--gray-500);
        box-shadow: var(--shadow-sm);
    }

    .news-tab-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }

    .news-tab-btn.active {
        background: linear-gradient(135deg, var(--violet-600), var(--violet-700));
        color: #fff;
        border-color: transparent;
        box-shadow: var(--shadow-violet);
    }

    .tab-count {
        background: rgba(255,255,255,0.25);
        padding: 2px 8px; border-radius: 99px;
        font-size: 0.76rem; font-weight: 700;
    }

    .news-tab-btn:not(.active) .tab-count {
        background: var(--gray-100);
        color: var(--gray-500);
    }

    /* ─── News Cards Grid ─── */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    @media (max-width: 1100px) { .news-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 700px)  { .news-grid { grid-template-columns: 1fr; } }

    .news-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(124, 58, 237, 0.12);
        border-color: var(--violet-200);
    }

    .news-card-image { width: 100%; height: 180px; object-fit: cover; display: block; background: var(--gray-100); }
    .news-card-image-placeholder { width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; font-size: 3rem; background: var(--gray-100); }

    .news-card-body { padding: 18px 20px 20px; flex: 1; display: flex; flex-direction: column; gap: 10px; }

    .news-card-meta { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; }
    .news-card-source { font-size: 0.74rem; font-weight: 800; color: var(--violet-600); text-transform: uppercase; letter-spacing: 0.5px; }
    .news-card-time { font-size: 0.72rem; color: var(--gray-400); font-weight: 500; }

    .news-card-title { font-size: 0.95rem; font-weight: 800; color: var(--gray-900); line-height: 1.4; flex: 1; }
    .news-card-desc { font-size: 0.82rem; color: var(--gray-500); line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

    .news-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 4px; gap: 8px; flex-wrap: wrap; }

    .news-read-btn {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.78rem; font-weight: 700; color: var(--violet-600);
        text-decoration: none; padding: 6px 12px; border-radius: 8px;
        background: #f3e8ff; transition: all 0.2s ease;
    }

    .news-read-btn:hover { background: var(--violet-600); color: white; }

    .sentiment-positive   { background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 99px; font-size: 0.72rem; font-weight: 700; }
    .sentiment-neutral    { background: #e0e7ff; color: #3730a3; padding: 3px 10px; border-radius: 99px; font-size: 0.72rem; font-weight: 700; }
    .sentiment-disruption { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 99px; font-size: 0.72rem; font-weight: 700; }

    /* Accent borders per category */
    .cat-accent-logistics { border-top: 3px solid #7c3aed; }
    .cat-accent-trade     { border-top: 3px solid #2563eb; }
    .cat-accent-shipping  { border-top: 3px solid #15803d; }
    .cat-accent-economy   { border-top: 3px solid #b45309; }

    /* Skeleton */
    .news-skeleton-card { background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); overflow: hidden; }
    .news-skeleton-img  { height: 180px; background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: skeleton-shimmer 1.5s infinite; }
    .news-skeleton-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 10px; }
    .news-skeleton-line { height: 12px; border-radius: 6px; background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: skeleton-shimmer 1.5s infinite; }

    @keyframes skeleton-shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Realtime badge */
    .badge-realtime {
        display: inline-flex; align-items: center; gap: 5px;
        background: #dcfce7; color: #15803d;
        padding: 3px 9px; border-radius: 99px;
        font-size: 0.7rem; font-weight: 800;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* Make whole card a link */
    .news-card-link-wrap { text-decoration: none; color: inherit; display: flex; flex-direction: column; flex: 1; }
    .news-card-link-wrap:hover .news-card-title { color: var(--violet-700); }

    /* Refresh button */
    .refresh-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 10px;
        background: rgba(255,255,255,0.15); color: #fff;
        font-size: 0.82rem; font-weight: 700;
        border: 1.5px solid rgba(255,255,255,0.2);
        cursor: pointer; transition: all 0.2s ease;
    }
    .refresh-btn:hover { background: rgba(255,255,255,0.25); }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <!-- PAGE HEADER -->
    <div class="page-header" style="margin-bottom: 20px;">
        <h1 class="page-title"><i class="ti ti-news" style="color: var(--violet-600);"></i> News Intelligence</h1>
        <p class="page-subtitle">Pantau berita global terkini seputar Logistics, Trade, Shipping, dan Economy — pilih negara untuk fokus pada berita spesifik.</p>
    </div>

    <!-- COUNTRY SELECTOR BANNER -->
    <div class="news-country-banner animate-fade-up">
        <div class="banner-inner">
            <div>
                <div class="banner-title"><i class="ti ti-world" style="color: var(--violet-400);"></i> News Intelligence</div>
                <div class="banner-subtitle">Berita realtime dari <strong style="color: #fff;">GNews API</strong> — Logistics, Trade, Shipping, Economy</div>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap;">
                    <div class="selected-country-pill" id="selectedCountryPill">🌐 Global — Semua Negara</div>
                    <span class="badge-realtime" id="apiSourceBadge"><span style="display:inline-block; width:7px; height:7px; background:#15803d; border-radius:50%;"></span> GNews API</span>
                </div>
                <div style="margin-top: 10px; font-size: 0.78rem; color: var(--violet-300);">🔄 Auto-refresh setiap 5 menit &nbsp;|&nbsp; <span id="lastRefreshTime"></span></div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: var(--violet-200); text-transform: uppercase; letter-spacing: 0.5px;">🔍 Pilih Negara</label>
                    <button class="refresh-btn" onclick="forceRefresh()" title="Refresh berita sekarang">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                </div>
                <select id="newsCountrySelect" class="country-select-news" onchange="onCountryChange()">
                    <option value="Global">🌐 Semua Negara (Global)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- STATS ROW -->
    <div class="news-stats-row animate-fade-up">
        <div class="news-stat-card">
            <div class="news-stat-icon violet">🚛</div>
            <div><div class="news-stat-label">Logistics</div><div class="news-stat-value" id="stat-logistics">—</div></div>
        </div>
        <div class="news-stat-card">
            <div class="news-stat-icon blue">🌐</div>
            <div><div class="news-stat-label">Trade</div><div class="news-stat-value" id="stat-trade">—</div></div>
        </div>
        <div class="news-stat-card">
            <div class="news-stat-icon green">🚢</div>
            <div><div class="news-stat-label">Shipping</div><div class="news-stat-value" id="stat-shipping">—</div></div>
        </div>
        <div class="news-stat-card">
            <div class="news-stat-icon amber">📈</div>
            <div><div class="news-stat-label">Economy</div><div class="news-stat-value" id="stat-economy">—</div></div>
        </div>
    </div>

    <!-- CATEGORY TABS -->
    <div class="news-tabs-bar animate-fade-up">
        <button class="news-tab-btn active" id="tab-logistics" onclick="switchTab('logistics')">🚛 Logistics <span class="tab-count" id="count-logistics">—</span></button>
        <button class="news-tab-btn" id="tab-trade" onclick="switchTab('trade')">🌐 Trade <span class="tab-count" id="count-trade">—</span></button>
        <button class="news-tab-btn" id="tab-shipping" onclick="switchTab('shipping')">🚢 Shipping <span class="tab-count" id="count-shipping">—</span></button>
        <button class="news-tab-btn" id="tab-economy" onclick="switchTab('economy')">📈 Economy <span class="tab-count" id="count-economy">—</span></button>
    </div>

    <!-- NEWS GRID PANEL -->
    <div id="newsGridPanel" class="news-grid animate-fade-up">
        @for ($i = 0; $i < 6; $i++)
        <div class="news-skeleton-card">
            <div class="news-skeleton-img"></div>
            <div class="news-skeleton-body">
                <div class="news-skeleton-line" style="width: 60%;"></div>
                <div class="news-skeleton-line" style="width: 90%;"></div>
                <div class="news-skeleton-line" style="width: 80%;"></div>
                <div class="news-skeleton-line" style="width: 50%;"></div>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
const CATEGORIES = ['logistics', 'trade', 'shipping', 'economy'];
const cache = {}; // key: `${country}__${cat}` => articles[]
let activeTab = 'logistics';
let activeCountry = 'Global';

document.addEventListener('DOMContentLoaded', function() {
    loadCountryList();
});

// ─── LOAD COUNTRY DROPDOWN ───
function loadCountryList() {
    fetch('/api/news-countries')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const sel = document.getElementById('newsCountrySelect');
                const opts = res.countries.map(c =>
                    `<option value="${escapeHtml(c.name)}">${c.name}</option>`
                ).join('');
                sel.innerHTML = `<option value="Global">🌐 Semua Negara (Global)</option>` + opts;
            }
        })
        .catch(err => console.error('❌ Country list error:', err))
        .finally(() => {
            // Load default Global news
            loadAllCategories('Global');
        });
}

// ─── ON COUNTRY CHANGE ───
function onCountryChange() {
    const country = document.getElementById('newsCountrySelect').value;
    activeCountry = country;

    const pill = document.getElementById('selectedCountryPill');
    pill.innerHTML = country === 'Global'
        ? '🌐 Global — Semua Negara'
        : `📍 ${country}`;

    // Reset stats
    CATEGORIES.forEach(c => {
        document.getElementById(`stat-${c}`).innerText = '...';
        document.getElementById(`count-${c}`).innerText = '...';
    });

    loadAllCategories(country);
}

// ─── LOAD ALL 4 CATEGORIES IN PARALLEL ───
function loadAllCategories(country) {
    // Show skeleton for active tab
    showSkeletons();

    Promise.all(CATEGORIES.map(cat => fetchCategory(country, cat)))
        .then(() => renderTab(activeTab));
}

// ─── FETCH ONE CATEGORY ───
async function fetchCategory(country, cat) {
    const key = `${country}__${cat}`;

    // Use cache if available
    if (cache[key]) {
        updateCatCount(cat, cache[key].length);
        return;
    }

    try {
        const encodedCountry = encodeURIComponent(country);
        const res = await fetch(`/api/news-by-category?category=${cat}&country=${encodedCountry}`);
        const data = await res.json();
        if (data.status === 'success') {
            cache[key] = data.articles;
            updateCatCount(cat, data.total);
        }
    } catch (err) {
        console.error(`❌ News fetch error [${country}][${cat}]:`, err);
        cache[key] = [];
        updateCatCount(cat, 0);
    }
}

function updateCatCount(cat, total) {
    document.getElementById(`count-${cat}`).innerText = total;
    document.getElementById(`stat-${cat}`).innerText = total + ' berita';
}

// ─── SWITCH TAB ───
function switchTab(cat) {
    activeTab = cat;
    CATEGORIES.forEach(c => {
        document.getElementById(`tab-${c}`).className = 'news-tab-btn' + (c === cat ? ' active' : '');
    });
    renderTab(cat);
}

// ─── RENDER GRID ───
function renderTab(cat) {
    const key = `${activeCountry}__${cat}`;
    const panel = document.getElementById('newsGridPanel');
    const articles = cache[key];

    if (!articles) {
        showSkeletons();
        return;
    }

    if (articles.length === 0) {
        panel.innerHTML = `
            <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--gray-400);">
                <div style="font-size:3rem; margin-bottom:12px;">📰</div>
                <div style="font-size:1.1rem; font-weight:700;">Tidak ada berita tersedia untuk negara ini</div>
                <div style="font-size:0.85rem; margin-top:6px;">Coba pilih kategori lain atau negara berbeda.</div>
            </div>`;
        return;
    }

    const accentClass = `cat-accent-${cat}`;

    panel.innerHTML = articles.map(a => {
        const title = a.title || '';
        let sentiment = 'Neutral', sentimentClass = 'sentiment-neutral', sentimentIcon = '🔵';
        if (/delay|strike|congestion|storm|blocked|risk|disruption|crisis|restrict|shortage/i.test(title)) {
            sentiment = 'Disruption'; sentimentClass = 'sentiment-disruption'; sentimentIcon = '🔴';
        } else if (/growth|expand|efficient|improves|launch|record|surge|profit|boost|rise|positive|invest/i.test(title)) {
            sentiment = 'Positive'; sentimentClass = 'sentiment-positive'; sentimentIcon = '🟢';
        }

        const timeAgo    = formatTimeAgo(a.publishedAt);
        const sourceName = (a.source && a.source.name) ? a.source.name : 'News Source';
        const url        = (a.url && a.url !== '#') ? a.url : null;
        const desc       = a.description || '';
        const img        = a.image || null;
        const isRealtime = a.realtime === true;

        const imgHtml = img
            ? `<img class="news-card-image" src="${img}" alt="" loading="lazy" onerror="this.outerHTML='<div class=\'news-card-image-placeholder\'>${getCategoryEmoji(cat)}</div>'">`
            : `<div class="news-card-image-placeholder">${getCategoryEmoji(cat)}</div>`;

        const via = a.via || 'fallback';
        const realtimeBadge = (isRealtime && via === 'gnews')
            ? `<span class="badge-realtime"><span style="display:inline-block;width:6px;height:6px;background:#15803d;border-radius:50%;"></span> GNews API</span>`
            : `<span style="background:#f1f5f9; color:#64748b; padding:3px 9px; border-radius:99px; font-size:0.7rem; font-weight:700;">📋 Sumber Terpercaya</span>`;

        const cardInner = `
            ${imgHtml}
            <div class="news-card-body">
                <div class="news-card-meta">
                    <span class="news-card-source">${escapeHtml(sourceName)}</span>
                    <div style="display:flex;align-items:center;gap:6px;">
                        ${realtimeBadge}
                        <span class="news-card-time">${timeAgo}</span>
                    </div>
                </div>
                <div class="news-card-title">${escapeHtml(title)}</div>
                ${desc ? `<div class="news-card-desc">${escapeHtml(desc)}</div>` : ''}
                <div class="news-card-footer">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span class="news-read-btn" style="pointer-events:none;"><i class="ti ti-external-link"></i> Baca Selengkapnya</span>
                    </div>
                    <span class="${sentimentClass}">${sentimentIcon} ${sentiment}</span>
                </div>
            </div>`;

        // Whole card is clickable if real URL available
        if (url) {
            return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="news-card ${accentClass}" style="text-decoration:none; cursor:pointer;">${cardInner}</a>`;
        } else {
            return `<div class="news-card ${accentClass}">${cardInner}</div>`;
        }
    }).join('');

    // Update banner badge based on source
    const hasGnews = articles.some(a => a.via === 'gnews');
    const badge = document.getElementById('apiSourceBadge');
    if (badge) {
        badge.innerHTML = hasGnews
            ? `<span style="display:inline-block;width:7px;height:7px;background:#15803d;border-radius:50%;"></span> GNews API`
            : `<span style="display:inline-block;width:7px;height:7px;background:#f59e0b;border-radius:50%;"></span> Sumber Terpercaya`;
        badge.style.background = hasGnews ? '#dcfce7' : '#fef3c7';
        badge.style.color      = hasGnews ? '#15803d' : '#b45309';
    }
}


function showSkeletons() {
    const panel = document.getElementById('newsGridPanel');
    panel.innerHTML = Array(6).fill(`
        <div class="news-skeleton-card">
            <div class="news-skeleton-img"></div>
            <div class="news-skeleton-body">
                <div class="news-skeleton-line" style="width:60%;"></div>
                <div class="news-skeleton-line" style="width:90%;"></div>
                <div class="news-skeleton-line" style="width:80%;"></div>
                <div class="news-skeleton-line" style="width:50%;"></div>
            </div>
        </div>`).join('');
}

// ─── HELPERS ───
function getCategoryEmoji(cat) {
    return { logistics: '🚛', trade: '🌐', shipping: '🚢', economy: '📈' }[cat] || '📰';
}

function formatTimeAgo(publishedAt) {
    if (!publishedAt) return '';
    try {
        const diff = Math.floor((Date.now() - new Date(publishedAt).getTime()) / 1000);
        if (diff < 60)   return 'Baru saja';
        if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    } catch(e) { return ''; }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ─── FORCE REFRESH (clears cache) ───
function forceRefresh() {
    CATEGORIES.forEach(cat => {
        const key = `${activeCountry}__${cat}`;
        delete cache[key];
    });
    document.getElementById('stat-logistics').innerText = '...';
    document.getElementById('stat-trade').innerText = '...';
    document.getElementById('stat-shipping').innerText = '...';
    document.getElementById('stat-economy').innerText = '...';
    loadAllCategories(activeCountry);
}

// ─── UPDATE LAST REFRESH TIME ───
function updateRefreshTime() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const el = document.getElementById('lastRefreshTime');
    if (el) el.innerText = `Terakhir diperbarui: ${h}:${m}`;
}

// ─── AUTO-REFRESH EVERY 5 MINUTES ───
setInterval(() => {
    forceRefresh();
    updateRefreshTime();
}, 5 * 60 * 1000);

updateRefreshTime();
</script>
@endsection