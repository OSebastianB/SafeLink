@extends('layouts.admin')
@section('title', 'Dataset Evaluasi')
@section('page_title', $dataset->name)

@section('content')
<div class="card card-soft p-3 mb-3">
    <p class="mb-1">{{ $dataset->description }}</p>
    <div class="small text-muted">Sumber: {{ $dataset->source ?: '-' }} | Total baris: {{ $dataset->total_rows }}</div>
    <form method="POST" action="{{ route('admin.evaluation.run', $dataset) }}" class="row g-2 align-items-end mt-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Mode evaluasi</label>
            <select name="mode" class="form-select">
                <option value="binary">Binary (High=phishing; Low/Medium=legitimate)</option>
                <option value="multiclass">Tiga kategori (low/medium/high)</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-cyan w-100">Jalankan Evaluasi</button></div>
    </form>
</div>
<div class="card card-soft p-3">
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>URL</th><th>Label</th><th>Source</th></tr></thead>
            <tbody>
            @foreach($dataset->rows as $row)
                <tr>
                    <td class="small text-break">{{ $row->url }}</td>
                    <td>{{ $row->ground_truth_label }}</td>
                    <td>{{ $row->source_reference }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
