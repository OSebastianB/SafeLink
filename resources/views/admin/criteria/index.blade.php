@extends('layouts.admin')
@section('title', 'Manajemen Kriteria')
@section('page_title', 'Manajemen Kriteria')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>Total bobot aktif: <strong class="{{ abs($totalWeight - 1) < 0.0001 ? 'text-success' : 'text-danger' }}">{{ number_format($totalWeight, 4) }}</strong></div>
    <a href="{{ route('admin.criteria.create') }}" class="btn btn-cyan btn-sm">Tambah Kriteria</a>
</div>
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Kode</th><th>Nama</th><th>Bobot</th><th>Atribut</th><th>Aktif</th><th>Core</th><th></th></tr></thead>
            <tbody>
            @foreach($criteria as $c)
                <tr>
                    <td>{{ $c->code }}</td>
                    <td>
                        <div class="fw-semibold">{{ $c->name }}</div>
                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($c->description, 80) }}</div>
                    </td>
                    <td>{{ number_format($c->weight, 2) }}</td>
                    <td>{{ $c->attribute }}</td>
                    <td>{{ $c->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $c->is_core ? 'Ya' : 'Tidak' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.criteria.edit', $c) }}" class="btn btn-sm btn-outline-primary">Edit / Skala</a>
                        @unless($c->is_core)
                            <form action="{{ route('admin.criteria.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
