@php
    $px = isset($profileExperience) ? $profileExperience : null;
@endphp
<div class="modal-body">
    <div class="form-body resume-experience-form">
        <div class="resume-exp-row row align-items-center mb-3" id="div_title">
            <label class="col-sm-4 col-form-label resume-exp-label" for="title">{{ __('Job title') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control resume-exp-input" id="title" placeholder="{{ __('e.g. Registered Nurse') }}" name="title" type="text" value="{{ old('title', $px ? $px->title : '') }}">
                <span class="help-block title-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row align-items-center mb-3" id="div_company">
            <label class="col-sm-4 col-form-label resume-exp-label" for="company">{{ __('Employer') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control resume-exp-input" id="company" placeholder="{{ __('Company or organization name') }}" name="company" type="text" value="{{ old('company', $px ? $px->company : '') }}">
                <span class="help-block company-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row align-items-center mb-3" id="div_employer_address">
            <label class="col-sm-4 col-form-label resume-exp-label" for="employer_address">{{ __('Employer address') }}</label>
            <div class="col-sm-8">
                <input class="form-control resume-exp-input" id="employer_address" placeholder="{{ __('Street, city, province/state, postal code') }}" name="employer_address" type="text" value="{{ old('employer_address', $px ? $px->employer_address : '') }}">
                <span class="help-block employer_address-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row align-items-center mb-3" id="div_date_start">
            <label class="col-sm-4 col-form-label resume-exp-label" for="date_start">{{ __('Date started working') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                @php
                    $dateStartVal = '';
                    if ($px && $px->date_start) {
                        try {
                            $dateStartVal = \Carbon\Carbon::parse($px->date_start)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $dateStartVal = '';
                        }
                    }
                @endphp
                <input class="form-control resume-exp-input datepicker" autocomplete="off" id="date_start" placeholder="{{ __('Start date') }}" name="date_start" type="text" value="{{ old('date_start', $dateStartVal) }}">
                <span class="help-block date_start-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row mb-3" id="div_is_currently_working">
            <label class="col-sm-4 col-form-label resume-exp-label">{{ __('Currently working here?') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <div class="radio-list pt-1">
                    @php
                        if (old('is_currently_working') !== null) {
                            $valYes = (string) old('is_currently_working') === '1';
                        } else {
                            $valYes = $px && (int) $px->is_currently_working === 1;
                        }
                    @endphp
                    <label class="radio-inline me-3"><input id="currently_working" name="is_currently_working" type="radio" value="1" {{ $valYes ? 'checked' : '' }}> {{ __('Yes') }}</label>
                    <label class="radio-inline"><input id="not_currently_working" name="is_currently_working" type="radio" value="0" {{ ! $valYes ? 'checked' : '' }}> {{ __('No') }}</label>
                </div>
                <span class="help-block is_currently_working-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row align-items-center mb-3" id="div_date_end" style="{{ $valYes ? 'display:none;' : '' }}">
            <label class="col-sm-4 col-form-label resume-exp-label" for="date_end">{{ __('Job end date') }}</label>
            <div class="col-sm-8">
                @php
                    $dateEndVal = '';
                    if ($px && $px->date_end) {
                        try {
                            $dateEndVal = \Carbon\Carbon::parse($px->date_end)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $dateEndVal = '';
                        }
                    }
                @endphp
                <input class="form-control resume-exp-input datepicker" autocomplete="off" id="date_end" placeholder="{{ __('End date') }}" name="date_end" type="text" value="{{ old('date_end', $dateEndVal) }}">
                <span class="help-block date_end-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-exp-row row mb-3" id="div_description">
            <label class="col-sm-4 col-form-label resume-exp-label pt-sm-2" for="description">{{ __('Job summary') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <textarea name="description" class="form-control resume-exp-input resume-exp-textarea" id="description" rows="5" placeholder="{{ __('Describe your role and achievements') }}">{{ old('description', $px ? $px->description : '') }}</textarea>
                <span class="help-block description-error text-danger small"></span>
            </div>
        </div>
    </div>
</div>
