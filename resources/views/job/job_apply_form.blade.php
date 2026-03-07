@extends('layouts.app')

@section('content') 

<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 

<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Apply for Job')]) 
<!-- Inner Page Title end -->

<div class="listpgWraper">
    <div class="container">
        @include('flash::message')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="userccount" style="background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Job Title -->
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-weight: 600; color: #333; margin-bottom: 10px;">{{__('Apply for this job')}}</h3>
                        <p style="color: #666; font-size: 16px;">{{ $job->title }}</p>
                    </div>

                    @auth
                        @php
                            $user = Auth::user();
                            $existingCvs = $user->profileCvs; // Get the actual CV collection
                            // Check if user has already applied for this job
                            $hasApplied = \App\JobApply::where('job_id', $job->id)
                                ->where('user_id', $user->id)
                                ->exists();
                        @endphp

                        @if($hasApplied)
                            <!-- Already Applied Message -->
                            <div style="text-align: center; padding: 60px 20px;">
                                <div style="width: 80px; height: 80px; background: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                    <i class="fas fa-check-circle" style="font-size: 40px; color: #28a745;"></i>
                                </div>
                                <h4 style="font-weight: 600; color: #333; margin-bottom: 10px;">{{__('Already Applied')}}</h4>
                                <p style="color: #666; margin-bottom: 30px;">{{__('You have already submitted an application for this job.')}}</p>
                                <a href="{{route('job.detail', $job_slug)}}" class="btn" style="background: #007bff; color: #fff; padding: 12px 32px; border-radius: 8px; text-decoration: none;">
                                    <i class="fas fa-arrow-left"></i> {{__('Back to Job Details')}}
                                </a>
                            </div>
                        @else
                    @endauth

                    @if(!Auth::check() || (Auth::check() && !$hasApplied))
                    {!! Form::open(['method' => 'post', 'route' => ['post.job.apply', $job_slug], 'files' => true, 'id' => 'apply-job-form']) !!}

                    @auth
                        @php
                            $existingCvs = $user->profileCvs ?? collect(); // Get the actual CV collection
                        @endphp

                        @if($existingCvs && $existingCvs->count() > 0)
                            <!-- Select Existing CV Section -->
                            <div style="margin-bottom: 30px;">
                                <h5 style="font-weight: 600; color: #333; margin-bottom: 15px;">{{__('Select your CV')}}</h5>
                                <div class="row" id="cv-selection">
                                    @foreach($existingCvs as $cv)
                                        <div class="col-md-6 mb-3">
                                            <div class="cv-card" data-cv-id="{{ $cv->id }}" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s;">
                                                <div style="display: flex; align-items: center;">
                                                    <div style="width: 48px; height: 48px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                        <i class="fas fa-file-pdf" style="font-size: 24px; color: #dc3545;"></i>
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div style="font-weight: 600; color: #333; margin-bottom: 4px;">{{ $cv->cv_title ?? __('Resume') }}</div>
                                                        <div style="font-size: 13px; color: #666;">{{ __('Click to select') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="selected_cv_id" id="selected_cv_id" value="">
                            </div>

                            <!-- OR Divider -->
                            <div style="text-align: center; margin: 30px 0; position: relative;">
                                <div style="border-top: 1px solid #e0e0e0; position: absolute; width: 100%; top: 50%;"></div>
                                <span style="background: #fff; padding: 0 20px; position: relative; color: #999; font-weight: 500;">{{__('or')}}</span>
                            </div>
                        @endif
                    @endauth

                    <!-- Upload New CV Section -->
                    <div style="margin-bottom: 30px;">
                        <h5 style="font-weight: 600; color: #333; margin-bottom: 15px;">{{__('Upload your CV')}}</h5>
                        <div style="border: 2px dashed #d0d0d0; border-radius: 12px; padding: 40px; text-align: center; background: #fafafa;">
                            <div style="margin-bottom: 15px;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #999;"></i>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="cv" class="btn" style="background: #fff; color: #333; border: 1px solid #ddd; padding: 10px 24px; border-radius: 8px; cursor: pointer; display: inline-block;">
                                    <i class="fas fa-upload"></i> {{__('Choose File')}}
                                </label>
                                <input type="file" name="cv" id="cv" class="form-control" style="display: none;" accept=".pdf,.doc,.docx">
                            </div>
                            <div id="file-name" style="color: #666; font-size: 14px;">{{__('No file chosen')}}</div>
                            <div style="color: #999; font-size: 13px; margin-top: 10px;">{{__('PDF, DOC, DOCX (Max 5MB)')}}</div>
                        </div>
                        @if ($errors->has('cv'))
                            <span class="help-block" style="color: #dc3545; font-size: 14px; margin-top: 10px;">
                                <strong>{{ $errors->first('cv') }}</strong>
                            </span>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    <div style="margin-bottom: 30px;">
                        <h5 style="font-weight: 600; color: #333; margin-bottom: 15px;">{{__('Contact Information')}}</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="formrow{{ $errors->has('name') ? ' has-error' : '' }}">
                                    {!! Form::text('name', auth()->user() ? auth()->user()->name : null, array('class'=>'form-control', 'id'=>'name', 'placeholder'=>__('Full Name'), 'required'=>'required', 'style'=>'border-radius: 8px; padding: 12px 16px; border: 1px solid #ddd;')) !!}
                                    @if ($errors->has('name'))
                                        <span class="help-block" style="color: #dc3545; font-size: 14px;">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="formrow{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {!! Form::email('email', auth()->user() ? auth()->user()->email : null, array('class'=>'form-control', 'id'=>'email', 'placeholder'=>__('Email'), 'required'=>'required', 'style'=>'border-radius: 8px; padding: 12px 16px; border: 1px solid #ddd;')) !!}
                                    @if ($errors->has('email'))
                                        <span class="help-block" style="color: #dc3545; font-size: 14px;">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="formrow{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    {!! Form::text('phone', auth()->user() ? auth()->user()->phone : null, array('class'=>'form-control', 'id'=>'phone', 'placeholder'=>__('Phone'), 'required'=>'required', 'style'=>'border-radius: 8px; padding: 12px 16px; border: 1px solid #ddd;')) !!}
                                    @if ($errors->has('phone'))
                                        <span class="help-block" style="color: #dc3545; font-size: 14px;">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cover Letter / Message -->
                    <div style="margin-bottom: 30px;">
                        <h5 style="font-weight: 600; color: #333; margin-bottom: 15px;">{{__('Message')}}</h5>
                        <textarea name="cover_letter" id="cover_letter" rows="6" class="form-control" placeholder="{{__('Write a cover letter or message to the employer...')}}" style="border-radius: 8px; padding: 12px 16px; border: 1px solid #ddd; resize: vertical;" maxlength="2000"></textarea>
                        <div style="text-align: right; color: #999; font-size: 13px; margin-top: 5px;">
                            <span id="char-count">0</span> / 2000 {{__('characters')}}
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div style="margin-bottom: 30px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="accept_terms" id="accept_terms" required style="width: 18px; height: 18px; margin-right: 10px;">
                            <span style="color: #666; font-size: 14px;">
                                {{__('I accept the')}} <a href="#" style="color: #007bff;">{{__('terms and conditions')}}</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div style="text-align: center;">
                        <button type="submit" class="btn" style="background: #007bff; color: #fff; padding: 14px 48px; border-radius: 8px; font-weight: 600; font-size: 16px; border: none; width: 100%;">
                            <i class="fas fa-paper-plane"></i> {{__('Apply Job')}}
                        </button>
                    </div>

                    {!! Form::close() !!}
                    @endif
                    
                    @auth
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // CV Card Selection
    $('.cv-card').on('click', function() {
        $('.cv-card').css({
            'border-color': '#e0e0e0',
            'background': '#fff'
        });
        
        $(this).css({
            'border-color': '#007bff',
            'background': '#f0f8ff'
        });
        
        $('#selected_cv_id').val($(this).data('cv-id'));
        
        // Clear file input if CV is selected
        $('#cv').val('');
        $('#file-name').text('{{__("No file chosen")}}');
    });
    
    // File Upload
    $('#cv').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#file-name').text(fileName);
            
            // Clear CV selection if file is uploaded
            $('.cv-card').css({
                'border-color': '#e0e0e0',
                'background': '#fff'
            });
            $('#selected_cv_id').val('');
        } else {
            $('#file-name').text('{{__("No file chosen")}}');
        }
    });
    
    // Character Counter
    $('#cover_letter').on('input', function() {
        const length = $(this).val().length;
        $('#char-count').text(length);
    });
    
    // Form Validation
    $('#apply-job-form').on('submit', function(e) {
        const selectedCv = $('#selected_cv_id').val();
        const uploadedFile = $('#cv').val();
        
        if (!selectedCv && !uploadedFile) {
            e.preventDefault();
            alert('{{__("Please select an existing CV or upload a new one")}}');
            return false;
        }
    });
});
</script>
@endpush
