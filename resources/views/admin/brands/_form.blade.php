@php($b = $brand ?? null)
<div class="mb-3">
    <label class="form-label">Nama brand</label>
    <input name="brand_name" class="form-control" value="{{ old('brand_name', $b->brand_name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Domain resmi</label>
    <input name="official_domain" class="form-control" value="{{ old('official_domain', $b->official_domain ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Keywords (pisahkan koma)</label>
    <input name="keywords" class="form-control" value="{{ old('keywords', isset($b) ? implode(', ', $b->keywords ?? []) : '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $b->notes ?? '') }}</textarea>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $b->is_active ?? true))>
    <label class="form-check-label">Aktif</label>
</div>
