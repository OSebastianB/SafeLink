@extends('layouts.app')

@section('title', 'Riwayat Pemeriksaan – SafeLink')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h3 fw-bold mb-0">Riwayat Pemeriksaan</h1>
        <a href="{{ route('user.history.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download"></i> Export CSV</a>
    </div>

    <form class="card card-soft p-3 mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari URL/domain"></div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach(['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi'] as $k=>$v)
                        <option value="{{ $k }}" @selected(request('category')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
            <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-cyan w-100">Filter</button></div>
        </div>
    </form>

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr><th>Tanggal</th><th>URL</th><th>SAW</th><th>Risiko</th><th>Kategori</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($scans as $scan)
                    <tr>
                        <td class="small">{{ optional($scan->scanned_at)->format('d/m/Y H:i') }}</td>
                        <td class="small text-break" style="max-width:320px;">{{ $scan->original_url }}</td>
                        <td>{{ number_format($scan->security_score, 4) }}</td>
                        <td>{{ number_format($scan->risk_index, 2) }}</td>
                        <td><span class="badge {{ $scan->riskBadgeClass() }}">{{ $scan->riskLabel() }}</span></td>
                        <td><a href="{{ route('scan.show', $scan) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Tidak ada data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $scans->links() }}
    </div>
</div>
@endsection
