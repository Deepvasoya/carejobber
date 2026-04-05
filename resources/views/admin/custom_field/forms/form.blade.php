@php
    $f = $field ?? null;
    $typesNeeding = \App\Models\CustomField::typesRequiringOptions();
    $oldContexts = old('contexts', $f ? ($f->contexts ?? []) : []);
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">{{ __('Field label') }} <span class="text-danger">*</span></label>
            <input type="text" name="label" class="form-control" value="{{ old('label', $f->label ?? '') }}" required maxlength="255" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ __('Internal key (slug)') }}</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $f->slug ?? '') }}" maxlength="191" placeholder="{{ __('Auto from label if empty') }}" />
            <small class="text-muted">{{ __('Used in code and form names; letters, numbers, hyphens.') }}</small>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Icon picture') }}</label>
    <input type="url" name="icon_url" class="form-control" value="{{ old('icon_url', $f->icon_url ?? '') }}" maxlength="2048" placeholder="{{ __('Paste the icon image URL') }}" />
    @if($f && $f->icon_url)
        <div class="mt-2"><img src="{{ $f->icon_url }}" alt="" style="max-height:32px;max-width:32px;object-fit:contain;" /></div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Field type') }} <span class="text-danger">*</span></label>
    <select name="field_type" id="custom_field_type" class="form-select" required>
        @foreach($fieldTypes as $typeKey => $typeLabel)
            <option value="{{ $typeKey }}" @selected(old('field_type', $f->field_type ?? 'text') === $typeKey)>{{ $typeLabel }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3" id="custom_field_options_block" style="display:none;">
    <label class="form-label">{{ __('Options') }} <span class="text-danger">*</span></label>
    <textarea name="options_text" id="custom_field_options_text" class="form-control" rows="6" placeholder="{{ __('One option per line. Optional: value|Label') }}">{{ old('options_text', $optionsText ?? '') }}</textarea>
    <small class="text-muted d-block mt-1">{{ __('Example: Option A') }}<br>{{ __('Or: opt-a|Option A') }}</small>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Use on') }} <span class="text-danger">*</span></label>
    @foreach($contextLabels as $ctxKey => $ctxLabel)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="contexts[]" value="{{ $ctxKey }}" id="ctx_{{ $ctxKey }}"
                {{ in_array($ctxKey, $oldContexts, true) ? 'checked' : '' }}>
            <label class="form-check-label" for="ctx_{{ $ctxKey }}">{{ $ctxLabel }}</label>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ __('Sort order') }}</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $f->sort_order ?? 0) }}" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label d-block">{{ __('Required') }}</label>
            <input type="hidden" name="is_required" value="0" />
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_required" value="1" id="cf_required" {{ old('is_required', $f->is_required ?? false) ? 'checked' : '' }} />
                <label class="form-check-label" for="cf_required">{{ __('Yes') }}</label>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label d-block">{{ __('Active') }}</label>
            <input type="hidden" name="is_active" value="0" />
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cf_active" {{ old('is_active', $f->is_active ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="cf_active">{{ __('Enable') }}</label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var needing = @json($typesNeeding);
    var sel = document.getElementById('custom_field_type');
    var block = document.getElementById('custom_field_options_block');
    var ta = document.getElementById('custom_field_options_text');
    function sync() {
        if (!sel || !block) return;
        var v = sel.value;
        var show = needing.indexOf(v) !== -1;
        block.style.display = show ? 'block' : 'none';
        if (ta) ta.required = show;
    }
    if (sel) {
        sel.addEventListener('change', sync);
        sync();
    }
})();
</script>
@endpush
