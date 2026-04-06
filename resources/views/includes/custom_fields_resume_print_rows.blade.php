{{-- Extra rows for printable resume: $profileCv (ProfileCv), context resume_builder --}}
@if(isset($profileCv) && $profileCv)
@php
    $formatter = app(\App\Services\CustomFieldValueService::class);
    $ctx = \App\Models\CustomField::CONTEXT_RESUME_BUILDER;
    $data = $profileCv->custom_field_data ?? [];
@endphp
@foreach(\App\Models\CustomField::query()->forContext($ctx)->get() as $field)
    @php $stored = $data[$field->slug] ?? null; @endphp
    @if($formatter->hasDisplayableValue($field, $stored))
        <tr>
            <td style="padding: 10px 0;"><strong>{{ $field->label }}</strong></td>
            <td style="padding: 10px 0;">
                @if($field->field_type === \App\Models\CustomField::TYPE_TEXTAREA)
                    {!! nl2br(e($formatter->formatDisplayValue($field, $stored))) !!}
                @else
                    {{ $formatter->formatDisplayValue($field, $stored) }}
                @endif
            </td>
        </tr>
    @endif
@endforeach
@endif
