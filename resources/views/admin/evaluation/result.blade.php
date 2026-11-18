@extends('layouts.admin')
@section('title', 'Hasil Evaluasi')
@section('page_title', 'Hasil Evaluasi – '.$dataset->name)

@section('content')
<div class="alert alert-info">
    Mode: <strong>{{ $mode }}</strong>.
    @if($mode === 'binary')
        Aturan: High Risk = prediksi phishing; Low/Medium = legitimate.
    @endif
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Dievaluasi', $result['evaluated']],
        ['Benar', $result['correct']],
        ['Salah', $result['incorrect']],
        ['Accuracy', number_format($result['accuracy']*100, 2).'%'],
        ['Precision', number_format($result['precision']*100, 2).'%'],
        ['Recall', number_format($result['recall']*100, 2).'%'],
        ['F1-Score', number_format($result['f1_score']*100, 2).'%'],
    ] as [$label,$value])
        <div class="col-6 col-md-3 col-xl">
            <div class="card card-soft p-3">
                <div class="small text-muted">{{ $label }}</div>
                <div class="fs-5 fw-bold">{{ $value }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card card-soft p-3 mb-4">
    <h2 class="h6 fw-bold">Confusion Matrix</h2>
    <pre class="mb-0 small">{{ json_encode($result['confusion_matrix'], JSON_PRETTY_PRINT) }}</pre>
</div>

@if(!empty($result['errors']))
    <div class="alert alert-warning">
        <strong>{{ count($result['errors']) }}</strong> baris gagal diproses.
    </div>
@endif

<div class="card card-soft p-3">
    <h2 class="h6 fw-bold">Detail Prediksi</h2>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>URL</th><th>GT</th><th>Pred</th><th>Kategori</th><th>Risk</th><th>Benar?</th></tr></thead>
            <tbody>
            @foreach($result['details'] as $d)
                <tr>
                    <td class="small text-break">{{ $d['url'] }}</td>
                    <td>{{ $d['ground_truth'] }}</td>
                    <td>{{ $d['predicted'] }}</td>
                    <td>{{ $d['risk_category'] }}</td>
                    <td>{{ number_format($d['risk_index'], 2) }}</td>
                    <td>{{ $d['correct'] ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
