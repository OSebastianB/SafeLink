@extends('layouts.app')

@section('title', 'SafeLink – Periksa Tautan Sebelum Anda Mengekliknya')

@push('styles')
<style>
    body {
        background: #0b1f3a;
    }
    .page-content {
        display: flex;
        flex-direction: column;
        flex: 1 0 auto;
    }
    .hero-panel {
        flex: 1 0 auto;
        display: flex;
        align-items: center;
        border-radius: 0;
        min-height: calc(100vh - 118px);
    }
    footer.site-footer {
        margin-top: 0 !important;
    }
</style>
@endpush

@section('content')
<section class="hero-panel">
    <div class="container py-4 position-relative" style="z-index:1;">
        <div class="row align-items-center gy-3">
            <div class="col-lg-10 col-xl-9 animate-fade-up">
                <div class="text-info fw-semibold mb-2">SafeLink</div>
                <h1 class="display-5 fw-bold mb-3">Periksa Tautan Sebelum Anda Mengekliknya.</h1>
                <p class="lead text-white-50 mb-4">Nilai potensi risiko phishing pada URL secara cepat dan transparan.</p>
                <form action="{{ route('scan.store') }}" method="POST" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-lg-9">
                        <input type="text" name="url" value="{{ old('url') }}" class="form-control url-input @error('url') is-invalid @enderror" placeholder="Tempel URL di sini, contoh: https://example.com/login" required maxlength="2048">
                        @error('url') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-cyan scan-btn w-100" type="submit">
                            <i class="bi bi-shield-check me-1"></i> Scan URL Sekarang
                        </button>
                    </div>
                </form>
                <p class="small text-white-50 mt-3 mb-0"><i class="bi bi-lock"></i> Sistem tidak membuka atau mengakses situs yang sedang diperiksa.</p>
            </div>
        </div>
    </div>
</section>
@endsection
