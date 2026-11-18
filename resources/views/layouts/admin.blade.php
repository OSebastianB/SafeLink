<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – SafeLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --sl-navy:#0b1f3a; --sl-cyan:#1ec8c8; --sl-bg:#f4f7fb; }
        body { font-family:"Plus Jakarta Sans",system-ui,sans-serif; background:var(--sl-bg); }
        .admin-sidebar {
            width: 260px; min-height: 100vh; background: linear-gradient(180deg,#0b1f3a,#123056);
            color: #fff; position: fixed; top:0; left:0; z-index: 1030;
            transition: transform .2s ease;
        }
        .admin-sidebar a { color: rgba(255,255,255,.8); text-decoration:none; display:flex; gap:.6rem; align-items:center; padding:.65rem 1rem; border-radius:.6rem; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: rgba(30,200,200,.15); color:#fff; }
        .admin-main { margin-left: 260px; min-height: 100vh; }
        .admin-topbar { background:#fff; border-bottom:1px solid #e8eef5; }
        .card-soft { border:0; border-radius:1rem; box-shadow:0 8px 24px rgba(11,31,58,.06); }
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<aside class="admin-sidebar p-3" id="adminSidebar">
    <div class="d-flex align-items-center gap-2 mb-4 px-2">
        <div class="rounded-3 bg-info text-dark fw-bold d-flex align-items-center justify-content-center" style="width:36px;height:36px;">S</div>
        <div>
            <div class="fw-bold">SafeLink</div>
            <small class="text-white-50">Admin Panel</small>
        </div>
    </div>
    <nav class="d-grid gap-1">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.criteria.index') }}" class="{{ request()->routeIs('admin.criteria.*') ? 'active' : '' }}"><i class="bi bi-list-check"></i> Manajemen Kriteria</a>
        <a href="{{ route('admin.weights.edit') }}" class="{{ request()->routeIs('admin.weights.*') ? 'active' : '' }}"><i class="bi bi-sliders"></i> Manajemen Bobot</a>
        <a href="{{ route('admin.threats.index') }}" class="{{ request()->routeIs('admin.threats.*') ? 'active' : '' }}"><i class="bi bi-shield-exclamation"></i> Reputasi Domain</a>
        <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><i class="bi bi-bookmark-star"></i> Brand Watchlist</a>
        <a href="{{ route('admin.scans.index') }}" class="{{ request()->routeIs('admin.scans.*') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Riwayat Pemeriksaan</a>
        <a href="{{ route('admin.evaluation.index') }}" class="{{ request()->routeIs('admin.evaluation.*') ? 'active' : '' }}"><i class="bi bi-clipboard-data"></i> Evaluasi SAW</a>
        <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan Risiko</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Manajemen Pengguna</a>
        <hr class="border-secondary">
        <a href="{{ route('home') }}"><i class="bi bi-house"></i> Beranda</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-link text-start text-white-50 text-decoration-none w-100 px-3"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </nav>
</aside>

<div class="admin-main">
    <div class="admin-topbar px-3 py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" type="button"><i class="bi bi-list"></i></button>
            <h1 class="h5 mb-0">@yield('page_title', 'Dashboard')</h1>
        </div>
        <div class="small text-muted">{{ auth()->user()->name }} (Admin)</div>
    </div>
    <div class="p-3 p-lg-4">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('adminSidebar').classList.toggle('show');
});
</script>
@stack('scripts')
</body>
</html>
