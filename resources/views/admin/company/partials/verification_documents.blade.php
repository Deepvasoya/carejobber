<span class="doc-count">
    <i class="ri ri-file-line me-1"></i>
    {{ $company->verificationDocuments->count() }} document(s)
</span>
<br>
@if($company->hasBusinessRegistration())
    <span class="badge bg-success bg-opacity-10 text-success">
        <i class="ri ri-checkbox-circle-line me-1"></i>Has Business Reg.
    </span>
@else
    <span class="badge bg-danger bg-opacity-10 text-danger">
        <i class="ri ri-close-circle-line me-1"></i>Missing Business Reg.
    </span>
@endif
<div class="verification-doc-links">
    @foreach($company->verificationDocuments as $doc)
        <a href="{{ route('admin.company.verification.document.show', $doc->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
            {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
        </a>
    @endforeach
</div>
