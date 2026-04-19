@extends('admin.layouts.admin_layout')
@push('css')
<style>
    .verification-card { border-radius: 8px; transition: all 0.3s ease; }
    .verification-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .company-logo-small { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-verified { background-color: #28a745; color: #fff; }
    .badge-rejected { background-color: #dc3545; color: #fff; }
    .doc-count { font-size: 0.875rem; color: #6c757d; }
    .verification-doc-links a { margin-right: 6px; margin-top: 6px; }
    .verification-reason-box { min-width: 220px; }
</style>
@endpush
@section('content')
<div class="page-bar mb-3">
    <ul class="page-breadcrumb mb-0">
        <li><a href="{{ route('admin.home') }}">Home</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><a href="{{ route('list.companies') }}">Companies</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><span>Verification Requests</span></li>
    </ul>
</div>

@include('flash::message')

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning bg-opacity-10 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center">
                            <i class="ri ri-time-line fs-3 text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0">{{ $pendingVerifications->count() }}</h4>
                        <p class="text-muted mb-0">Pending Verifications</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success bg-opacity-10 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success bg-opacity-25 d-flex align-items-center justify-content-center">
                            <i class="ri ri-checkbox-circle-line fs-3 text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0">{{ $recentlyVerified->count() }}</h4>
                        <p class="text-muted mb-0">Recently Verified</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger bg-opacity-10 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-danger bg-opacity-25 d-flex align-items-center justify-content-center">
                            <i class="ri ri-close-circle-line fs-3 text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-0">{{ $recentlyRejected->count() }}</h4>
                        <p class="text-muted mb-0">Recently Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri ri-time-line me-1"></i> Pending Verification Requests</h5>
    </div>
    <div class="card-body">
        @if($pendingVerifications->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Documents</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingVerifications as $company)
                    @php $firstDoc = $company->verificationDocuments->first(); @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    {!! $company->printCompanyImage(60, 60) !!}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $company->name }}</h6>
                                    <small class="text-muted">{{ $company->getIndustry('industry') ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $company->email }}</td>
                        <td>
                            @include('admin.company.partials.verification_documents')
                        </td>
                        <td>
                            @if($firstDoc)
                                {{ $firstDoc->uploaded_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $firstDoc->uploaded_at->diffForHumans() }}</small>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-pending">
                                <i class="ri ri-time-line me-1"></i>Pending Review
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.public.company', $company->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri ri-eye-line me-1"></i>View Profile
                            </a>
                            <form action="{{ route('admin.company.approve.verification', $company->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ri ri-checkbox-circle-line me-1"></i>Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.company.reject.verification', $company->id) }}" method="POST" class="d-inline-flex align-items-start gap-2">
                                @csrf
                                <textarea name="reason" class="form-control form-control-sm verification-reason-box" rows="2" placeholder="Write rejection reason" required></textarea>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="ri ri-close-circle-line me-1"></i>Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="ri ri-checkbox-circle-line text-success" style="font-size: 4rem;"></i>
            <h5 class="mt-3">No Pending Verifications</h5>
            <p class="text-muted">No company is currently waiting for review.</p>
        </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri ri-checkbox-circle-line me-1"></i> Recently Verified Companies</h5>
    </div>
    <div class="card-body">
        @if($recentlyVerified->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Documents</th>
                        <th>Verified Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentlyVerified as $company)
                    <tr>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->email }}</td>
                        <td>
                            @include('admin.company.partials.verification_documents')
                        </td>
                        <td>
                            {{ $company->verified_at->format('M d, Y') }}<br>
                            <small class="text-muted">{{ $company->verified_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge badge-verified">
                                <i class="ri ri-checkbox-circle-line me-1"></i>Verified
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="ri ri-information-line text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">No Recently Verified Companies</h5>
            <p class="text-muted">Verified companies will appear here.</p>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri ri-close-circle-line me-1"></i> Recently Rejected Companies</h5>
    </div>
    <div class="card-body">
        @if($recentlyRejected->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Documents</th>
                        <th>Reviewed</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentlyRejected as $company)
                    <tr>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->email }}</td>
                        <td>
                            @include('admin.company.partials.verification_documents')
                        </td>
                        <td>
                            @if($company->verification_reviewed_at)
                                {{ $company->verification_reviewed_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $company->verification_reviewed_at->diffForHumans() }}</small>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $company->verification_rejection_reason ?: '—' }}</td>
                        <td>
                            <span class="badge badge-rejected">
                                <i class="ri ri-close-circle-line me-1"></i>Rejected
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.public.company', $company->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri ri-eye-line me-1"></i>View Profile
                            </a>
                            <form action="{{ route('admin.company.approve.verification', $company->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ri ri-checkbox-circle-line me-1"></i>Approve
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="ri ri-information-line text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">No Rejected Companies</h5>
            <p class="text-muted">Rejected verification requests will appear here.</p>
        </div>
        @endif
    </div>
</div>
@endsection
