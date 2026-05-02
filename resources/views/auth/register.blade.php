@extends('layouts.app')

@section('content') 

<!-- Header start --> 

@include('includes.header') 

<!-- Header end --> 



<div class="authpages">

    <div class="container">

       <div class="row justify-content-center align-items-center">
        <div class="col-lg-5">

        @include('flash::message')        

<style>
.step-wrapper { text-align: center; margin-bottom: 30px; padding-top: 20px; }
.step-container { display: inline-block; position: relative; width: 140px; vertical-align: top; }
.step-indicator { display: inline-block; width: 35px; height: 35px; border-radius: 50%; background: #eee; color: #999; line-height: 35px; font-weight: bold; margin: 0 auto; position: relative; z-index: 2; font-size: 14px;}
.step-indicator.active { background: #0056b3; color: #fff; }
.step-indicator.completed { background: #28a745; color: #fff; }
.step-label { display: block; font-size: 13px; margin-top: 8px; color: #999; font-weight: 500;}
.step-container.active .step-label { color: #0056b3; }
.step-container.completed .step-label { color: #28a745; }
.step-line { position: absolute; top: 17px; left: 50%; width: 100%; height: 2px; background: #eee; z-index: 1; }
.step-container.completed .step-line { background: #28a745; }
.step-container.active .step-line { background: #0056b3; }
.step-container:last-child .step-line { display: none; }

.step-content { display: none; }
.step-content.active { display: block; animation: fadeIn 0.5s; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.step-content h4 { text-align: center; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 22px; }
.step-content p.sub-title { text-align: center; margin-bottom: 25px; color: #777; font-size: 14px; }
.formrow { margin-bottom: 15px; position: relative; text-align: left; }
.formrow label { font-size: 13px; font-weight: 500; color: #555; display: block; margin-bottom: 5px; }
.formrow i.fa-icon { position: absolute; left: 15px; top: 38px; color: #0056b3; }
.form-control.with-icon { padding-left: 40px; background-color: #f0f7ff; border: 1px solid #d1e3ff; height: 45px; border-radius: 6px; }
.form-control.with-icon:focus { background-color: #fff; border-color: #0056b3; box-shadow: 0 0 0 0.2rem rgba(0,86,179,.15); }
select.form-control.with-icon { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
.select-arrow { position: absolute; right: 15px; top: 40px; color: #999; pointer-events: none; }

.action-buttons { display: flex; justify-content: center; align-items: center; margin-top: 30px; gap: 10px; }
.btn-next, .btn-submit { background: #0056b3; color: #fff; border: none; padding: 10px 30px; border-radius: 4px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.btn-next:hover, .btn-submit:hover { background: #004494; color: #fff; }
.btn-back { border: 1px solid #0056b3; color: #0056b3; background: transparent; padding: 9px 15px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; cursor: pointer;}
.btn-back:hover { background: #f0f7ff; }
.login-link { text-align: center; margin-top: 20px; font-size: 14px; color: #555; }
.login-link a { color: #0056b3; font-weight: 600; text-decoration: none; }

/* Radio box styling */
.radio-box-group { display: flex; gap: 10px; }
.radio-box { flex: 1; text-align: center; border: 1px solid #d1e3ff; border-radius: 6px; padding: 10px; cursor: pointer; background: #f0f7ff; font-size: 14px; color: #555; position: relative; transition: 0.3s; }
.radio-box input { display: none; }
.radio-box.active { border-color: #0056b3; background: #e0edff; color: #0056b3; font-weight: 600; }
.radio-box i.fa-check-circle { position: absolute; top: -8px; right: -8px; color: #0056b3; font-size: 18px; display: none; background: #fff; border-radius: 50%; }
.radio-box.active i.fa-check-circle { display: block; }

/* Career level radio buttons */
.career-radio-box { border: 1px solid #d1e3ff; border-radius: 6px; padding: 12px 15px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; }
.career-radio-box input { display: none; }
.career-radio-box .title { font-weight: 600; font-size: 15px; color: #333; display: block; }
.career-radio-box .desc { font-size: 12px; color: #777; }
.career-radio-box .circle { width: 18px; height: 18px; border: 2px solid #ccc; border-radius: 50%; display: inline-block; position: relative; }
.career-radio-box.active { border-color: #0056b3; background: #f0f7ff; }
.career-radio-box.active .circle { border-color: #0056b3; }
.career-radio-box.active .circle:after { content: ''; width: 10px; height: 10px; background: #0056b3; border-radius: 50%; position: absolute; top: 2px; left: 2px; }

/* Select2 overrides */
.select2-container--default .select2-selection--multiple { background-color: #f0f7ff; border: 1px solid #d1e3ff; border-radius: 6px; min-height: 45px; padding: 5px 35px 5px 10px; }
.select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #0056b3; }
.select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #e0edff; border: 1px solid #cce0ff; color: #0056b3; border-radius: 4px; padding: 4px 8px; font-size: 13px; margin-top: 5px; }
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #0056b3; margin-right: 5px; }
.select2-icon { position: absolute; left: 15px; top: 38px; color: #0056b3; z-index: 10; }

/* Age validation styling */
.help-block.text-success { color: #28a745 !important; font-size: 12px; }
.help-block.text-danger { color: #dc3545 !important; font-size: 12px; }
#age-validation-message, #age-success-message { 
    animation: fadeIn 0.3s ease-in-out; 
    padding: 8px 12px; 
    border-radius: 4px; 
    background: rgba(255,255,255,0.9);
}
#age-validation-message { border-left: 3px solid #dc3545; background-color: #f8d7da; }
#age-success-message { border-left: 3px solid #28a745; background-color: #d4edda; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="useraccountwrap" style="background: transparent; box-shadow: none;">
    <div class="userccount whitebg" style="border-radius: 10px; padding: 30px 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        
        <h3 class="text-center" style="font-size: 24px; font-weight: 600; color: #333; margin-bottom: 20px;">{{__('Welcome')}}</h3>
        
        <div class="step-wrapper">
            <div class="step-container active" id="indicator-1">
                <div class="step-line"></div>
                <div class="step-indicator">1</div>
                <span class="step-label">{{__('Information')}}</span>
            </div>
            <div class="step-container" id="indicator-2">
                <div class="step-line"></div>
                <div class="step-indicator">2</div>
                <span class="step-label">{{__('Preference')}}</span>
            </div>
            <div class="step-container" id="indicator-3">
                <div class="step-indicator">3</div>
                <span class="step-label">{{__('Sign Up')}}</span>
            </div>
        </div>

        <form class="form-horizontal mt-3" method="POST" action="{{ route('register') }}" id="multiStepForm" onsubmit="return validateFormBeforeSubmit()">
            @csrf
            <input type="hidden" name="candidate_or_employer" value="candidate" />

            <!-- Step 1: Personal Information -->
            <div class="step-content active" id="step-1">
                <h4>{{__('Personal Information')}}</h4>
                <p class="sub-title">{{__('Provide your basic information and DOB')}}</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="formrow{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label>{{__('First Name')}} *</label>
                            <i class="fas fa-user fa-icon"></i>
                            <input type="text" name="first_name" class="form-control with-icon" required placeholder="{{__('First Name')}}" value="{{old('first_name')}}">
                            @if ($errors->has('first_name')) <span class="help-block text-danger"> <strong>{{ $errors->first('first_name') }}</strong> </span> @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="formrow{{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label>{{__('Last Name')}} *</label>
                            <i class="fas fa-user fa-icon"></i>
                            <input type="text" name="last_name" class="form-control with-icon" required placeholder="{{__('Last Name')}}" value="{{old('last_name')}}">
                            @if ($errors->has('last_name')) <span class="help-block text-danger"> <strong>{{ $errors->first('last_name') }}</strong> </span> @endif
                        </div>
                    </div>
                </div>

                <div class="formrow{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label>{{__('Phone Number')}} *</label>
                    <i class="fas fa-phone fa-icon"></i>
                    <input type="text" name="phone" class="form-control with-icon" required placeholder="{{__('Phone Number')}}" value="{{old('phone')}}">
                    @if ($errors->has('phone')) <span class="help-block text-danger"> <strong>{{ $errors->first('phone') }}</strong> </span> @endif
                </div>

                <div class="formrow{{ $errors->has('date_of_birth') ? ' has-error' : '' }}">
                    <label>{{__('Date of Birth')}} *</label>
                    <i class="far fa-calendar-alt fa-icon"></i>
                    <input type="date" name="date_of_birth" class="form-control with-icon" required placeholder="{{__('Date of Birth')}}" value="{{old('date_of_birth')}}" onchange="validateAge(this)" max="{{ date('Y-m-d') }}">
                    @if ($errors->has('date_of_birth')) <span class="help-block text-danger"> <strong>{{ $errors->first('date_of_birth') }}</strong> </span> @endif
                    <div id="age-validation-message" style="display: none; margin-top: 5px;">
                        <span class="help-block text-danger">
                            <strong><i class="fas fa-exclamation-triangle"></i> {{__('You must be at least 18 years old to register')}}</strong>
                        </span>
                    </div>
                    <div id="age-success-message" style="display: none; margin-top: 5px;">
                        <span class="help-block text-success">
                            <strong><i class="fas fa-check-circle"></i> <span id="calculated-age"></span></strong>
                        </span>
                    </div>
                </div>

                <div class="formrow{{ $errors->has('gender_id') ? ' has-error' : '' }}">
                    <label>{{__('Gender')}}</label>
                    <div class="radio-box-group">
                        @php
                        $i=1;
                        @endphp
                        @foreach($genders as $key => $gender)
                            @continue($i > 3)
                            @php
                                $i++;
                            @endphp
                            <label class="radio-box {{ old('gender_id') == $key ? 'active' : '' }}" onclick="selectGender(this)">
                                <input type="radio" name="gender_id" value="{{$key}}" {{ old('gender_id') == $key ? 'checked' : '' }}>
                                {{$gender}}
                                <i class="fas fa-check-circle"></i>
                            </label>
                        @endforeach
                    </div>
                    @if ($errors->has('gender_id')) <span class="help-block text-danger"> <strong>{{ $errors->first('gender_id') }}</strong> </span> @endif
                    <p style="font-size: 11px; color: #999; margin-top: 5px;"><i class="fas fa-lock"></i> {{__('Your information is safe and confidential.')}}</p>
                </div>
                
                <div class="formrow{{ $errors->has('street_address') ? ' has-error' : '' }}">
                    <label>{{__('Current Address')}}</label>
                    <i class="far fa-map fa-icon"></i>
                    <input type="text" name="street_address" class="form-control with-icon" placeholder="{{__('Current Address')}}" value="{{old('street_address')}}">
                    @if ($errors->has('street_address')) <span class="help-block text-danger"> <strong>{{ $errors->first('street_address') }}</strong> </span> @endif
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-next" onclick="nextStep(2)">{{__('Next')}}</button>
                </div>
            </div>

            <!-- Step 2: Job Preferences -->
            <div class="step-content" id="step-2">
                <h4>{{__('Job Preferences')}}</h4>
                <p class="sub-title">{{__('Choose your preferences to get matching job recommendations')}}</p>

                <div class="formrow{{ $errors->has('job_title') ? ' has-error' : '' }}">
                    <label>{{__('Enter Preferred Job Title')}}</label>
                    <i class="fas fa-briefcase select2-icon"></i>
                    <select name="job_title[]" class="form-control select2-multiple" multiple="multiple" style="width: 100%;" data-placeholder="{{__('e.g. Dance Instructor, QA Engineer')}}">
                        @if(old('job_title'))
                            @foreach(old('job_title') as $oldTitle)
                                <option value="{{$oldTitle}}" selected>{{$oldTitle}}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('job_title')) <span class="help-block text-danger"> <strong>{{ $errors->first('job_title') }}</strong> </span> @endif
                </div>

                <div class="formrow{{ $errors->has('job_category_id') ? ' has-error' : '' }}">
                    <label>{{__('Choose Appropriate Job Category')}}</label>
                    <i class="fas fa-list-alt fa-icon"></i>
                    <select name="job_category_id" class="form-control with-icon">
                        <option value="">{{__('Choose Appropriate Job Category')}}</option>
                        @foreach($jobCategories as $key => $category)
                            <option value="{{$key}}" {{old('job_category_id') == $key ? 'selected' : ''}}>{{$category}}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                    @if ($errors->has('job_category_id')) <span class="help-block text-danger"> <strong>{{ $errors->first('job_category_id') }}</strong> </span> @endif
                    <p style="font-size: 11px; color: #999; margin-top: 5px;"><i class="fas fa-info-circle"></i> {{__('You\'ll now see matching jobs aligned with this input.')}}</p>
                </div>
                
                <div class="formrow mt-4{{ $errors->has('career_level_id') ? ' has-error' : '' }}">
                    <label style="margin-bottom: 15px; font-weight: 600; color: #333; text-align: center;">{{__('Choose the preferred level that best matches your work experience')}}</label>
                    
                    @foreach($careerLevels as $key => $level)
                        <label class="career-radio-box {{ old('career_level_id') == $key ? 'active' : '' }}" onclick="selectCareerLevel(this)">
                            <div>
                                <span class="title">{{$level}}</span>
                                <span class="desc">{{__('Select if this matches your experience')}}</span>
                            </div>
                            <input type="radio" name="career_level_id" value="{{$key}}" {{ old('career_level_id') == $key ? 'checked' : '' }}>
                            <div class="circle"></div>
                        </label>
                    @endforeach
                    
                    @if ($errors->has('career_level_id')) <span class="help-block text-danger"> <strong>{{ $errors->first('career_level_id') }}</strong> </span> @endif
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-back" onclick="prevStep(1)"><i class="fas fa-arrow-left"></i></button>
                    <button type="button" class="btn-next" onclick="nextStep(3)">{{__('Next')}}</button>
                </div>
            </div>

            <!-- Step 3: Login Credentials -->
            <div class="step-content" id="step-3">
                <h4>{{__('Account Details')}}</h4>
                <p class="sub-title">{{__('Set up your login credentials')}}</p>

                <div class="formrow{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label>{{__('Email Address')}} *</label>
                    <i class="fas fa-envelope fa-icon"></i>
                    <input type="email" name="email" class="form-control with-icon" required placeholder="{{__('Email')}}" value="{{old('email')}}">
                    @if ($errors->has('email')) <span class="help-block text-danger"> <strong>{{ $errors->first('email') }}</strong> </span> @endif
                </div>

                <div class="formrow{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label>{{__('Password')}} *</label>
                    <i class="fas fa-lock fa-icon"></i>
                    <input type="password" name="password" class="form-control with-icon" required placeholder="{{__('Password')}}">
                    @if ($errors->has('password')) <span class="help-block text-danger"> <strong>{{ $errors->first('password') }}</strong> </span> @endif
                </div>

                <div class="formrow{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label>{{__('Confirm Password')}} *</label>
                    <i class="fas fa-lock fa-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control with-icon" required placeholder="{{__('Password Confirmation')}}">
                    @if ($errors->has('password_confirmation')) <span class="help-block text-danger"> <strong>{{ $errors->first('password_confirmation') }}</strong> </span> @endif
                </div>

                <!-- Referral Code Section -->
                <div class="formrow" style="margin-top:20px;">
                    <label style="font-weight: 500; margin-bottom: 10px; display: block;">{{__('Do you have an invitation code?')}}</label>
                    <div style="margin-bottom: 10px;">
                        <label style="font-weight: normal; margin-right: 15px; cursor: pointer;">
                            <input type="radio" name="has_referral_code" value="yes" id="hasReferralYes" {{ (old('has_referral_code') == 'yes' || request('ref') || session('user_referral_code')) ? 'checked' : '' }} style="margin-right: 5px;">
                            {{__('Yes')}}
                        </label>
                        <label style="font-weight: normal; cursor: pointer;">
                            <input type="radio" name="has_referral_code" value="no" id="hasReferralNo" {{ (old('has_referral_code', 'no') == 'no' && !request('ref') && !session('user_referral_code')) ? 'checked' : '' }} style="margin-right: 5px;">
                            {{__('No')}}
                        </label>
                    </div>
                    <div id="referralCodeField" style="display: {{ (old('has_referral_code') == 'yes' || request('ref') || session('user_referral_code')) ? 'block' : 'none' }};">
                        <input type="text" name="ref" id="referralCodeInput" class="form-control" placeholder="{{__('Enter invitation code')}}" value="{{ old('ref', request('ref') ?: session('user_referral_code')) }}">
                        @if ($errors->has('ref')) <span class="help-block text-danger"> <strong>{{ $errors->first('ref') }}</strong> </span> @endif
                    </div>
                </div>

                <div class="formrow{{ $errors->has('is_subscribed') ? ' has-error' : '' }}">
                    <?php $is_checked = old('is_subscribed', 1) ? 'checked="checked"' : ''; ?>
                    <label style="cursor: pointer; font-weight:normal; font-size:13px;">
                        <input type="checkbox" value="1" name="is_subscribed" {{$is_checked}} style="vertical-align: middle; margin-right: 5px;" />
                        {{__('Subscribe to Newsletter')}}
                    </label>
                    @if ($errors->has('is_subscribed')) <span class="help-block text-danger"> <strong>{{ $errors->first('is_subscribed') }}</strong> </span> @endif
                </div>

                <div class="formrow{{ $errors->has('terms_of_use') ? ' has-error' : '' }}" style="font-size: 13px; color:#555; text-align:left;">
                    <label style="cursor: pointer; font-weight:normal;">
                        <input type="checkbox" value="1" name="terms_of_use" required style="vertical-align: middle; margin-right: 5px;"/>
                        {!! __('By clicking on \'Create Account\' below you are agreeing to the <a href=":url" target="_blank" style="color:#0056b3; font-weight:600;">terms</a> and <a href=":privacy" target="_blank" style="color:#0056b3; font-weight:600;">privacy</a> of :site!', ['url' => url('cms/terms-of-use'), 'privacy' => url('cms/privacy-policy'), 'site' => config('app.name')]) !!}
                    </label>
                    @if ($errors->has('terms_of_use')) <div class="help-block text-danger mt-1"> <strong>{{ $errors->first('terms_of_use') }}</strong> </div> @endif
                </div>

                <div class="form-group text-center mx-auto mobile-padding-no mb-3 {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                    {!! app('captcha')->display() !!}
                    @if ($errors->has('g-recaptcha-response')) <span class="help-block text-danger"> <strong>{{ $errors->first('g-recaptcha-response') }}</strong> </span> @endif
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-back" onclick="prevStep(2)"><i class="fas fa-arrow-left"></i></button>
                    <button type="submit" class="btn-submit">{{__('Create Account')}}</button>
                </div>
            </div>

        </form>

        <div class="login-link">
            {{__('Already have an account?')}} <a href="{{route('login')}}">{{__('Login here')}}</a>
        </div>

    </div>
</div>
        </div>
       </div>
    </div>
</div>

@include('includes.footer')

@push('scripts')
<!-- Select2 CSS/JS if not already included globally (Usually Carejobber has it globally, but just in case) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
function selectGender(element) {
    document.querySelectorAll('.radio-box').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function selectCareerLevel(element) {
    document.querySelectorAll('.career-radio-box').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function validateStep(step) {
    let isValid = true;
    const currentStepDiv = document.getElementById('step-' + step);
    const requiredInputs = currentStepDiv.querySelectorAll('input[required], select[required]');
    
    currentStepDiv.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '#d1e3ff');

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = 'red';
        }
    });

    // Additional age validation for date of birth in step 1
    if (step === 1) {
        const dobInput = currentStepDiv.querySelector('input[name="date_of_birth"]');
        if (dobInput && dobInput.value) {
            // Trigger the age validation function
            validateAge(dobInput);
            
            // Check if age validation failed (error message is visible)
            const errorMessage = document.getElementById('age-validation-message');
            if (errorMessage && errorMessage.style.display !== 'none') {
                isValid = false;
            }
        }
    }

    return isValid;
}

function nextStep(step) {
    if(!validateStep(step - 1)) return;

    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.getElementById('step-' + step).classList.add('active');

    const prevIndicator = document.getElementById('indicator-' + (step - 1));
    prevIndicator.classList.remove('active');
    prevIndicator.classList.add('completed');
    prevIndicator.querySelector('.step-indicator').innerHTML = '<i class="fas fa-check"></i>';

    const currIndicator = document.getElementById('indicator-' + step);
    currIndicator.classList.add('active');
}

function prevStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.getElementById('step-' + step).classList.add('active');

    const nextIndicator = document.getElementById('indicator-' + (step + 1));
    nextIndicator.classList.remove('active');
    nextIndicator.classList.remove('completed');
    nextIndicator.querySelector('.step-indicator').innerHTML = (step + 1);

    const currIndicator = document.getElementById('indicator-' + step);
    currIndicator.classList.add('active');
    currIndicator.classList.remove('completed');
    currIndicator.querySelector('.step-indicator').innerHTML = step;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2-multiple').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: "{{__('e.g. Dance Instructor, QA Engineer')}}"
    });

    const hasReferralYes = document.getElementById('hasReferralYes');
    const hasReferralNo = document.getElementById('hasReferralNo');
    const referralCodeField = document.getElementById('referralCodeField');
    const referralCodeInput = document.getElementById('referralCodeInput');
    
    function toggleReferralField() {
        if (hasReferralYes.checked) {
            referralCodeField.style.display = 'block';
            referralCodeInput.setAttribute('required', 'required');
        } else {
            referralCodeField.style.display = 'none';
            referralCodeInput.removeAttribute('required');
            referralCodeInput.value = '';
        }
    }
    
    if(hasReferralYes) hasReferralYes.addEventListener('change', toggleReferralField);
    if(hasReferralNo) hasReferralNo.addEventListener('change', toggleReferralField);
    
    // Validate age on page load if date is already filled
    const dobInput = document.querySelector('input[name="date_of_birth"]');
    if (dobInput && dobInput.value) {
        validateAge(dobInput);
    }
    
    // Auto-open step with errors if validation failed
    @if($errors->any())
        @if($errors->has('email') || $errors->has('password') || $errors->has('terms_of_use') || $errors->has('g-recaptcha-response'))
            nextStep(2);
            nextStep(3);
        @elseif($errors->has('job_title') || $errors->has('job_category_id') || $errors->has('career_level_id'))
            nextStep(2);
        @endif
    @endif
});

// Age validation function that triggers on date change
function validateAge(input) {
    const dobValue = input.value;
    const errorMessage = document.getElementById('age-validation-message');
    const successMessage = document.getElementById('age-success-message');
    const calculatedAgeSpan = document.getElementById('calculated-age');
    
    // Reset messages and input styling
    errorMessage.style.display = 'none';
    successMessage.style.display = 'none';
    input.style.borderColor = '#d1e3ff';
    
    if (!dobValue) {
        return;
    }
    
    const dob = new Date(dobValue);
    const today = new Date();
    
    // Check if date is in the future
    if (dob > today) {
        input.style.borderColor = 'red';
        errorMessage.querySelector('strong').innerHTML = '<i class="fas fa-exclamation-triangle"></i> {{__("Date of birth cannot be in the future")}}';
        errorMessage.style.display = 'block';
        return;
    }
    
    // Calculate age
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    if (age < 18) {
        input.style.borderColor = 'red';
        errorMessage.querySelector('strong').innerHTML = '<i class="fas fa-exclamation-triangle"></i> {{__("You must be at least 18 years old to register")}}';
        errorMessage.style.display = 'block';
    } else {
        input.style.borderColor = '#28a745';
        calculatedAgeSpan.textContent = '{{__("Age")}}: ' + age + ' {{__("years old - Valid")}}';
        successMessage.style.display = 'block';
    }
}

// Final form validation before submission
function validateFormBeforeSubmit() {
    const dobInput = document.querySelector('input[name="date_of_birth"]');
    if (dobInput && dobInput.value) {
        validateAge(dobInput);
        const errorMessage = document.getElementById('age-validation-message');
        if (errorMessage && errorMessage.style.display !== 'none') {
            alert('{{__("Please correct the date of birth error before submitting.")}}');
            return false;
        }
    }
    return true;
}
</script>
@endpush

@endsection 