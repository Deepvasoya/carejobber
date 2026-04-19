@extends('admin.layouts.admin_layout')
@push('css')
<style>
    .company-detail-logo { max-width: 100px; overflow: hidden; border-radius: 8px; }
    .company-detail-logo img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; display: block; }
    .company-detail-list { list-style: none; padding: 0; margin: 0; }
    .company-detail-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .company-detail-list li:last-child { border-bottom: 0; }
    .company-detail-list .text-muted { font-size: 0.875rem; }
    .job-opening-item { padding: 16px 0; border-bottom: 1px solid #eee; }
    .job-opening-item:last-child { border-bottom: 0; padding-bottom: 0; }
    .job-opening-item .badge { font-weight: 500; }
</style>
@endpush
@section('content')
<div class="page-bar mb-3">
    <ul class="page-breadcrumb mb-0">
        <li><a href="{{ route('admin.home') }}">Home</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><a href="{{ route('list.companies') }}">Companies</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><span>Company Details</span></li>
    </ul>
</div>
@include('flash::message')

{{-- Company overview card --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-start">
            <div class="col-md-8">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="company-detail-logo">{!! $company->printCompanyImage(100, 100) !!}</div>
                    </div>
                    <div>
                        <h4 class="mb-1">{{ $company->name }}</h4>
                        @if($company->getIndustry('industry'))
                        <p class="text-muted mb-1">{{ $company->getIndustry('industry') }}</p>
                        @endif
                        <p class="mb-0 small text-muted">
                            <i class="ri ri-calendar-line me-1"></i> {{ __('Member Since') }}, {{ $company->created_at->format('M d, Y') }}
                        </p>
                        @if($company->location)
                        <p class="mb-0 small text-muted">
                            <i class="ri ri-map-pin-line me-1"></i> {{ $company->location }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="border-start ps-3">
                    @if(!empty($company->phone))
                    <p class="mb-1 small"><i class="ri ri-phone-line me-1 text-muted"></i> <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a></p>
                    @endif
                    @if(!empty($company->email))
                    <p class="mb-1 small"><i class="ri ri-mail-line me-1 text-muted"></i> <a href="mailto:{{ $company->email }}">{{ $company->email }}</a></p>
                    @endif
                    @if(!empty($company->website) && filter_var($company->website, FILTER_VALIDATE_URL) !== false)
                    <p class="mb-1 small"><i class="ri ri-global-line me-1 text-muted"></i> <a href="{{ $company->website }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($company->website, 40) }}</a></p>
                    @else
                    <p class="mb-1 small text-muted">URL not present in profile</p>
                    @endif
                    @if($company->getSocialNetworkHtml())
                    <div class="mt-2">{!! $company->getSocialNetworkHtml() !!}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- About Company --}}
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-information-line me-1"></i> {{ __('About Company') }}</h5>
            </div>
            <div class="card-body">
                <div class="prose">{!! $company->description !!}</div>
            </div>
        </div>

        {{-- Job Openings --}}
        <?php $jobs = $company->jobs()->notExpire()->where('jobs.is_active', 1)->get(); ?>
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-briefcase-4-line me-1"></i> {{ __('Job Openings') }}</h5>
            </div>
            <div class="card-body">
                @if(isset($jobs) && $jobs->count())
                <ul class="list-unstyled mb-0">
                    @foreach($jobs as $companyJob)
                    <li class="job-opening-item">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <h6 class="mb-1">
                                    <a href="{{ route('public.job', ['id' => $companyJob->id]) }}" title="{{ $companyJob->title }}">{{ $companyJob->title }}</a>
                                </h6>
                                <div class="small text-muted">
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-1">{{ $companyJob->getJobType('job_type') }}</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">{{ $companyJob->getJobShift('job_shift') }}</span>
                                    <span>{{ $companyJob->getCity('city') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('public.job', ['id' => $companyJob->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('View Job Details') }}</a>
                        </div>
                        <p class="small text-muted mt-2 mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($companyJob->description), 150, '...') }}</p>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted mb-0">{{ __('No active job openings.') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Company Details --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-building-line me-1"></i> {{ __('Company Details') }}</h5>
            </div>
            <div class="card-body">
                <ul class="company-detail-list">
                    <li>
                        <span class="text-muted">{{ __('Email Verified') }}</span>
                        <span>{{ (bool)$company->verified ? __('Yes') : __('No') }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Company Verified') }}</span>
                        <span>
                            @if($company->isVerified())
                                <span class="badge bg-success"><i class="ri ri-checkbox-circle-line me-1"></i>Verified</span>
                            @else
                                <span class="badge bg-warning"><i class="ri ri-time-line me-1"></i>Pending</span>
                            @endif
                        </span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Total Employees') }}</span>
                        <span>{{ $company->no_of_employees ?? '–' }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Established In') }}</span>
                        <span>{{ $company->established_in ?? '–' }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Current jobs') }}</span>
                        <span>{{ $company->countNumJobs('company_id', $company->id) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Verification Documents --}}
        <?php $verificationDocs = $company->verificationDocuments; ?>
        @if($verificationDocs->count() > 0)
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="ri ri-file-shield-line me-1"></i> {{ __('Verification Documents') }}</h5>
                @if(!$company->isVerified())
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success" id="approveVerificationBtn">
                        <i class="ri ri-checkbox-circle-line me-1"></i>Approve Verification
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" id="rejectVerificationBtn">
                        <i class="ri ri-close-circle-line me-1"></i>Reject Verification
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($verificationDocs as $doc)
                    <li class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    @if($doc->document_type === 'business_registration')
                                        <i class="ri ri-file-text-line me-1"></i>Business Registration
                                    @elseif($doc->document_type === 'tax_document')
                                        <i class="ri ri-file-list-line me-1"></i>Tax Document
                                    @elseif($doc->document_type === 'establishment_photo')
                                        <i class="ri ri-image-line me-1"></i>Establishment Photo
                                    @endif
                                </h6>
                                <p class="small text-muted mb-1">{{ $doc->original_filename }}</p>
                                <p class="small text-muted mb-0">
                                    <i class="ri ri-calendar-line me-1"></i>{{ $doc->uploaded_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <a href="{{ route('admin.company.verification.document.show', $doc->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="ri ri-download-line me-1"></i>View
                            </a>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @if($company->isVerified())
                <div class="alert alert-success mb-0 mt-2">
                    <i class="ri ri-checkbox-circle-line me-1"></i>
                    Company verified on {{ $company->verified_at->format('M d, Y') }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Company Person --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-user-line me-1"></i> {{ __('Company Person') }}</h5>
            </div>
            <div class="card-body">
                <ul class="company-detail-list">
                    <li>
                        <span class="text-muted">{{ __('Name') }}</span>
                        <span>{{ $company->contact_name ?? '–' }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Email') }}</span>
                        <span>{{ $company->contact_email ?? '–' }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Designation') }}</span>
                        <span>{{ $company->ceo ?? '–' }}</span>
                    </li>
                    <li>
                        <span class="text-muted">{{ __('Company Reg. Number') }}</span>
                        <span>{{ $company->registration_number ?? '–' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Status Update Modal (Bootstrap 5) --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Document Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label for="documentTitle" class="form-label">Document Title</label>
                        <input type="text" class="form-control" id="documentTitle" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="approve" value="1">
                            <label class="form-check-label" for="approve">Approve</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="reject" value="0">
                            <label class="form-check-label" for="reject">Reject</label>
                        </div>
                    </div>
                    <div class="mb-3" id="commentsGroup" style="display:none;">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="documentField" name="field">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStatusBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style type="text/css">.formrow iframe { height: 78px; }</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
$(function() {
    var statusModalEl = document.getElementById('statusModal');
    var statusModal = statusModalEl ? new bootstrap.Modal(statusModalEl) : null;
    $('.status-icon').on('click', function() {
        var $icon = $(this);
        var field = $icon.data('field');
        var documentTitle = $icon.closest('tr').find('label').text();
        $('#documentTitle').val(documentTitle);
        $('#documentField').val(field);
        $('input[name="status"]').prop('checked', false);
        $('#commentsGroup').hide();
        if (statusModal) statusModal.show();
    });
    $('input[name="status"]').on('change', function() {
        $('#commentsGroup').toggle($(this).val() == '0');
    });
    $('#saveStatusBtn').on('click', function() {
        var field = $('#documentField').val();
        var status = $('input[name="status"]:checked').val();
        var comments = $('#comments').val();
        $.ajax({
            url: "{{ route('edit.changeStatus', $company->id) }}",
            type: 'POST',
            data: { field: field, status: status, comments: comments, _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    var $icon = $('.status-icon[data-field="' + field + '"]');
                    var $i = $icon.find('i');
                    if (status == 1) {
                        $i.removeClass('fa-times-circle text-danger ri-close-circle-line').addClass('fa-check-circle text-success ri-checkbox-circle-line');
                        $icon.attr('title', 'Verified');
                        $icon.contents().filter(function() { return this.nodeType === 3; }).remove();
                        $icon.append(' Approved');
                    } else {
                        $i.removeClass('fa-check-circle text-success ri-checkbox-circle-line').addClass('fa-times-circle text-danger ri-close-circle-line');
                        $icon.attr('title', 'Not Verified');
                        $icon.contents().filter(function() { return this.nodeType === 3; }).remove();
                        $icon.append(' Rejected');
                    }
                    Swal.fire('Changed!', 'The status has been updated.', 'success');
                } else {
                    Swal.fire('Error!', 'There was an error updating the status.', 'error');
                }
                if (statusModal) statusModal.hide();
            },
            error: function() {
                Swal.fire('Error!', 'There was an error updating the status.', 'error');
                if (statusModal) statusModal.hide();
            }
        });
    });

    // Approve company verification
    $('#approveVerificationBtn').on('click', function() {
        Swal.fire({
            title: 'Approve Company Verification?',
            text: 'This will mark the company as verified and grant them a verified badge.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.company.approve.verification', $company->id) }}",
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'Company has been verified successfully.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message || 'There was an error approving the verification.', 'error');
                        }
                    },
                    error: function(xhr) {
                        var message = 'There was an error approving the verification.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });

    $('#rejectVerificationBtn').on('click', function() {
        Swal.fire({
            title: 'Reject Company Verification?',
            input: 'textarea',
            inputLabel: 'Rejection reason',
            inputPlaceholder: 'Write the reason for rejection',
            inputAttributes: {
                'aria-label': 'Write the reason for rejection'
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Reject',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Rejection reason is required.';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.company.reject.verification', $company->id) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reason: result.value
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Rejected!',
                                text: 'Company verification has been rejected.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message || 'There was an error rejecting the verification.', 'error');
                        }
                    },
                    error: function(xhr) {
                        var message = 'There was an error rejecting the verification.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
