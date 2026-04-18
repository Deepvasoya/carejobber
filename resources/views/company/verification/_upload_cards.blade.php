@php
    $documentCards = [
        [
            'field' => 'business_registration',
            'type' => \App\VerificationDocument::TYPE_BUSINESS_REGISTRATION,
            'title' => __('Business Registration Document'),
            'description' => __('Required to start company verification. Upload this first so admin can review your business.'),
            'required' => true,
        ],
        [
            'field' => 'tax_document',
            'type' => \App\VerificationDocument::TYPE_TAX_DOCUMENT,
            'title' => __('Tax Document'),
            'description' => __('Optional supporting document for your company verification record.'),
            'required' => false,
        ],
        [
            'field' => 'establishment_photo',
            'type' => \App\VerificationDocument::TYPE_ESTABLISHMENT_PHOTO,
            'title' => __('Establishment Photo'),
            'description' => __('Optional photo of your office, shop, or business location.'),
            'required' => false,
        ],
    ];
@endphp

@if($errors->has('document_upload'))
    <div class="alert alert-danger mb-3">
        {{ $errors->first('document_upload') }}
    </div>
@endif

<div class="verification-upload-grid">
    @foreach($documentCards as $card)
        @php
            $document = $latestVerificationDocuments[$card['type']] ?? null;
        @endphp
        <div class="verification-upload-card">
            <div class="verification-upload-card__header">
                <h5 class="mb-2">
                    {{ $card['title'] }}
                    @if($card['required'])
                        <span class="verification-required">*</span>
                    @else
                        <span class="verification-optional">{{ __('Optional') }}</span>
                    @endif
                </h5>
                <p class="text-muted mb-0">{{ $card['description'] }}</p>
            </div>

            <div class="verification-upload-card__meta">
                <span>{{ __('Formats: PNG, JPG, JPEG, PDF') }}</span>
                <span>{{ __('Max size: 2MB') }}</span>
            </div>

            @if($document)
                <div class="verification-upload-card__current">
                    <div>
                        <strong>{{ __('Latest file:') }}</strong> {{ $document->original_filename }}
                    </div>
                    <div class="text-muted small">
                        {{ __('Uploaded:') }} {{ $document->uploaded_at->format('M d, Y h:i A') }}
                    </div>
                    <a href="{{ route('company.verification.document.show', $document->id) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        {{ __('View current file') }}
                    </a>
                </div>
            @endif

            {!! Form::open(['method' => 'POST', 'route' => 'company.verification.store', 'class' => 'form', 'files' => true]) !!}
                <div class="formrow {!! APFrmErrHelp::hasError($errors, $card['field']) !!}">
                    <input
                        type="file"
                        name="{{ $card['field'] }}"
                        id="{{ $card['field'] }}"
                        class="form-control"
                        accept=".png,.jpg,.jpeg,.pdf"
                        @if($card['required'] && !$document) required @endif
                    >
                    {!! APFrmErrHelp::showErrors($errors, $card['field']) !!}
                </div>

                <div class="verification-upload-card__actions">
                    <button type="submit" class="btn">
                        {{ $document ? __('Replace document') : __('Upload document') }}
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    @endforeach
</div>
