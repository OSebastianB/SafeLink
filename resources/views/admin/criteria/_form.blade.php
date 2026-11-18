@php($c = $criterion ?? null)
<div class="mb-3">
    <label class="form-label">Kode</label>
    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $c->code ?? '') }}" required {{ isset($c) && $c->is_core ? 'readonly' : '' }}>
    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $c->name ?? '') }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $c->description ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Bobot (0–1)</label>
        <input type="number" step="0.01" min="0" max="1" name="weight" class="form-control" value="{{ old('weight', $c->weight ?? '0.00') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Atribut</label>
        <select name="attribute" class="form-select">
            <option value="benefit" @selected(old('attribute', $c->attribute ?? 'benefit')==='benefit')>Benefit</option>
            <option value="cost" @selected(old('attribute', $c->attribute ?? '')==='cost')>Cost</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Urutan</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $c->sort_order ?? 0) }}">
    </div>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $c->is_active ?? true))>
    <label class="form-check-label" for="is_active">Aktif</label>
</div>
