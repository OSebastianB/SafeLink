@extends('layouts.app')

@section('title', 'Hasil Pemeriksaan – SafeLink')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Hasil Pemeriksaan</h1>
            <div class="text-muted small">{{ optional($scan->scanned_at)->format('d M Y H:i') }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scan.create') }}" class="btn btn-cyan">Scan URL Lain</a>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
            @endauth
        </div>
    </div>

    <x-scan-result :scan="$scan" />
</div>
@endsection
