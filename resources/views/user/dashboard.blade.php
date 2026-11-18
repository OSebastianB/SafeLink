@extends('layouts.app')

@section('title', 'Dashboard Pengguna – SafeLink')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-bold mb-0">Dashboard</h1>
            <div class="text-muted">Halo, {{ auth()->user()->name }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scan.create') }}" class="btn btn-cyan">Periksa URL</a>
            <a href="{{ route('user.history') }}" class="btn btn-outline-secondary">Riwayat</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Scan', $stats['total'], 'bi-search', 'text-primary'],
            ['Risiko Rendah', $stats['low'], 'bi-shield-check', 'text-success'],
            ['Risiko Sedang', $stats['medium'], 'bi-shield', 'text-warning'],
            ['Risiko Tinggi', $stats['high'], 'bi-shield-exclamation', 'text-danger'],
        ] as [$label,$value,$icon,$color])
            <div class="col-6 col-lg-3">
                <div class="card card-soft p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-muted">{{ $label }}</div>
                            <div class="fs-3 fw-bold">{{ $value }}</div>
                        </div>
                        <i class="bi {{ $icon }} {{ $color }} fs-3"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-soft p-3 h-100">
                <h2 class="h6 fw-bold">Distribusi Risiko</h2>
                <canvas id="riskChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-soft p-3 h-100">
                <h2 class="h6 fw-bold mb-3">Riwayat Terbaru</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>URL</th><th>Risiko</th><th>Kategori</th><th></th></tr></thead>
                        <tbody>
                        @forelse($recent as $scan)
                            <tr>
                                <td class="small text-break" style="max-width:240px;">{{ $scan->domain }}</td>
                                <td>{{ number_format($scan->risk_index, 1) }}</td>
                                <td><span class="badge {{ $scan->riskBadgeClass() }}">{{ $scan->riskLabel() }}</span></td>
                                <td><a href="{{ route('scan.show', $scan) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Belum ada pemeriksaan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('riskChart'), {
    type: 'doughnut',
    data: {
        labels: ['Rendah', 'Sedang', 'Tinggi'],
        datasets: [{
            data: [{{ $stats['low'] }}, {{ $stats['medium'] }}, {{ $stats['high'] }}],
            backgroundColor: ['#198754', '#ffc107', '#dc3545']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
