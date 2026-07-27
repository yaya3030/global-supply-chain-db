@extends('layouts.app')

@section('title', 'Admin Dashboard — Control Center')
@section('breadcrumb', 'Admin Dashboard')

@section('extra_head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Control Center Dashboard Layout */
    .cc-wrapper {
        display: flex;
        gap: 24px;
        min-height: calc(100vh - 120px);
        font-family: 'Inter', sans-serif;
    }

    /* Left Control Center Sidebar - Matching Primary App Shell Gradient */
    .cc-sidebar {
        width: 240px;
        flex-shrink: 0;
        background: linear-gradient(180deg, var(--violet-950) 0%, var(--violet-900) 100%);
        border-radius: var(--radius-lg);
        padding: 24px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(236, 72, 153, 0.15);
        position: relative;
        overflow: hidden;
    }

    /* Subtle ambient shimmer overlay */
    .cc-sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 180px;
        background: radial-gradient(ellipse at 30% 0%, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .cc-sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 800;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .cc-sidebar-brand-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--violet-500) 0%, var(--violet-600) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        box-shadow: var(--shadow-violet);
    }

    .cc-sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
        z-index: 1;
    }

    .cc-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
        font-size: 0.92rem;
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-base);
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }

    .cc-menu-item:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(2px);
    }

    .cc-menu-item.active {
        background: linear-gradient(135deg, var(--violet-600) 0%, var(--violet-700) 100%);
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(219, 39, 119, 0.35);
    }

    .cc-menu-item i {
        font-size: 1.25rem;
    }

    .cc-sidebar-footer {
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
    }

    .cc-btn-back {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--violet-200);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        transition: all var(--transition-base);
    }

    .cc-btn-back:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Main Content Area */
    .cc-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Section Header */
    .cc-header {
        margin-bottom: 4px;
    }

    .cc-header-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.5px;
    }

    .cc-header-subtitle {
        color: var(--gray-500);
        font-size: 0.95rem;
        margin-top: 4px;
    }

    /* 3 Summary Cards */
    .cc-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .cc-stat-card {
        border-radius: var(--radius-lg);
        padding: 24px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        transition: transform var(--transition-base), box-shadow var(--transition-base);
    }

    .cc-stat-card:hover {
        transform: translateY(-2px);
    }

    .cc-stat-card.purple {
        background: linear-gradient(135deg, var(--violet-600) 0%, var(--violet-700) 100%);
        box-shadow: var(--shadow-violet);
    }

    .cc-stat-card.blue {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25);
    }

    .cc-stat-card.amber {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
    }

    .cc-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .cc-stat-label {
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        opacity: 0.92;
    }

    .cc-stat-val {
        font-size: 2.4rem;
        font-weight: 800;
        margin-top: 6px;
        line-height: 1;
        letter-spacing: -1px;
    }

    .cc-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #ffffff;
    }

    .cc-stat-subtext {
        font-size: 0.82rem;
        opacity: 0.88;
        margin-top: 14px;
        font-weight: 500;
    }

    /* Main Grid Layout */
    .cc-main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .cc-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: 24px;
        box-shadow: var(--shadow-sm);
    }

    .cc-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .cc-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cc-card-title i {
        color: var(--violet-600);
        font-size: 1.25rem;
    }

    .cc-link-btn {
        color: var(--violet-600);
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: color var(--transition-fast);
    }

    .cc-link-btn:hover {
        color: var(--violet-800);
        text-decoration: underline;
    }

    /* Table Styling */
    .cc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cc-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--gray-600);
        border-bottom: 2px solid var(--gray-100);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cc-table td {
        padding: 14px;
        font-size: 0.88rem;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .cc-user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cc-avatar {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, var(--violet-400) 0%, var(--violet-600) 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-xs);
    }

    .cc-badge {
        padding: 4px 12px;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }

    .cc-badge.user {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
    }

    .cc-badge.admin {
        background: var(--violet-50);
        color: var(--violet-700);
        border: 1px solid var(--violet-200);
    }

    /* Quick Action Buttons */
    .cc-actions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cc-action-btn {
        width: 100%;
        padding: 14px 18px;
        border-radius: var(--radius-md);
        background: #ffffff;
        font-weight: 600;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all var(--transition-base);
        text-align: left;
    }

    .cc-action-btn.violet {
        border: 1.5px solid var(--violet-200);
        color: var(--violet-700);
        background: var(--violet-50);
    }

    .cc-action-btn.violet:hover {
        background: var(--violet-100);
        border-color: var(--violet-400);
        transform: translateY(-1px);
    }

    .cc-action-btn.green {
        border: 1.5px solid #bbf7d0;
        color: #15803d;
        background: #f0fdf4;
    }

    .cc-action-btn.green:hover {
        background: #dcfce7;
        border-color: #22c55e;
        transform: translateY(-1px);
    }

    .cc-action-btn.amber {
        border: 1.5px solid #fef08a;
        color: #b45309;
        background: #fefce8;
    }

    .cc-action-btn.amber:hover {
        background: #fef9c3;
        border-color: #eab308;
        transform: translateY(-1px);
    }

    .cc-action-btn i {
        font-size: 1.25rem;
    }

    /* Management Tab Panels */
    .cc-panel {
        display: none;
    }

    .cc-panel.active {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .cc-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        background: #ffffff;
        padding: 16px 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-xs);
    }

    .cc-search-input {
        padding: 10px 16px;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.9rem;
        width: 300px;
        outline: none;
        transition: all var(--transition-fast);
    }

    .cc-search-input:focus {
        border-color: var(--violet-500);
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
    }

    .cc-btn-primary {
        background: linear-gradient(135deg, var(--violet-600) 0%, var(--violet-700) 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 0.88rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-violet);
        transition: all var(--transition-base);
    }

    .cc-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-violet-lg);
    }

    .cc-btn-danger {
        background: var(--danger);
        color: #ffffff;
        border: none;
        padding: 6px 14px;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background var(--transition-fast);
    }

    .cc-btn-danger:hover {
        background: #dc2626;
    }

    .cc-btn-edit {
        background: var(--info);
        color: #ffffff;
        border: none;
        padding: 6px 14px;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        margin-right: 6px;
        transition: background var(--transition-fast);
    }

    .cc-btn-edit:hover {
        background: #2563eb;
    }

    /* Custom Modal Dialog */
    .cc-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .cc-modal-backdrop.show {
        display: flex;
    }

    .cc-modal {
        background: #ffffff;
        border-radius: var(--radius-xl);
        width: 100%;
        max-width: 520px;
        padding: 28px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--gray-200);
    }

    .cc-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .cc-modal-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--gray-900);
    }

    .cc-modal-close {
        background: transparent;
        border: none;
        font-size: 1.4rem;
        color: var(--gray-400);
        cursor: pointer;
        transition: color var(--transition-fast);
    }

    .cc-modal-close:hover {
        color: var(--gray-700);
    }

    .cc-form-group {
        margin-bottom: 16px;
    }

    .cc-form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--gray-700);
        margin-bottom: 6px;
    }

    .cc-form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 0.9rem;
        outline: none;
        transition: all var(--transition-fast);
    }

    .cc-form-control:focus {
        border-color: var(--violet-500);
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
    }

    /* Notification Alert */
    .cc-alert {
        padding: 14px 18px;
        border-radius: var(--radius-md);
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 16px;
        display: none;
    }

    .cc-alert-success {
        background: var(--success-bg);
        color: var(--success);
        border: 1px solid #a7f3d0;
    }

    .cc-alert-error {
        background: var(--danger-bg);
        color: var(--danger);
        border: 1px solid #fecaca;
    }
</style>
@endsection

@section('content')
<div class="cc-wrapper">
    <!-- LEFT SIDEBAR CONTROL CENTER -->
    <aside class="cc-sidebar">
        <div>
            <div class="cc-sidebar-brand">
                <div class="cc-sidebar-brand-icon">
                    <i class="ti ti-lock"></i>
                </div>
                <div>
                    <div style="font-size: 15px; font-weight: 800; color: white;">Control Center</div>
                    <div style="font-size: 10px; color: var(--violet-300); font-weight: 600; text-transform: uppercase;">Admin Portal</div>
                </div>
            </div>
            <nav class="cc-sidebar-menu">
                <button onclick="switchTab('dashboard')" id="tab-btn-dashboard" class="cc-menu-item active">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </button>
                <button onclick="switchTab('users')" id="tab-btn-users" class="cc-menu-item">
                    <i class="ti ti-users"></i> Kelola User
                </button>
                <button onclick="switchTab('ports')" id="tab-btn-ports" class="cc-menu-item">
                    <i class="ti ti-anchor"></i> Dataset Pelabuhan
                </button>
                <button onclick="switchTab('articles')" id="tab-btn-articles" class="cc-menu-item">
                    <i class="ti ti-news"></i> Artikel Analisis
                </button>
            </nav>
        </div>
        <div class="cc-sidebar-footer">
            <a href="{{ route('dashboard') }}" class="cc-btn-back">
                <i class="ti ti-arrow-left"></i> Kembali ke Utama
            </a>
        </div>
    </aside>

    <!-- RIGHT MAIN CONTENT -->
    <main class="cc-content">
        <!-- GLOBAL NOTIFICATION ALERT -->
        <div id="globalAlert" class="cc-alert"></div>

        <!-- TAB 1: DASHBOARD OVERVIEW -->
        <div id="panel-dashboard" class="cc-panel active">
            <div class="cc-header">
                <h1 class="cc-header-title">
                    <i class="ti ti-adjustments-alt" style="color: var(--violet-600);"></i> Control Center Dashboard
                </h1>
                <p class="cc-header-subtitle">Overview status dan manajemen operasional sistem Supply Chain Risk Platform.</p>
            </div>

            <!-- 3 STATS CARDS -->
            <div class="cc-stats-grid">
                <div class="cc-stat-card purple">
                    <div class="cc-stat-top">
                        <div>
                            <div class="cc-stat-label">Total Users</div>
                            <div class="cc-stat-val" id="stat-total-users">{{ $totalUsers }}</div>
                        </div>
                        <div class="cc-stat-icon">
                            <i class="ti ti-users-group"></i>
                        </div>
                    </div>
                    <div class="cc-stat-subtext">Akun pengguna terdaftar dalam sistem</div>
                </div>

                <div class="cc-stat-card blue">
                    <div class="cc-stat-top">
                        <div>
                            <div class="cc-stat-label">Total Ports</div>
                            <div class="cc-stat-val" id="stat-total-ports">{{ $totalPorts }}</div>
                        </div>
                        <div class="cc-stat-icon">
                            <i class="ti ti-ship"></i>
                        </div>
                    </div>
                    <div class="cc-stat-subtext">Pelabuhan pengiriman logistik terpantau</div>
                </div>

                <div class="cc-stat-card amber">
                    <div class="cc-stat-top">
                        <div>
                            <div class="cc-stat-label">Total Articles</div>
                            <div class="cc-stat-val" id="stat-total-articles">{{ $totalArticles }}</div>
                        </div>
                        <div class="cc-stat-icon">
                            <i class="ti ti-news"></i>
                        </div>
                    </div>
                    <div class="cc-stat-subtext">Artikel berita yang dipublikasikan</div>
                </div>
            </div>

            <!-- LOWER GRID -->
            <div class="cc-main-grid">
                <!-- RECENT USERS -->
                <div class="cc-card">
                    <div class="cc-card-header">
                        <span class="cc-card-title">
                            <i class="ti ti-user-plus"></i> Pendaftaran Pengguna Baru
                        </span>
                        <button onclick="switchTab('users')" class="cc-link-btn">Kelola Pengguna</button>
                    </div>
                    <table class="cc-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                            </tr>
                        </thead>
                        <tbody id="overview-recent-users">
                            @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="cc-user-cell">
                                        <div class="cc-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td style="color: var(--violet-600); font-family: monospace;">{{ $user->email }}</td>
                                <td>
                                    <span class="cc-badge {{ strtolower($user->role) === 'admin' ? 'admin' : 'user' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td style="color: var(--gray-500);">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--gray-400); padding: 20px;">Belum ada pengguna.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- QUICK ACTIONS -->
                <div class="cc-card">
                    <div class="cc-card-header">
                        <span class="cc-card-title">
                            <i class="ti ti-subtask"></i> Tindakan Cepat
                        </span>
                    </div>
                    <div class="cc-actions-list">
                        <button onclick="openUserModal()" class="cc-action-btn violet">
                            <i class="ti ti-user-plus"></i> Tambah Pengguna Baru
                        </button>
                        <button onclick="openPortModal()" class="cc-action-btn green">
                            <i class="ti ti-circle-plus"></i> Tambah Pelabuhan Baru
                        </button>
                        <button onclick="openArticleModal()" class="cc-action-btn amber">
                            <i class="ti ti-edit"></i> Tulis Artikel Berita Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: USERS MANAGEMENT -->
        <div id="panel-users" class="cc-panel">
            <div class="cc-header">
                <h1 class="cc-header-title"><i class="ti ti-users" style="color: var(--violet-600);"></i> Kelola Pengguna</h1>
                <p class="cc-header-subtitle">Manajemen daftar pengguna terdaftar, perbarui peran, atau tambah pengguna baru.</p>
            </div>
            <div class="cc-toolbar">
                <input type="text" id="search-users" class="cc-search-input" placeholder="Cari nama atau email..." onkeyup="filterUsersTable()">
                <button onclick="openUserModal()" class="cc-btn-primary">
                    <i class="ti ti-plus"></i> Tambah Pengguna Baru
                </button>
            </div>
            <div class="cc-card">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-users-body">
                        <!-- Loaded dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: PORTS MANAGEMENT -->
        <div id="panel-ports" class="cc-panel">
            <div class="cc-header">
                <h1 class="cc-header-title"><i class="ti ti-anchor" style="color: var(--violet-600);"></i> Kelola Dataset Pelabuhan</h1>
                <p class="cc-header-subtitle">Manajemen titik pelabuhan logistik internasional dalam sistem.</p>
            </div>
            <div class="cc-toolbar">
                <input type="text" id="search-ports" class="cc-search-input" placeholder="Cari nama pelabuhan atau negara..." onkeyup="filterPortsTable()">
                <button onclick="openPortModal()" class="cc-btn-primary">
                    <i class="ti ti-plus"></i> Tambah Pelabuhan Baru
                </button>
            </div>
            <div class="cc-card">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Pelabuhan</th>
                            <th>Kode</th>
                            <th>Negara</th>
                            <th>Koordinat (Lat, Long)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-ports-body">
                        <!-- Loaded dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: ARTICLES MANAGEMENT -->
        <div id="panel-articles" class="cc-panel">
            <div class="cc-header">
                <h1 class="cc-header-title"><i class="ti ti-news" style="color: var(--violet-600);"></i> Kelola Artikel Analisis</h1>
                <p class="cc-header-subtitle">Manajemen artikel berita & publikasi analisis intelijen rantai pasok.</p>
            </div>
            <div class="cc-toolbar">
                <input type="text" id="search-articles" class="cc-search-input" placeholder="Cari judul artikel..." onkeyup="filterArticlesTable()">
                <button onclick="openArticleModal()" class="cc-btn-primary">
                    <i class="ti ti-plus"></i> Tulis Artikel Baru
                </button>
            </div>
            <div class="cc-card">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul Artikel</th>
                            <th>Slug</th>
                            <th>Penulis</th>
                            <th>Tanggal Publikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-articles-body">
                        <!-- Loaded dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- MODAL USER -->
<div id="userModal" class="cc-modal-backdrop">
    <div class="cc-modal">
        <div class="cc-modal-header">
            <h3 class="cc-modal-title" id="userModalTitle">Tambah Pengguna Baru</h3>
            <button class="cc-modal-close" onclick="closeUserModal()">&times;</button>
        </div>
        <form id="userForm" onsubmit="saveUser(event)">
            <input type="hidden" id="user-id">
            <div class="cc-form-group">
                <label class="cc-form-label">Nama Lengkap</label>
                <input type="text" id="user-name" class="cc-form-control" required placeholder="Contoh: Budi Santoso">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Alamat Email</label>
                <input type="email" id="user-email" class="cc-form-control" required placeholder="budi@example.com">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Role / Peran</label>
                <select id="user-role" class="cc-form-control" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label" id="user-password-label">Password</label>
                <input type="password" id="user-password" class="cc-form-control" placeholder="Minimal 6 karakter">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="cc-action-btn" onclick="closeUserModal()" style="width: auto; border: 1px solid var(--gray-300);">Batal</button>
                <button type="submit" class="cc-btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PORT -->
<div id="portModal" class="cc-modal-backdrop">
    <div class="cc-modal">
        <div class="cc-modal-header">
            <h3 class="cc-modal-title" id="portModalTitle">Tambah Pelabuhan Baru</h3>
            <button class="cc-modal-close" onclick="closePortModal()">&times;</button>
        </div>
        <form id="portForm" onsubmit="savePort(event)">
            <input type="hidden" id="port-id">
            <div class="cc-form-group">
                <label class="cc-form-label">Nama Pelabuhan</label>
                <input type="text" id="port-name" class="cc-form-control" required placeholder="Contoh: Port of Tanjung Priok">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Kode Pelabuhan (Opsional)</label>
                <input type="text" id="port-code" class="cc-form-control" placeholder="Contoh: IDTPP">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Negara</label>
                <select id="port-country-id" class="cc-form-control" required>
                    <option value="">Pilih Negara...</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="cc-form-group">
                    <label class="cc-form-label">Latitude</label>
                    <input type="number" step="any" id="port-lat" class="cc-form-control" required placeholder="-6.1000">
                </div>
                <div class="cc-form-group">
                    <label class="cc-form-label">Longitude</label>
                    <input type="number" step="any" id="port-long" class="cc-form-control" required placeholder="106.8800">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="cc-action-btn" onclick="closePortModal()" style="width: auto; border: 1px solid var(--gray-300);">Batal</button>
                <button type="submit" class="cc-btn-primary">Simpan Pelabuhan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ARTICLE -->
<div id="articleModal" class="cc-modal-backdrop">
    <div class="cc-modal" style="max-width: 650px;">
        <div class="cc-modal-header">
            <h3 class="cc-modal-title" id="articleModalTitle">Tulis Artikel Analisis Baru</h3>
            <button class="cc-modal-close" onclick="closeArticleModal()">&times;</button>
        </div>
        <form id="articleForm" onsubmit="saveArticle(event)">
            <input type="hidden" id="article-id">
            <div class="cc-form-group">
                <label class="cc-form-label">Judul Artikel</label>
                <input type="text" id="article-title" class="cc-form-control" required placeholder="Contoh: Analisis Keterlambatan Rantai Pasok Asia 2026">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Slug URL (Opsional)</label>
                <input type="text" id="article-slug" class="cc-form-control" placeholder="analisis-keterlambatan-rantai-pasok">
            </div>
            <div class="cc-form-group">
                <label class="cc-form-label">Konten Artikel</label>
                <textarea id="article-content" class="cc-form-control" rows="6" required placeholder="Tuliskan analisis berita di sini..."></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="cc-action-btn" onclick="closeArticleModal()" style="width: auto; border: 1px solid var(--gray-300);">Batal</button>
                <button type="submit" class="cc-btn-primary">Terbitkan Artikel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let cachedUsers = [];
    let cachedPorts = [];
    let cachedArticles = [];
    let cachedCountries = [];

    // Switch Tabs inside Control Center
    function switchTab(tabName) {
        document.querySelectorAll('.cc-menu-item').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.cc-panel').forEach(panel => panel.classList.remove('active'));

        document.getElementById(`tab-btn-${tabName}`).classList.add('active');
        document.getElementById(`panel-${tabName}`).classList.add('active');

        if (tabName === 'users') loadUsers();
        if (tabName === 'ports') loadPorts();
        if (tabName === 'articles') loadArticles();
        if (tabName === 'dashboard') loadOverview();
    }

    // Show Global Alert
    function showAlert(msg, isError = false) {
        const el = document.getElementById('globalAlert');
        el.className = `cc-alert ${isError ? 'cc-alert-error' : 'cc-alert-success'}`;
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

    // Load Overview Stats
    function loadOverview() {
        fetch('/admin-api/overview')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    document.getElementById('stat-total-users').textContent = res.data.total_users;
                    document.getElementById('stat-total-ports').textContent = res.data.total_ports;
                    document.getElementById('stat-total-articles').textContent = res.data.total_articles;

                    const tbody = document.getElementById('overview-recent-users');
                    if (res.data.recent_users.length > 0) {
                        tbody.innerHTML = res.data.recent_users.map(u => `
                            <tr>
                                <td>
                                    <div class="cc-user-cell">
                                        <div class="cc-avatar">${u.name.substring(0, 2).toUpperCase()}</div>
                                        <strong>${u.name}</strong>
                                    </div>
                                </td>
                                <td style="color: var(--violet-600); font-family: monospace;">${u.email}</td>
                                <td>
                                    <span class="cc-badge ${u.role.toLowerCase() === 'admin' ? 'admin' : 'user'}">
                                        ${u.role.charAt(0).toUpperCase() + u.role.slice(1)}
                                    </span>
                                </td>
                                <td style="color: var(--gray-500);">${u.created_at}</td>
                            </tr>
                        `).join('');
                    }
                }
            });
    }

    // Load Users
    function loadUsers() {
        fetch('/admin-api/users')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    cachedUsers = res.data;
                    renderUsersTable(cachedUsers);
                }
            });
    }

    function renderUsersTable(users) {
        const tbody = document.getElementById('table-users-body');
        if (users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--gray-400);">Tidak ada pengguna ditemukan.</td></tr>`;
            return;
        }
        tbody.innerHTML = users.map((u, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <div class="cc-user-cell">
                        <div class="cc-avatar">${u.name.substring(0, 2).toUpperCase()}</div>
                        <strong>${u.name}</strong>
                    </div>
                </td>
                <td style="color: var(--violet-600); font-family: monospace;">${u.email}</td>
                <td>
                    <span class="cc-badge ${u.role.toLowerCase() === 'admin' ? 'admin' : 'user'}">
                        ${u.role.charAt(0).toUpperCase() + u.role.slice(1)}
                    </span>
                </td>
                <td style="color: var(--gray-500);">${u.created_at}</td>
                <td>
                    <button onclick="editUser(${u.id})" class="cc-btn-edit"><i class="ti ti-edit"></i> Edit</button>
                    <button onclick="deleteUser(${u.id})" class="cc-btn-danger"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        `).join('');
    }

    function filterUsersTable() {
        const q = document.getElementById('search-users').value.toLowerCase();
        const filtered = cachedUsers.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
        renderUsersTable(filtered);
    }

    // Open/Close User Modal
    function openUserModal(user = null) {
        document.getElementById('userForm').reset();
        if (user) {
            document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
            document.getElementById('user-id').value = user.id;
            document.getElementById('user-name').value = user.name;
            document.getElementById('user-email').value = user.email;
            document.getElementById('user-role').value = user.role;
            document.getElementById('user-password').removeAttribute('required');
            document.getElementById('user-password-label').textContent = 'Password (Kosongkan jika tidak diubah)';
        } else {
            document.getElementById('userModalTitle').textContent = 'Tambah Pengguna Baru';
            document.getElementById('user-id').value = '';
            document.getElementById('user-password').setAttribute('required', 'required');
            document.getElementById('user-password-label').textContent = 'Password';
        }
        document.getElementById('userModal').classList.add('show');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.remove('show');
    }

    function editUser(id) {
        const user = cachedUsers.find(u => u.id === id);
        if (user) openUserModal(user);
    }

    function saveUser(e) {
        e.preventDefault();
        const id = document.getElementById('user-id').value;
        const data = {
            name: document.getElementById('user-name').value,
            email: document.getElementById('user-email').value,
            role: document.getElementById('user-role').value,
            password: document.getElementById('user-password').value,
        };

        const url = id ? `/admin-api/users/${id}` : '/admin-api/users';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                closeUserModal();
                loadUsers();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menyimpan pengguna.', true);
            }
        });
    }

    function deleteUser(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) return;
        fetch(`/admin-api/users/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                loadUsers();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menghapus pengguna.', true);
            }
        });
    }

    // Load Countries for Port Form
    function loadCountries(selectedId = null) {
        fetch('/admin-api/countries')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    cachedCountries = res.data;
                    const select = document.getElementById('port-country-id');
                    select.innerHTML = '<option value="">Pilih Negara...</option>' + 
                        cachedCountries.map(c => `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.name} (${c.iso2})</option>`).join('');
                }
            });
    }

    // Load Ports
    function loadPorts() {
        fetch('/admin-api/ports')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    cachedPorts = res.data;
                    renderPortsTable(cachedPorts);
                }
            });
    }

    function renderPortsTable(ports) {
        const tbody = document.getElementById('table-ports-body');
        if (ports.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--gray-400);">Tidak ada pelabuhan ditemukan.</td></tr>`;
            return;
        }
        tbody.innerHTML = ports.map((p, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${p.port_name}</strong></td>
                <td><code style="background:var(--gray-100); color:var(--violet-700); padding:2px 6px; border-radius:4px;">${p.port_code || '-'}</code></td>
                <td>${p.country_name}</td>
                <td style="color:var(--gray-500); font-family:monospace;">${p.latitude}, ${p.longitude}</td>
                <td>
                    <button onclick="editPort(${p.id})" class="cc-btn-edit"><i class="ti ti-edit"></i> Edit</button>
                    <button onclick="deletePort(${p.id})" class="cc-btn-danger"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        `).join('');
    }

    function filterPortsTable() {
        const q = document.getElementById('search-ports').value.toLowerCase();
        const filtered = cachedPorts.filter(p => p.port_name.toLowerCase().includes(q) || (p.country_name && p.country_name.toLowerCase().includes(q)));
        renderPortsTable(filtered);
    }

    function openPortModal(port = null) {
        document.getElementById('portForm').reset();
        loadCountries(port ? port.country_id : null);
        if (port) {
            document.getElementById('portModalTitle').textContent = 'Edit Pelabuhan';
            document.getElementById('port-id').value = port.id;
            document.getElementById('port-name').value = port.port_name;
            document.getElementById('port-code').value = port.port_code || '';
            document.getElementById('port-lat').value = port.latitude;
            document.getElementById('port-long').value = port.longitude;
        } else {
            document.getElementById('portModalTitle').textContent = 'Tambah Pelabuhan Baru';
            document.getElementById('port-id').value = '';
        }
        document.getElementById('portModal').classList.add('show');
    }

    function closePortModal() {
        document.getElementById('portModal').classList.remove('show');
    }

    function editPort(id) {
        const port = cachedPorts.find(p => p.id === id);
        if (port) openPortModal(port);
    }

    function savePort(e) {
        e.preventDefault();
        const id = document.getElementById('port-id').value;
        const data = {
            country_id: document.getElementById('port-country-id').value,
            port_name: document.getElementById('port-name').value,
            port_code: document.getElementById('port-code').value,
            latitude: document.getElementById('port-lat').value,
            longitude: document.getElementById('port-long').value,
        };

        const url = id ? `/admin-api/ports/${id}` : '/admin-api/ports';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                closePortModal();
                loadPorts();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menyimpan pelabuhan.', true);
            }
        });
    }

    function deletePort(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus pelabuhan ini?')) return;
        fetch(`/admin-api/ports/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                loadPorts();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menghapus pelabuhan.', true);
            }
        });
    }

    // Load Articles
    function loadArticles() {
        fetch('/admin-api/articles')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    cachedArticles = res.data;
                    renderArticlesTable(cachedArticles);
                }
            });
    }

    function renderArticlesTable(articles) {
        const tbody = document.getElementById('table-articles-body');
        if (articles.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--gray-400);">Tidak ada artikel ditemukan.</td></tr>`;
            return;
        }
        tbody.innerHTML = articles.map((a, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${a.title}</strong></td>
                <td><code style="background:var(--gray-100); color:var(--violet-700); padding:2px 6px; border-radius:4px;">${a.slug}</code></td>
                <td>${a.author_name}</td>
                <td style="color:var(--gray-500);">${a.created_at}</td>
                <td>
                    <button onclick="editArticle(${a.id})" class="cc-btn-edit"><i class="ti ti-edit"></i> Edit</button>
                    <button onclick="deleteArticle(${a.id})" class="cc-btn-danger"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        `).join('');
    }

    function filterArticlesTable() {
        const q = document.getElementById('search-articles').value.toLowerCase();
        const filtered = cachedArticles.filter(a => a.title.toLowerCase().includes(q) || a.slug.toLowerCase().includes(q));
        renderArticlesTable(filtered);
    }

    function openArticleModal(article = null) {
        document.getElementById('articleForm').reset();
        if (article) {
            document.getElementById('articleModalTitle').textContent = 'Edit Artikel Analisis';
            document.getElementById('article-id').value = article.id;
            document.getElementById('article-title').value = article.title;
            document.getElementById('article-slug').value = article.slug;
            document.getElementById('article-content').value = article.content;
        } else {
            document.getElementById('articleModalTitle').textContent = 'Tulis Artikel Analisis Baru';
            document.getElementById('article-id').value = '';
        }
        document.getElementById('articleModal').classList.add('show');
    }

    function closeArticleModal() {
        document.getElementById('articleModal').classList.remove('show');
    }

    function editArticle(id) {
        const article = cachedArticles.find(a => a.id === id);
        if (article) openArticleModal(article);
    }

    function saveArticle(e) {
        e.preventDefault();
        const id = document.getElementById('article-id').value;
        const data = {
            title: document.getElementById('article-title').value,
            slug: document.getElementById('article-slug').value,
            content: document.getElementById('article-content').value,
        };

        const url = id ? `/admin-api/articles/${id}` : '/admin-api/articles';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                closeArticleModal();
                loadArticles();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menyimpan artikel.', true);
            }
        });
    }

    function deleteArticle(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus artikel ini?')) return;
        fetch(`/admin-api/articles/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showAlert(res.message);
                loadArticles();
                loadOverview();
            } else {
                showAlert(res.message || 'Gagal menghapus artikel.', true);
            }
        });
    }

    // Real-time polling
    let activeInterval = null;

    function startRealTimePolling(tabName) {
        if (activeInterval) clearInterval(activeInterval);
        
        activeInterval = setInterval(() => {
            if (tabName === 'users') loadUsers();
            if (tabName === 'ports') loadPorts();
            if (tabName === 'articles') loadArticles();
            if (tabName === 'dashboard') loadOverview();
        }, 5000); // Polling every 5 seconds for real-time feel
    }

    // Modified switchTab to include polling
    const originalSwitchTab = switchTab;
    switchTab = function(tabName) {
        originalSwitchTab(tabName);
        startRealTimePolling(tabName);
    };

    document.addEventListener('DOMContentLoaded', function() {
        startRealTimePolling('dashboard');
    });
</script>
@endsection