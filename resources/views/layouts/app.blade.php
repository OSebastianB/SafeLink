<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SafeLink – Smart URL Risk Assessment')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sl-navy: #0b1f3a;
            --sl-navy-2: #123056;
            --sl-cyan: #1ec8c8;
            --sl-cyan-2: #0ea5a5;
            --sl-bg: #f4f7fb;
            --sl-text: #1a2332;
            --sl-muted: #5b6b7c;
        }
        body {
            font-family: "Plus Jakarta Sans", system-ui, sans-serif;
            color: var(--sl-text);
            background: var(--sl-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main, .page-content { flex: 1 0 auto; }

        .navbar-safelink {
            background: linear-gradient(120deg, var(--sl-navy), var(--sl-navy-2));
        }
        .brand-mark {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--sl-cyan), #3b82f6);
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 800;
        }
        .btn-cyan {
            background: var(--sl-cyan); border-color: var(--sl-cyan); color: #053333; font-weight: 700;
        }
        .btn-cyan:hover { background: var(--sl-cyan-2); border-color: var(--sl-cyan-2); color: #fff; }
        .hero-panel {
            background:
                radial-gradient(circle at 15% 20%, rgba(30,200,200,.25), transparent 40%),
                radial-gradient(circle at 85% 10%, rgba(59,130,246,.2), transparent 35%),
                linear-gradient(145deg, #0b1f3a 0%, #123056 55%, #0d2848 100%);
            color: #fff;
            border-radius: 0 0 2rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .hero-panel::after {
            content: "";
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }
        .card-soft {
            border: 0; border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(11,31,58,.06);
        }
        .section-title { font-weight: 800; letter-spacing: -.02em; }
        .text-muted-2 { color: var(--sl-muted) !important; }
        .risk-low { color: #198754; }
        .risk-medium { color: #d97706; }
        .risk-high { color: #dc3545; }
        footer.site-footer {
            background: var(--sl-navy); color: rgba(255,255,255,.75);
        }
        .url-input {
            border: 0; border-radius: 999px; padding: .9rem 1.25rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
        }
        .scan-btn {
            border-radius: 999px; padding: .9rem 1.5rem; font-weight: 700;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp .6s ease both; }
        .animate-delay-1 { animation-delay: .1s; }
        .animate-delay-2 { animation-delay: .2s; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-safelink sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-mark">S</span>
            <span class="fw-bold">SafeLink</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('scan.create') }}">Periksa URL</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-light">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-cyan btn-sm" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="page-content">
@if(session('success'))
    <div class="container mt-3"><div class="alert alert-success">{{ session('success') }}</div></div>
@endif
@if(session('error'))
    <div class="container mt-3"><div class="alert alert-danger">{{ session('error') }}</div></div>
@endif

@yield('content')
</div>


<footer class="site-footer mt-auto py-4">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <div class="small">&copy; {{ date('Y') }} SafeLink</div>
        <div class="small">Smart URL Risk Assessment System</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
