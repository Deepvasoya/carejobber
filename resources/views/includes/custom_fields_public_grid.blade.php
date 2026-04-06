{{--
    Public profile grid rows for saved custom fields (Candidate Details, Company Detail).
    Expects: $record (User|Company with custom_field_data), $context (CustomField context string)
--}}
@php
    $ctx = $context ?? \App\Models\CustomField::CONTEXT_PROFILE;
    $formatter = app(\App\Services\CustomFieldValueService::class);
    $data = $record->custom_field_data ?? [];
    $fieldsList = \App\Models\CustomField::query()->forContext($ctx)->get();
@endphp
@foreach($fieldsList as $field)
    @php
        $stored = $data[$field->slug] ?? null;
    @endphp
    @if($formatter->hasDisplayableValue($field, $stored))
        <li class="col-lg-6 col-md-6 col-6">
            <div class="jbitlist">
                @if(!empty($field->icon_url))
                    <span class="cf-icon-wrap" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;">
                        <img src="{{ $field->icon_url }}" alt="" style="max-width:24px;max-height:24px;object-fit:contain;" />
                    </span>
                @else
                    <span class="material-symbols-outlined">tune</span>
                @endif
                <div class="jbitdata">
                    <strong>{{ $field->label }}</strong>
                    @if($field->field_type === \App\Models\CustomField::TYPE_TEXTAREA)
                        <span>{!! nl2br(e($formatter->formatDisplayValue($field, $stored))) !!}</span>
                    @else
                        <span>{{ $formatter->formatDisplayValue($field, $stored) }}</span>
                    @endif
                </div>
            </div>
        </li>
    @endif
@endforeach
