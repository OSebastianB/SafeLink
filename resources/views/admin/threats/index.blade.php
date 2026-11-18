@extends('layouts.admin')
@section('title', 'Reputasi Domain')
@section('page_title', 'Data Reputasi Domain')

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari domain">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach(['malicious','phishing','suspicious','no_match','unknown'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
    </form>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.threats.create') }}" class="btn btn-cyan btn-sm">Tambah</a>
    </div>
</div>

<div class="card card-soft p-3 mb-3">
    <form method="POST" action="{{ route('admin.threats.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-8">
            <label class="form-label small">Import CSV (domain,status,threat_type,source,verified_at,expires_at,notes)</label>
            <input type="file" name="csv" class="form-control" accept=".csv,text/csv" required>
        </div>
        <div class="col-md-4"><button class="btn btn-outline-primary w-100">Import CSV</button></div>
    </form>
</div>

<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Domain</th><th>Status</th><th>Tipe</th><th>Sumber</th><th>Verified</th><th>Expires</th><th></th></tr></thead>
            <tbody>
            @foreach($records as $r)
                <tr>
                    <td>{{ $r->domain }} @if($r->is_synthetic)<span class="badge text-bg-secondary">sintetis</span>@endif</td>
                    <td>{{ $r->status }}</td>
                    <td>{{ $r->threat_type }}</td>
                    <td>{{ $r->source }}</td>
                    <td class="small">{{ optional($r->verified_at)->format('d/m/Y') }}</td>
                    <td class="small">{{ optional($r->expires_at)->format('d/m/Y') }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.threats.edit', $r) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.threats.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</div>
@endsection
