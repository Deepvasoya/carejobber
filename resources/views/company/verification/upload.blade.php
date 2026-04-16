@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Document Verification')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8"> 
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
                        
                        <h5>{{__('Upload Verification Documents')}}</h5>
                        <p>{{__('Please upload the required documents to verify your company profile. This will help build trust with job seekers.')}}</p>
                        
                        {!! Form::open(['method' => 'POST', 'route' => 'company.verification.store', 'class' => 'form', 'files' => true]) !!}
                        
                        <div class="row">
                            <!-- Business Registration Document -->
                            <div class="col-md-12">
                                <div class="upload-section">
                                    <h6>{{__('Business Registration Document')}} <span style="color: red;">*</span></h6>
                                    <p class="text-muted small">{{__('Please ensure documents are clear, readable, well-lit, and in focus')}}</p>
                                    <p class="text-muted small"><strong>{{__('Supported formats:')}}</strong> PNG, JPG, JPEG, PDF | <strong>{{__('Max size:')}}</strong> 2MB</p>
                                    
                                    <div class="formrow {!! APFrmErrHelp::hasError($errors, 'business_registration') !!}">
                                        <input type="file" name="business_registration" id="business_registration" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                                        {!! APFrmErrHelp::showErrors($errors, 'business_registration') !!}
                                    </div>
                                </div>
                                <hr>
                            </div>

                            <!-- Tax Document -->
                            <div class="col-md-12">
                                <div class="upload-section">
                                    <h6>{{__('Tax Document')}} <span class="text-muted">({{__('Optional')}})</span></h6>
                                    <p class="text-muted small">{{__('Please ensure documents are clear, readable, well-lit, and in focus')}}</p>
                                    <p class="text-muted small"><strong>{{__('Supported formats:')}}</strong> PNG, JPG, JPEG, PDF | <strong>{{__('Max size:')}}</strong> 2MB</p>
                                    
                                    <div class="formrow {!! APFrmErrHelp::hasError($errors, 'tax_document') !!}">
                                        <input type="file" name="tax_document" id="tax_document" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                                        {!! APFrmErrHelp::showErrors($errors, 'tax_document') !!}
                                    </div>
                                </div>
                                <hr>
                            </div>

                            <!-- Establishment Photo -->
                            <div class="col-md-12">
                                <div class="upload-section">
                                    <h6>{{__('Establishment Photo')}} <span class="text-muted">({{__('Optional')}})</span></h6>
                                    <p class="text-muted small">{{__('Please ensure documents are clear, readable, well-lit, and in focus')}}</p>
                                    <p class="text-muted small"><strong>{{__('Supported formats:')}}</strong> PNG, JPG, JPEG, PDF | <strong>{{__('Max size:')}}</strong> 2MB</p>
                                    
                                    <div class="formrow {!! APFrmErrHelp::hasError($errors, 'establishment_photo') !!}">
                                        <input type="file" name="establishment_photo" id="establishment_photo" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                                        {!! APFrmErrHelp::showErrors($errors, 'establishment_photo') !!}
                                    </div>
                                </div>
                                <hr>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <div class="formrow">
                                    <button type="submit" class="btn">{{__('Save and Continue')}} <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        {!! Form::close() !!}
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
    .upload-section {
        margin-bottom: 20px;
    }
    .upload-section h6 {
        font-weight: 600;
        margin-bottom: 10px;
    }
    .upload-section .text-muted {
        margin-bottom: 8px;
    }
    .upload-section .formrow {
        margin-top: 10px;
    }
</style>
@endpush
