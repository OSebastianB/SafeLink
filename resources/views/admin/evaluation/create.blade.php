@extends('layouts.admin')
@section('title', 'Unggah Dataset')
@section('page_title', 'Unggah Dataset Evaluasi')

@section('content')
<div class="card card-soft p-4 col-lg-8">
    <p class="text-muted">CSV wajib berisi kolom <code>url</code> dan <code>label</code> (phishing/legitimate). Kolom <code>source</code> opsional.</p>
    <form method="POST" action="{{ route('admin.evaluation.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama dataset</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sumber</label>
            <input name="source" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">File CSV</label>
            <input type="file" name="csv" class="form-control" accept=".csv,text/csv" required>
        </div>
        <button class="btn btn-cyan">Unggah</button>
    </form>
</div>
@endsection
