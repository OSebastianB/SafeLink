@extends('layouts.admin')
@section('title', 'Riwayat Scan')
@section('page_title', 'Riwayat Pemeriksaan')

@section('content')
<form class="card card-soft p-3 mb-3" method="GET">
    <div class="row g-2">
        <div class="col-md-6"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari URL/domain"></div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Semua</option>
                @foreach(['low','medium','high'] as $c)
                    <option value="{{ $c }}" @selected(request('category')===$c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-cyan w-100">Filter</button></div>
    </div>
</form>
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Waktu</th><th>URL</th><th>User</th><th>SAW</th><th>Risiko</th><th>Kategori</th><th></th></tr></thead>
            <tbody>
            @foreach($scans as $scan)
                <tr>
                    <td class="small">{{ optional($scan->scanned_at)->format('d/m/Y H:i') }}</td>
                    <td class="small text-break" style="max-width:280px;">{{ $scan->original_url }}</td>
                    <td class="small">{{ $scan->user?->email ?? 'guest' }}</td>
                    <td>{{ number_format($scan->security_score, 4) }}</td>
                    <td>{{ number_format($scan->risk_index, 2) }}</td>
                    <td><span class="badge {{ $scan->riskBadgeClass() }}">{{ $scan->riskLabel() }}</span></td>
                    <td><a href="{{ route('admin.scans.show', $scan) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $scans->links() }}
</div>
@endsection
