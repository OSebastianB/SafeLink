@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Administrator')

@section('content')
<div class="row g-3 mb-4">
    @foreach([
        ['Total Pengguna', $stats['users'], 'bi-people'],
        ['Total Scan', $stats['scans'], 'bi-search'],
        ['Risiko Rendah', $stats['low'], 'bi-shield-check'],
        ['Risiko Sedang', $stats['medium'], 'bi-shield'],
        ['Risiko Tinggi', $stats['high'], 'bi-shield-exclamation'],
    ] as [$label,$value,$icon])
        <div class="col-6 col-xl">
            <div class="card card-soft p-3">
                <div class="small text-muted">{{ $label }}</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fs-3 fw-bold">{{ $value }}</div>
                    <i class="bi {{ $icon }} text-primary fs-4"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card card-soft p-3">
            <h2 class="h6 fw-bold">Pemeriksaan 14 Hari Terakhir</h2>
            <canvas id="dailyChart" height="140"></canvas>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft p-3 h-100">
            <h2 class="h6 fw-bold">Distribusi Risiko</h2>
            <canvas id="riskChart" height="180"></canvas>
            <hr>
            <div class="small">
                <div><strong>Provider reputasi:</strong> {{ $providerStatus['name'] }} ({{ $providerStatus['available'] ? 'aktif' : 'nonaktif' }})</div>
                <div><strong>RDAP:</strong> {{ $providerStatus['rdap_enabled'] ? 'enabled' : 'disabled' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft p-3">
    <h2 class="h6 fw-bold mb-3">URL Terbaru Diperiksa</h2>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Waktu</th><th>URL</th><th>User</th><th>Risiko</th><th>Kategori</th><th></th></tr></thead>
            <tbody>
            @foreach($recent as $scan)
                <tr>
                    <td class="small">{{ optional($scan->scanned_at)->format('d/m/Y H:i') }}</td>
                    <td class="small text-break" style="max-width:260px;">{{ $scan->domain }}</td>
                    <td class="small">{{ $scan->user?->email ?? 'guest' }}</td>
                    <td>{{ number_format($scan->risk_index, 1) }}</td>
                    <td><span class="badge {{ $scan->riskBadgeClass() }}">{{ $scan->riskLabel() }}</span></td>
                    <td><a href="{{ route('admin.scans.show', $scan) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($daily->pluck('day')) !!},
        datasets: [{
            label: 'Scan',
            data: {!! json_encode($daily->pluck('total')) !!},
            borderColor: '#1ec8c8',
            backgroundColor: 'rgba(30,200,200,.2)',
            tension: .3,
            fill: true
        }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
new Chart(document.getElementById('riskChart'), {
    type: 'doughnut',
    data: {
        labels: ['Rendah','Sedang','Tinggi'],
        datasets: [{ data: [{{ $stats['low'] }},{{ $stats['medium'] }},{{ $stats['high'] }}], backgroundColor:['#198754','#ffc107','#dc3545'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
