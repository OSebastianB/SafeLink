@props(['scan'])
@php
    $riskClass = match($scan->risk_category) {
        'low' => 'risk-low',
        'medium' => 'risk-medium',
        'high' => 'risk-high',
        default => '',
    };
@endphp

@if($scan->threat_match_status && in_array($scan->threat_match_status, ['malicious','phishing']))
    <div class="alert alert-danger">
        <strong>Peringatan ancaman terkonfirmasi:</strong> {{ $scan->threat_match_status }} (aturan prioritas di luar agregasi SAW).
    </div>
@endif
@if($scan->warning_message)
    <div class="alert alert-warning">{{ $scan->warning_message }}</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card card-soft p-4 h-100">
            <h2 class="h6 text-muted text-uppercase">Informasi URL</h2>
            <div class="mb-2"><strong>URL:</strong> <span class="text-break">{{ $scan->original_url }}</span></div>
            <div class="mb-2"><strong>Host/Domain:</strong> {{ $scan->domain }}</div>
            <div class="mb-2"><strong>Protokol:</strong> {{ strtoupper($scan->protocol) }}</div>
            <div><strong>Waktu:</strong> {{ optional($scan->scanned_at)->format('d M Y H:i:s') }}</div>
            @if($scan->user)
                <div class="mt-2"><strong>User:</strong> {{ $scan->user->email }}</div>
            @endif
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft p-4 h-100">
            <h2 class="h6 text-muted text-uppercase">Ringkasan</h2>
            <div class="display-6 fw-bold {{ $riskClass }}">{{ number_format($scan->risk_index, 2) }}</div>
            <div class="mb-2">Indeks Risiko</div>
            <span class="badge {{ $scan->riskBadgeClass() }} mb-3">{{ $scan->riskLabel() }}</span>
            <div class="small">Nilai preferensi SAW: <strong>{{ number_format($scan->security_score, 4) }}</strong></div>
            <div class="small">Cakupan data: <strong>{{ number_format($scan->data_coverage_percentage, 1) }}%</strong></div>
            <div class="small">Status reputasi: <strong>{{ $scan->threat_match_status ?: 'unknown' }}</strong></div>
        </div>
    </div>
</div>

<div class="card card-soft p-3 p-lg-4 mb-4">
    <h2 class="h5 fw-bold mb-3">Penilaian Kriteria</h2>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
            <tr>
                <th>Kode</th><th>Kriteria</th><th>Hasil</th><th>Skor</th><th>Bobot</th>
                <th>Normalisasi</th><th>Kontribusi</th><th>Sumber</th><th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($scan->criterionResults as $row)
                <tr>
                    <td>{{ $row->criterion_code_snapshot }}</td>
                    <td>{{ $row->criterion_name_snapshot }}</td>
                    <td class="small" style="max-width:280px;">{{ $row->evidence }}</td>
                    <td>{{ number_format($row->score, 2) }}</td>
                    <td>{{ number_format($row->criterion_weight_snapshot, 2) }}</td>
                    <td>{{ number_format($row->normalized_value, 4) }}</td>
                    <td>{{ number_format($row->weighted_contribution, 4) }}</td>
                    <td class="small">{{ $row->data_source }}</td>
                    <td>
                        @if($row->data_status === 'unavailable')
                            <span class="badge text-bg-secondary">Data tidak tersedia</span>
                        @else
                            <span class="badge text-bg-success">Tersedia</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card card-soft p-4 mb-4">
    <h2 class="h5 fw-bold">Penjelasan Hasil</h2>
    <p class="mb-0">{{ $scan->explanation }}</p>
</div>

<div class="alert alert-light border">
    <strong>Disclaimer:</strong> Hasil pemeriksaan merupakan estimasi risiko berdasarkan indikator yang tersedia. Hasil ini bukan jaminan mutlak bahwa suatu website aman atau berbahaya.
</div>
