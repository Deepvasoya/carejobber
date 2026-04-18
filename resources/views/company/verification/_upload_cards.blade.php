@php
    $documentCards = [
        [
            'field' => 'business_registration',
            'type' => \App\VerificationDocument::TYPE_BUSINESS_REGISTRATION,
            'title' => __('Business Registration Document'),
            'description' => __('Required to start company verification. Upload this first so admin can review your business.'),
            'required' => true,
            'icon' => 'fas fa-file-alt',
            'icon_color' => '#0357e9',
        ],
        [
            'field' => 'tax_document',
            'type' => \App\VerificationDocument::TYPE_TAX_DOCUMENT,
            'title' => __('Tax Document'),
            'description' => __('Optional supporting document for your company verification record.'),
            'required' => false,
            'icon' => 'fas fa-file-invoice-dollar',
            'icon_color' => '#28a745',
        ],
        [
            'field' => 'establishment_photo',
            'type' => \App\VerificationDocument::TYPE_ESTABLISHMENT_PHOTO,
            'title' => __('Establishment Photo'),
            'description' => __('Optional photo of your office, shop, or business location.'),
            'required' => false,
            'icon' => 'fas fa-camera',
            'icon_color' => '#fd7e14',
        ],
    ];
@endphp

@if($errors->has('document_upload'))
    <div class="alert alert-danger mb-3">
        {{ $errors->first('document_upload') }}
    </div>
@endif

<div class="vdoc-list">
    @foreach($documentCards as $i => $card)
        @php $document = $latestVerificationDocuments[$card['type']] ?? null; @endphp

        <div class="vdoc-item">
            {{-- Left: icon + info --}}
            <div class="vdoc-info">
                <div class="vdoc-icon" style="background: {{ $card['icon_color'] }}18; color: {{ $card['icon_color'] }};">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <div class="vdoc-text">
                    <div class="vdoc-title">
                        {{ $card['title'] }}
                        @if($card['required'])
                            <span class="vdoc-badge vdoc-badge--required">{{ __('Required') }}</span>
                        @else
                            <span class="vdoc-badge vdoc-badge--optional">{{ __('Optional') }}</span>
                        @endif
                    </div>
                    <div class="vdoc-desc">{{ $card['description'] }}</div>
                    <div class="vdoc-meta">
                        <span><i class="fas fa-file-upload"></i> PNG, JPG, JPEG, PDF</span>
                        <span><i class="fas fa-weight"></i> Max 2MB</span>
                        @if($document)
                            <span class="vdoc-uploaded">
                                <i class="fas fa-check-circle"></i>
                                {{ $document->original_filename }}
                                &mdash; {{ $document->uploaded_at->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: upload form --}}
            <div class="vdoc-actions">
                @if($document)
                    <a href="{{ route('company.verification.document.show', $document->id) }}" target="_blank" class="vdoc-btn vdoc-btn--view">
                        <i class="fas fa-eye"></i> {{ __('View') }}
                    </a>
                @endif

                {!! Form::open(['method' => 'POST', 'route' => 'company.verification.store', 'class' => 'vdoc-form', 'files' => true]) !!}
                    <label class="vdoc-file-label" for="vdoc_{{ $card['field'] }}">
                        <i class="fas fa-paperclip"></i>
                        <span class="vdoc-file-name" id="vdoc_name_{{ $card['field'] }}">{{ __('Choose file') }}</span>
                    </label>
                    <input
                        type="file"
                        name="{{ $card['field'] }}"
                        id="vdoc_{{ $card['field'] }}"
                        class="vdoc-file-input"
                        accept=".png,.jpg,.jpeg,.pdf"
                        data-label="vdoc_name_{{ $card['field'] }}"
                        @if($card['required'] && !$document) required @endif
                    >
                    {!! APFrmErrHelp::showErrors($errors, $card['field']) !!}
                    <button type="submit" class="vdoc-btn vdoc-btn--upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        {{ $document ? __('Replace') : __('Upload') }}
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    @endforeach
</div>

<style>
.vdoc-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 16px;
}
.vdoc-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 20px;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
}
.vdoc-item:last-child { border-bottom: none; }
.vdoc-item:hover { background: #fafbfc; }

.vdoc-info {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    flex: 1;
    min-width: 0;
}
.vdoc-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.vdoc-text { flex: 1; min-width: 0; }
.vdoc-title {
    font-weight: 600;
    font-size: 14px;
    color: #1a1a2e;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 3px;
}
.vdoc-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.vdoc-badge--required { background: #fee2e2; color: #dc2626; }
.vdoc-badge--optional { background: #f0fdf4; color: #16a34a; }
.vdoc-desc {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 6px;
    line-height: 1.5;
}
.vdoc-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 11px;
    color: #9ca3af;
}
.vdoc-meta i { margin-right: 3px; }
.vdoc-uploaded { color: #16a34a; font-weight: 500; }

.vdoc-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.vdoc-form {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.vdoc-file-input { display: none; }
.vdoc-file-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border: 1px dashed #d1d5db;
    border-radius: 8px;
    font-size: 12px;
    color: #374151;
    cursor: pointer;
    background: #f9fafb;
    white-space: nowrap;
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: border-color 0.2s, background 0.2s;
}
.vdoc-file-label:hover { border-color: #0357e9; background: #eff6ff; color: #0357e9; }
.vdoc-file-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 110px;
    display: inline-block;
}
.vdoc-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    transition: opacity 0.2s;
}
.vdoc-btn:hover { opacity: 0.85; text-decoration: none; }
.vdoc-btn--upload { background: #0357e9; color: #fff; }
.vdoc-btn--view { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

@media (max-width: 768px) {
    .vdoc-item { flex-direction: column; align-items: flex-start; }
    .vdoc-actions { width: 100%; }
    .vdoc-form { flex-wrap: wrap; }
}
</style>

<script>
document.querySelectorAll('.vdoc-file-input').forEach(function(input) {
    input.addEventListener('change', function() {
        var labelId = this.dataset.label;
        var label = document.getElementById(labelId);
        if (label && this.files.length > 0) {
            label.textContent = this.files[0].name;
        }
    });
});
</script>
