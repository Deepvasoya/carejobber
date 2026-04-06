{{-- Single custom field input. Expects $field (CustomField), optional $namePrefix, $value --}}
@php
    $namePrefix = $namePrefix ?? 'custom_fields';
    $baseName = $namePrefix . '[' . $field->slug . ']';
    $id = 'cf_' . $field->slug;
    $oldKey = $namePrefix . '.' . $field->slug;
    $val = $value ?? old($oldKey);
    $opts = $field->options ?? [];
    $label = $field->label;
    $req = $field->is_required;
@endphp

<div class="mb-3 custom-field-input" data-field-slug="{{ $field->slug }}" data-field-type="{{ $field->field_type }}">
    <label class="form-label" for="{{ $id }}">
        @if($field->icon_url)
            <img src="{{ $field->icon_url }}" alt="" class="me-1" style="width:20px;height:20px;object-fit:contain;vertical-align:middle;" />
        @endif
        {{ $label }}
        @if($req)<span class="text-danger">*</span>@endif
    </label>

    @switch($field->field_type)
        @case(\App\Models\CustomField::TYPE_TEXT)
            <input type="text" class="form-control" name="{{ $baseName }}" id="{{ $id }}" value="{{ $val ?? '' }}" @if($req) required @endif />
            @break
        @case(\App\Models\CustomField::TYPE_NUMBER)
            <input type="number" class="form-control" name="{{ $baseName }}" id="{{ $id }}" value="{{ $val ?? '' }}" step="any" @if($req) required @endif />
            @break
        @case(\App\Models\CustomField::TYPE_TEXTAREA)
            <textarea class="form-control" name="{{ $baseName }}" id="{{ $id }}" rows="3" @if($req) required @endif>{{ $val ?? '' }}</textarea>
            @break
        @case(\App\Models\CustomField::TYPE_DATE)
            <input type="date" class="form-control" name="{{ $baseName }}" id="{{ $id }}" value="{{ $val ?? '' }}" @if($req) required @endif />
            @break
        @case(\App\Models\CustomField::TYPE_DATETIME)
            <input type="datetime-local" class="form-control" name="{{ $baseName }}" id="{{ $id }}" value="{{ $val ?? '' }}" @if($req) required @endif />
            @break
        @case(\App\Models\CustomField::TYPE_RADIO)
            <div>
                @foreach($opts as $opt)
                    @php
                        $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                        $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt;
                        $rid = $id . '_' . $loop->index;
                    @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{ $baseName }}" id="{{ $rid }}" value="{{ $ov }}" @checked((string)($val ?? '') === (string)$ov) @if($req && $loop->first) required @endif />
                        <label class="form-check-label" for="{{ $rid }}">{{ $ol }}</label>
                    </div>
                @endforeach
            </div>
            @break
        @case(\App\Models\CustomField::TYPE_SELECT)
            <select class="form-control" name="{{ $baseName }}" id="{{ $id }}" @if($req) required @endif>
                <option value="">{{ __('Select…') }}</option>
                @foreach($opts as $opt)
                    @php
                        $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                        $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt;
                    @endphp
                    <option value="{{ $ov }}" @selected((string)($val ?? '') === (string)$ov)>{{ $ol }}</option>
                @endforeach
            </select>
            @break
        @case(\App\Models\CustomField::TYPE_MULTISELECT)
            @php
                $selected = is_array($val) ? $val : (array) array_filter(explode(',', (string)($val ?? '')));
            @endphp
            <select class="form-control" name="{{ $baseName }}[]" id="{{ $id }}" multiple size="{{ min(8, max(3, count($opts))) }}" @if($req) required @endif>
                @foreach($opts as $opt)
                    @php
                        $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                        $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt;
                    @endphp
                    <option value="{{ $ov }}" @selected(in_array((string)$ov, array_map('strval', $selected), true))>{{ $ol }}</option>
                @endforeach
            </select>
            <small class="text-muted">{{ __('Hold Ctrl/Cmd to select multiple.') }}</small>
            @break
        @case(\App\Models\CustomField::TYPE_CHECKBOXES)
            @php
                $selected = is_array($val) ? $val : (array) array_filter(explode(',', (string)($val ?? '')));
            @endphp
            <div>
                @foreach($opts as $opt)
                    @php
                        $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                        $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt;
                        $cid = $id . '_' . $loop->index;
                    @endphp
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $baseName }}[]" id="{{ $cid }}" value="{{ $ov }}" @checked(in_array((string)$ov, array_map('strval', $selected), true)) />
                        <label class="form-check-label" for="{{ $cid }}">{{ $ol }}</label>
                    </div>
                @endforeach
            </div>
            @break
        @default
            <input type="text" class="form-control" name="{{ $baseName }}" id="{{ $id }}" value="{{ $val ?? '' }}" />
    @endswitch
    @error('custom_fields.'.$field->slug)
        <span class="text-danger small">{{ $message }}</span>
    @enderror
</div>
