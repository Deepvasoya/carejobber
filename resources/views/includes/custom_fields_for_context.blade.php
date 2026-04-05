{{--
    Render active custom fields for a context (admin-defined).
    Usage: @include('includes.custom_fields_for_context', ['context' => 'profile'])
    Contexts: profile | job_listing | resume_builder
    Optional: 'values' => [ 'slug' => value ] for pre-filled values
    Optional: 'namePrefix' => 'custom_fields' (default)
--}}
@php
    $ctx = $context ?? 'profile';
    $namePrefix = $namePrefix ?? 'custom_fields';
    $values = $values ?? [];
    $customFieldsList = \App\Models\CustomField::query()->forContext($ctx)->get();
@endphp
@if($customFieldsList->isEmpty())
    {{-- No fields defined for this context --}}
@else
    <div class="custom-fields-context" data-context="{{ $ctx }}">
        @foreach($customFieldsList as $field)
            @include('includes.custom_field_input', [
                'field' => $field,
                'namePrefix' => $namePrefix,
                'value' => $values[$field->slug] ?? null,
            ])
        @endforeach
    </div>
@endif
