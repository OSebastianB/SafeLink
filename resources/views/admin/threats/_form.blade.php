@php($r = $record ?? null)
<div class="mb-3">
    <label class="form-label">Domain</label>
    <input name="domain" class="form-control" value="{{ old('domain', $r->domain ?? '') }}" required>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach(['malicious','phishing','suspicious','no_match','unknown'] as $s)
                <option value="{{ $s }}" @selected(old('status', $r->status ?? '')===$s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Threat type</label>
        <input name="threat_type" class="form-control" value="{{ old('threat_type', $r->threat_type ?? '') }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Sumber</label>
    <input name="source" class="form-control" value="{{ old('source', $r->source ?? 'manual') }}" required>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Verified at</label>
        <input type="datetime-local" name="verified_at" class="form-control" value="{{ old('verified_at', optional($r->verified_at ?? null)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Expires at</label>
        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', optional($r->expires_at ?? null)->format('Y-m-d\TH:i')) }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $r->notes ?? '') }}</textarea>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_synthetic" value="1" @checked(old('is_synthetic', $r->is_synthetic ?? true))>
    <label class="form-check-label">Data sintetis / demo</label>
</div>
