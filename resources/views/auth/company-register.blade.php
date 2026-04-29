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
                        .step-wrapper {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-top: 20px;
                        }

                        .step-container {
                            display: inline-block;
                            position: relative;
                            width: 140px;
                            vertical-align: top;
                        }

                        .step-indicator {
                            display: inline-block;
                            width: 35px;
                            height: 35px;
                            border-radius: 50%;
                            background: #eee;
                            color: #999;
                            line-height: 35px;
                            font-weight: bold;
                            margin: 0 auto;
                            position: relative;
                            z-index: 2;
                            font-size: 14px;
                        }

                        .step-indicator.active {
                            background: #0056b3;
                            color: #fff;
                        }

                        .step-indicator.completed {
                            background: #28a745;
                            color: #fff;
                        }

                        .step-label {
                            display: block;
                            font-size: 13px;
                            margin-top: 8px;
                            color: #999;
                            font-weight: 500;
                        }

                        .step-container.active .step-label {
                            color: #0056b3;
                        }

                        .step-container.completed .step-label {
                            color: #28a745;
                        }

                        .step-line {
                            position: absolute;
                            top: 17px;
                            left: 50%;
                            width: 100%;
                            height: 2px;
                            background: #eee;
                            z-index: 1;
                        }

                        .step-container.completed .step-line {
                            background: #28a745;
                        }

                        .step-container.active .step-line {
                            background: #0056b3;
                        }

                        .step-container:last-child .step-line {
                            display: none;
                        }

                        .step-content {
                            display: none;
                        }

                        .step-content.active {
                            display: block;
                            animation: fadeIn 0.5s;
                        }

                        @keyframes fadeIn {
                            from {
                                opacity: 0;
                                transform: translateY(10px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }

                        .step-content h4 {
                            text-align: center;
                            margin-bottom: 5px;
                            font-weight: 600;
                            color: #333;
                            font-size: 22px;
                        }

                        .step-content p.sub-title {
                            text-align: center;
                            margin-bottom: 25px;
                            color: #777;
                            font-size: 14px;
                        }

                        .formrow {
                            margin-bottom: 15px;
                            position: relative;
                        }

                        .formrow i {
                            position: absolute;
                            left: 15px;
                            top: 12px;
                            color: #0056b3;
                        }

                        .form-control.with-icon {
                            padding-left: 40px;
                            background-color: #f0f7ff;
                            border: 1px solid #d1e3ff;
                            height: 45px;
                            border-radius: 6px;
                        }

                        .form-control.with-icon:focus {
                            background-color: #fff;
                            border-color: #0056b3;
                            box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, .15);
                        }

                        select.form-control.with-icon {
                            -webkit-appearance: none;
                            -moz-appearance: none;
                            appearance: none;
                        }

                        .select-arrow {
                            position: absolute;
                            right: 15px;
                            top: 15px;
                            color: #999;
                            pointer-events: none;
                        }

                        .action-buttons {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            margin-top: 30px;
                            gap: 10px;
                        }

                        .btn-next,
                        .btn-submit {
                            background: #0056b3;
                            color: #fff;
                            border: none;
                            padding: 10px 30px;
                            border-radius: 4px;
                            font-weight: 600;
                            cursor: pointer;
                            transition: 0.3s;
                        }

                        .btn-next:hover,
                        .btn-submit:hover {
                            background: #004494;
                            color: #fff;
                        }

                        .btn-back {
                            border: 1px solid #0056b3;
                            color: #0056b3;
                            background: transparent;
                            padding: 9px 15px;
                            border-radius: 4px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            transition: 0.3s;
                            cursor: pointer;
                        }

                        .btn-back:hover {
                            background: #f0f7ff;
                        }

                        .login-link {
                            text-align: center;
                            margin-top: 20px;
                            font-size: 14px;
                            color: #555;
                        }

                        .login-link a {
                            color: #0056b3;
                            font-weight: 600;
                            text-decoration: none;
                        }
                    </style>

                    <div class="useraccountwrap" style="background: transparent; box-shadow: none;">
                        <div class="userccount whitebg"
                            style="border-radius: 10px; padding: 30px 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">

                            <h3 class="text-center"
                                style="font-size: 24px; font-weight: 600; color: #333; margin-bottom: 20px;">
                                {{__('3 easy steps of Employer Registration')}}
                            </h3>

                            <div class="step-wrapper">
                                <div class="step-container active" id="indicator-1">
                                    <div class="step-line"></div>
                                    <div class="step-indicator">1</div>
                                    <span class="step-label">{{__('Organization')}}</span>
                                </div>
                                <div class="step-container" id="indicator-2">
                                    <div class="step-line"></div>
                                    <div class="step-indicator">2</div>
                                    <span class="step-label">{{__('Correspondence')}}</span>
                                </div>
                                <div class="step-container" id="indicator-3">
                                    <div class="step-indicator">3</div>
                                    <span class="step-label">{{__('Sign Up')}}</span>
                                </div>
                            </div>

                            <form class="form-horizontal mt-3" method="POST" action="{{ route('company.register') }}"
                                id="multiStepForm">
                                {{ csrf_field() }}
                                <input type="hidden" name="candidate_or_employer" value="employer" />

                                <!-- Step 1: Organization Information -->
                                <div class="step-content active" id="step-1">
                                    <h4>{{__('Organization Information')}}</h4>
                                    <p class="sub-title">{{__('Provide your organization\'s brief')}}</p>

                                    <div class="formrow{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <i class="fas fa-building"></i>
                                        <input type="text" name="name" class="form-control with-icon" required
                                            placeholder="{{__('Organization Name')}}" value="{{old('name')}}">
                                        @if ($errors->has('name')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('name') }}</strong> </span> @endif
                                    </div>

                                    <div class="formrow{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <i class="fas fa-phone-alt"></i>
                                        <input type="text" name="phone" class="form-control with-icon" required
                                            placeholder="{{__('Organization Number')}}" value="{{old('phone')}}">
                                        @if ($errors->has('phone')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('phone') }}</strong> </span> @endif
                                    </div>

                                    <div class="formrow{{ $errors->has('ownership_type_id') ? ' has-error' : '' }}">
                                        <i class="fas fa-industry"></i>
                                        <select name="ownership_type_id" class="form-control with-icon" required>
                                            <option value="">{{__('Select Industry Type')}}</option>
                                            @foreach($ownershipTypes as $key => $type)
                                                <option value="{{$key}}" {{old('ownership_type_id') == $key ? 'selected' : ''}}>
                                                    {{$type}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down select-arrow"></i>
                                        @if ($errors->has('ownership_type_id')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('ownership_type_id') }}</strong> </span> @endif
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn-next"
                                            onclick="nextStep(2)">{{__('Continue')}}</button>
                                    </div>
                                </div>

                                <!-- Step 2: Correspondence Detail -->
                                <div class="step-content" id="step-2">
                                    <h4>{{__('Correspondence Detail')}}</h4>
                                    <p class="sub-title">{{__('Provide Recruitment focal-person\'s contact detail')}}</p>

                                    <div class="formrow{{ $errors->has('contact_name') ? ' has-error' : '' }}">
                                        <i class="fas fa-user-tie"></i>
                                        <input type="text" name="contact_name" class="form-control with-icon" required
                                            placeholder="{{__('Contact Person\'s Name')}}" value="{{old('contact_name')}}">
                                        @if ($errors->has('contact_name')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('contact_name') }}</strong> </span> @endif
                                    </div>

                                    <div class="formrow{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
                                        <i class="fas fa-phone-volume"></i>
                                        <input type="text" name="contact_phone" class="form-control with-icon" required
                                            placeholder="{{__('Contact Person\'s Mobile Number')}}"
                                            value="{{old('contact_phone')}}">
                                        @if ($errors->has('contact_phone')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('contact_phone') }}</strong> </span> @endif
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn-back" onclick="prevStep(1)"><i
                                                class="fas fa-arrow-left"></i></button>
                                        <button type="button" class="btn-next" onclick="nextStep(3)">{{__('Next')}}</button>
                                    </div>
                                </div>

                                <!-- Step 3: Login Credentials -->
                                <div class="step-content" id="step-3">
                                    <h4>{{__('Login Credentials')}}</h4>
                                    <p class="sub-title">{{__('Provide official email and create account')}}</p>

                                    <div class="formrow{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" name="email" class="form-control with-icon" required
                                            placeholder="{{__('Organization Login Email')}}" value="{{old('email')}}">
                                        @if ($errors->has('email')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('email') }}</strong> </span> @endif
                                    </div>

                                    <div class="formrow{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password" class="form-control with-icon" required
                                            placeholder="{{__('Enter Password')}}">
                                        @if ($errors->has('password')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('password') }}</strong> </span> @endif
                                    </div>

                                    <div class="formrow{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password_confirmation" class="form-control with-icon"
                                            required placeholder="{{__('Re-enter Password')}}">
                                        @if ($errors->has('password_confirmation')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong> </span> @endif
                                    </div>

                                    <!-- Referral Code Section -->
                                    <div class="formrow" style="margin-top:20px;">
                                        <label
                                            style="font-weight: normal; margin-bottom: 5px; display: block; font-size:13px;">{{__('Do you have an invitation code?')}}</label>
                                        <div style="margin-bottom: 10px;">
                                            <label
                                                style="font-weight: normal; margin-right: 15px; cursor: pointer; font-size:13px;">
                                                <input type="radio" name="has_referral_code" value="yes" id="hasReferralYes"
                                                    {{ (old('has_referral_code') == 'yes' || request('ref') || session('referral_code')) ? 'checked' : '' }}
                                                    style="margin-right: 5px;">
                                                {{__('Yes')}}
                                            </label>
                                            <label style="font-weight: normal; cursor: pointer; font-size:13px;">
                                                <input type="radio" name="has_referral_code" value="no" id="hasReferralNo"
                                                    {{ (old('has_referral_code', 'no') == 'no' && !request('ref') && !session('referral_code')) ? 'checked' : '' }}
                                                    style="margin-right: 5px;">
                                                {{__('No')}}
                                            </label>
                                        </div>
                                        <div id="referralCodeField"
                                            style="display: {{ (old('has_referral_code') == 'yes' || request('ref') || session('referral_code')) ? 'block' : 'none' }};">
                                            <input type="text" name="ref" id="referralCodeInput" class="form-control"
                                                placeholder="{{__('Enter invitation code')}}"
                                                value="{{ old('ref', request('ref') ?: session('referral_code')) }}">
                                            @if ($errors->has('ref')) <span class="help-block text-danger">
                                            <strong>{{ $errors->first('ref') }}</strong> </span> @endif
                                        </div>
                                    </div>

                                    <div class="formrow{{ $errors->has('terms_of_use') ? ' has-error' : '' }}"
                                        style="font-size: 13px; color:#555; text-align:center;">
                                        <label style="cursor: pointer; font-weight:normal;">
                                            <input type="checkbox" value="1" name="terms_of_use" required
                                                style="vertical-align: middle; margin-right: 5px;" />
                                            {!! __('By clicking on \'Create an Employer Account\' below you are agreeing to the <a href=":url" target="_blank" style="color:#0056b3; font-weight:600;">terms</a> and <a href=":privacy" target="_blank" style="color:#0056b3; font-weight:600;">privacy</a> of :site!', ['url' => url('cms/terms-of-use'), 'privacy' => url('cms/privacy-policy'), 'site' => config('app.name')]) !!}
                                        </label>
                                        @if ($errors->has('terms_of_use'))
                                            <div class="help-block text-danger mt-1">
                                                <strong>{{ $errors->first('terms_of_use') }}</strong>
                                        </div> @endif
                                    </div>

                                    <div
                                        class="form-group col-12 col-sm-12 col-md-10 text-center mx-auto mobile-padding-no mb-3 {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                                        {!! app('captcha')->display() !!}
                                        @if ($errors->has('g-recaptcha-response')) <span class="help-block text-danger">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong> </span> @endif
                                    </div>

                                    <div class="action-buttons">
                                        <button type="button" class="btn-back" onclick="prevStep(2)"><i
                                                class="fas fa-arrow-left"></i></button>
                                        <button type="submit"
                                            class="btn-submit">{{__('Create an Employer Account')}}</button>
                                    </div>
                                </div>

                            </form>

                            <div class="login-link">
                                {{__('Already have an employer account?')}} <a
                                    href="{{url('company-login')}}">{{__('Login Here')}}</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.footer')

    @push('scripts')
        <script>
            function validateStep(step) {
                let isValid = true;
                const currentStepDiv = document.getElementById('step-' + step);
                const requiredInputs = currentStepDiv.querySelectorAll('input[required], select[required]');

                // Clear old error highlights
                currentStepDiv.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '#d1e3ff');

                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.style.borderColor = 'red';
                    }
                });

                return isValid;
            }

            function nextStep(step) {
                if (!validateStep(step - 1)) return;

                document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
                document.getElementById('step-' + step).classList.add('active');

                // Update indicators
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

                // Update indicators
                const nextIndicator = document.getElementById('indicator-' + (step + 1));
                nextIndicator.classList.remove('active');
                nextIndicator.classList.remove('completed');
                nextIndicator.querySelector('.step-indicator').innerHTML = (step + 1);

                const currIndicator = document.getElementById('indicator-' + step);
                currIndicator.classList.add('active');
                currIndicator.classList.remove('completed');
                currIndicator.querySelector('.step-indicator').innerHTML = step;
            }

            document.addEventListener('DOMContentLoaded', function () {
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

                if (hasReferralYes) hasReferralYes.addEventListener('change', toggleReferralField);
                if (hasReferralNo) hasReferralNo.addEventListener('change', toggleReferralField);

                // Auto-open step with errors if validation failed
                @if($errors->any())
                    // Quick check for step 3 errors
                    @if($errors->has('email') || $errors->has('password') || $errors->has('terms_of_use') || $errors->has('g-recaptcha-response'))
                        nextStep(2);
                        nextStep(3);
                    @elseif($errors->has('contact_name') || $errors->has('contact_phone'))
                        nextStep(2);
                    @endif
                @endif
                                                                                });
        </script>
    @endpush

@endsection