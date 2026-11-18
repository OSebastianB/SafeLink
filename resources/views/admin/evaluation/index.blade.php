@extends('layouts.admin')
@section('title', 'Evaluasi SAW')
@section('page_title', 'Evaluasi Metode SAW')

@section('content')
<div class="mb-3"><a href="{{ route('admin.evaluation.create') }}" class="btn btn-cyan btn-sm">Unggah Dataset</a></div>
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Nama</th><th>Sumber</th><th>Baris</th><th>Diunggah</th><th></th></tr></thead>
            <tbody>
            @forelse($datasets as $ds)
                <tr>
                    <td>{{ $ds->name }}</td>
                    <td>{{ $ds->source }}</td>
                    <td>{{ $ds->rows_count }}</td>
                    <td class="small">{{ $ds->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.evaluation.show', $ds) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        <form action="{{ route('admin.evaluation.destroy', $ds) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dataset?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">Belum ada dataset.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $datasets->links() }}
</div>
@endsection
