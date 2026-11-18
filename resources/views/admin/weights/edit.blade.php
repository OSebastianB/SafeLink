@extends('layouts.admin')
@section('title', 'Manajemen Bobot')
@section('page_title', 'Manajemen Bobot')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft p-4">
            <form method="POST" action="{{ route('admin.weights.update') }}" id="weightForm">
                @csrf @method('PUT')
                @foreach($criteria as $c)
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6"><strong>{{ $c->code }}</strong> – {{ $c->name }}</div>
                        <div class="col-md-6">
                            <input type="number" step="0.01" min="0" max="1" name="weights[{{ $c->id }}]" value="{{ number_format($c->weight, 2, '.', '') }}" class="form-control weight-input" required>
                        </div>
                    </div>
                @endforeach
                <div class="alert alert-light border mt-3">Total: <strong id="weightTotal">0</strong> (harus 1.00)</div>
                <button class="btn btn-cyan">Simpan Bobot</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft p-3">
            <h2 class="h6 fw-bold">Log Perubahan Bobot</h2>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Waktu</th><th>Kode</th><th>Lama</th><th>Baru</th></tr></thead>
                    <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small">{{ $log->created_at->format('d/m H:i') }}</td>
                            <td>{{ $log->criterion_code }}</td>
                            <td>{{ number_format($log->old_weight, 2) }}</td>
                            <td>{{ number_format($log->new_weight, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">Belum ada perubahan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function recalc() {
    let t = 0;
    document.querySelectorAll('.weight-input').forEach(i => t += parseFloat(i.value || 0));
    document.getElementById('weightTotal').textContent = t.toFixed(4);
}
document.querySelectorAll('.weight-input').forEach(i => i.addEventListener('input', recalc));
recalc();
</script>
@endpush
