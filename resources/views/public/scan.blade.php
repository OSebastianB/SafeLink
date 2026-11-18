@extends('layouts.app')

@section('title', 'Periksa URL – SafeLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-soft p-4 p-lg-5">
                <h1 class="h3 fw-bold mb-2">Periksa URL</h1>
                <p class="text-muted-2 mb-4">Tempel tautan yang ingin dinilai. Sistem tidak membuka situs target.</p>
                <form action="{{ route('scan.store') }}" method="POST" id="scanForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL</label>
                        <input type="text" name="url" value="{{ old('url') }}" class="form-control form-control-lg @error('url') is-invalid @enderror" placeholder="https://example.com" required maxlength="2048">
                        @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-cyan btn-lg" type="submit" id="scanBtn">
                        <span class="default-label"><i class="bi bi-search"></i> Periksa URL</span>
                        <span class="loading-label d-none"><span class="spinner-border spinner-border-sm me-1"></span> Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('scanForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('scanBtn');
    btn.querySelector('.default-label').classList.add('d-none');
    btn.querySelector('.loading-label').classList.remove('d-none');
    btn.disabled = true;
});
</script>
@endpush
