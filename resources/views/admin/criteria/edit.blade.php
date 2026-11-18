@extends('layouts.admin')
@section('title', 'Edit Kriteria')
@section('page_title', 'Edit Kriteria '.$criterion->code)

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-soft p-4">
            <form method="POST" action="{{ route('admin.criteria.update', $criterion) }}">
                @csrf @method('PUT')
                @include('admin.criteria._form', ['criterion' => $criterion])
                <button class="btn btn-cyan">Perbarui</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-soft p-4">
            <h2 class="h6 fw-bold">Skala Penilaian</h2>
            <div class="table-responsive mb-3">
                <table class="table table-sm">
                    <thead><tr><th>Kondisi</th><th>Skor</th><th>Aktif</th><th></th></tr></thead>
                    <tbody>
                    @foreach($criterion->scales as $scale)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.scales.update', $scale) }}" class="row g-1">
                                    @csrf @method('PUT')
                                    <div class="col-12"><input name="condition_name" class="form-control form-control-sm" value="{{ $scale->condition_name }}" required></div>
                                    <div class="col-12"><input name="condition_description" class="form-control form-control-sm" value="{{ $scale->condition_description }}"></div>
                                    <div class="col-4"><input name="score" type="number" step="0.1" min="0" max="5" class="form-control form-control-sm" value="{{ $scale->score }}" required></div>
                                    <div class="col-4"><input name="sort_order" type="number" class="form-control form-control-sm" value="{{ $scale->sort_order }}"></div>
                                    <div class="col-4 form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($scale->is_active)> Aktif</div>
                                    <div class="col-12"><button class="btn btn-sm btn-outline-primary">Simpan</button></div>
                                </form>
                            </td>
                            <td>{{ $scale->score }}</td>
                            <td>{{ $scale->is_active ? 'Ya' : 'Tidak' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.scales.destroy', $scale) }}" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('admin.criteria.scales.store', $criterion) }}" class="row g-2">
                @csrf
                <div class="col-md-4"><input name="condition_name" class="form-control" placeholder="Nama kondisi" required></div>
                <div class="col-md-4"><input name="condition_description" class="form-control" placeholder="Deskripsi"></div>
                <div class="col-md-2"><input name="score" type="number" step="0.1" min="0" max="5" class="form-control" placeholder="Skor" required></div>
                <div class="col-md-2"><button class="btn btn-cyan w-100">Tambah</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
