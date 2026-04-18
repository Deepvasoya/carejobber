@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Document Verification')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row" style="gap: 20px; margin: 0;">
            @include('includes.company_dashboard_menu')

            <div class="col-lg-7" style="flex: 1; min-width: 0;"> 
                <div class="userccount">
                    <div class="formpanel mt0"> 
                        @include('flash::message')

                        @if($company->isVerified())
                            <div class="alert alert-success">
                                {{ __('Your company is verified.') }}
                            </div>
                        @elseif($company->verification_status === 'rejected')
                            <div class="alert alert-danger">
                                {{ __('Your verification was rejected.') }}
                                @if($company->verification_rejection_reason)
                                    <br><strong>{{ __('Reason:') }}</strong> {{ $company->verification_rejection_reason }}
                                @endif
                            </div>
                        @elseif($company->hasPendingVerification())
                            <div class="alert alert-warning">
                                {{ __('Your documents are under review by admin.') }}
                            </div>
                        @endif

                        <div class="alert alert-info">
                            {{ __('Upload your business registration document to begin company verification. Job posting and candidate resume access remain locked until admin approval.') }}
                        </div>
                        
                        <h5>{{__('Upload Verification Documents')}}</h5>
                        <p>{{__('Each document has its own upload button so you can submit or replace them separately.')}}</p>

                        @include('company.verification._upload_cards', ['latestVerificationDocuments' => $latestVerificationDocuments])
                    </div>

                    <div class="formpanel mt-4">
                        <h5>{{ __('Uploaded Document History') }}</h5>
                        @if($documents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Document Type') }}</th>
                                            <th>{{ __('File Name') }}</th>
                                            <th>{{ __('Uploaded') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documents as $document)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_', ' ', $document->document_type)) }}</td>
                                                <td>{{ $document->original_filename }}</td>
                                                <td>
                                                    {{ $document->uploaded_at->format('M d, Y h:i A') }}
                                                    <br>
                                                    <small class="text-muted">{{ $document->uploaded_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('company.verification.document.show', $document->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('No verification documents uploaded yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection

@push('styles')
<style type="text/css">
    .verification-upload-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }
    .verification-upload-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        background: #fff;
    }
    .verification-upload-card__meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
        color: #6b7280;
        margin: 15px 0;
    }
    .verification-upload-card__current {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
    }
    .verification-upload-card__actions {
        margin-top: 15px;
    }
    .verification-required {
        color: #dc2626;
    }
    .verification-optional {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }
</style>
@endpush
