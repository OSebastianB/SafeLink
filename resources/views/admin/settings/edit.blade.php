@extends('layouts.admin')
@section('title', 'Pengaturan Risiko')
@section('page_title', 'Pengaturan Kategori Risiko')

@section('content')
<div class="card card-soft p-4 col-lg-8">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Ambang maksimum Risiko Rendah</label>
            <input type="number" step="0.1" name="risk_low_max" class="form-control" value="{{ old('risk_low_max', $settings['risk_low_max']) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ambang maksimum Risiko Sedang</label>
            <input type="number" step="0.1" name="risk_medium_max" class="form-control" value="{{ old('risk_medium_max', $settings['risk_medium_max']) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Peringatan cakupan data minimum (%)</label>
            <input type="number" step="0.1" name="min_data_coverage_warning" class="form-control" value="{{ old('min_data_coverage_warning', $settings['min_data_coverage_warning']) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Disclaimer</label>
            <textarea name="app_disclaimer" class="form-control" rows="4">{{ old('app_disclaimer', $settings['app_disclaimer']) }}</textarea>
        </div>
        <button class="btn btn-cyan">Simpan Pengaturan</button>
    </form>
</div>
@endsection
