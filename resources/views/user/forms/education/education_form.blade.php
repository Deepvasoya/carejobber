@php
    $pe = isset($profileEducation) ? $profileEducation : null;
@endphp
<div class="modal-body">
    <div class="form-body resume-education-form">
        <div class="resume-edu-row row align-items-center mb-3" id="div_degree_level_id">
            <label class="col-sm-4 col-form-label resume-edu-label" for="degree_level_id">{{ __('Level of education') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                {!! Form::select('degree_level_id', ['' => __('Select highest level of education')] + $degreeLevels, $pe ? $pe->degree_level_id : null, ['class' => 'form-control resume-edu-input', 'id' => 'degree_level_id']) !!}
                <span class="help-block degree_level_id-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row align-items-center mb-3" id="div_degree_type_id">
            <label class="col-sm-4 col-form-label resume-edu-label" for="degree_type_id">{{ __('Certification type') }}</label>
            <div class="col-sm-8">
                <span id="degree_types_dd">
                    {!! Form::select('degree_type_id', ['' => __('Select certification type')], $pe ? $pe->degree_type_id : null, ['class' => 'form-control resume-edu-input', 'id' => 'degree_type_id']) !!}
                </span>
                <span class="help-block degree_type_id-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row align-items-center mb-3" id="div_degree_title">
            <label class="col-sm-4 col-form-label resume-edu-label" for="degree_title">{{ __('Title') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control resume-edu-input" id="degree_title" placeholder="{{ __('e.g. Diploma in Practical Nursing') }}" name="degree_title" type="text" value="{{ old('degree_title', $pe ? $pe->degree_title : '') }}">
                <span class="help-block degree_title-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row align-items-center mb-3" id="div_institution">
            <label class="col-sm-4 col-form-label resume-edu-label" for="institution">{{ __('Academy') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control resume-edu-input" id="institution" placeholder="{{ __('School / academy name') }}" name="institution" type="text" value="{{ old('institution', $pe ? $pe->institution : '') }}">
                <span class="help-block institution-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row align-items-center mb-3" id="div_school_location">
            <label class="col-sm-4 col-form-label resume-edu-label" for="school_location">{{ __('School address') }}</label>
            <div class="col-sm-8">
                <input class="form-control resume-edu-input" id="school_location" placeholder="{{ __('Street, city, postal code') }}" name="school_location" type="text" value="{{ old('school_location', $pe ? $pe->school_location : '') }}">
                <span class="help-block school_location-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row align-items-center mb-3" id="div_date_completion">
            <label class="col-sm-4 col-form-label resume-edu-label" for="date_completion">{{ __('Year') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                @php
                    $date_completion = old('date_completion', $pe ? $pe->date_completion : null);
                @endphp
                {!! Form::select('date_completion', ['' => __('Year graduated')] + MiscHelper::getEstablishedIn(), $date_completion, ['class' => 'form-control resume-edu-input', 'id' => 'date_completion']) !!}
                <span class="help-block date_completion-error text-danger small"></span>
            </div>
        </div>

        <div class="resume-edu-row row mb-3" id="div_description">
            <label class="col-sm-4 col-form-label resume-edu-label pt-sm-2" for="education_description">{{ __('Description') }}</label>
            <div class="col-sm-8">
                <textarea class="form-control resume-edu-input resume-edu-textarea" id="education_description" name="description" rows="5" placeholder="{{ __('Courses, honours, relevant details') }}">{{ old('description', $pe ? $pe->description : '') }}</textarea>
                <span class="help-block description-error text-danger small"></span>
            </div>
        </div>

        @if(\App\Helpers\LocationHelper::showCountry())
        <div class="resume-edu-row row align-items-center mb-3" id="div_country_id">
            <label class="col-sm-4 col-form-label resume-edu-label" for="education_country_id">{{ __('Country') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                @php
                    $country_id = old('country_id', $pe ? $pe->country_id : ($siteSetting->default_country_id ?? null));
                @endphp
                {!! Form::select('country_id', ['' => __('Select country')] + $countries, $country_id, ['class' => 'form-control resume-edu-input', 'id' => 'education_country_id']) !!}
                <span class="help-block country_id-error text-danger small"></span>
            </div>
        </div>
        @endif

        @if(\App\Helpers\LocationHelper::showState())
        <div class="resume-edu-row row align-items-center mb-3" id="div_state_id">
            <label class="col-sm-4 col-form-label resume-edu-label" for="education_state_id">{{ __('State / province') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <span id="default_state_education_dd">
                    {!! Form::select('state_id', ['' => __('Select state')], $pe ? $pe->state_id : null, ['class' => 'form-control resume-edu-input', 'id' => 'education_state_id']) !!}
                </span>
                <span class="help-block state_id-error text-danger small"></span>
            </div>
        </div>
        @endif

        @if(\App\Helpers\LocationHelper::showCity())
        <div class="resume-edu-row row align-items-center mb-3" id="div_city_id">
            <label class="col-sm-4 col-form-label resume-edu-label" for="city_id">{{ __('City') }} <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <span id="default_city_education_dd">
                    {!! Form::select('city_id', ['' => __('Select city')], $pe ? $pe->city_id : null, ['class' => 'form-control resume-edu-input', 'id' => 'city_id']) !!}
                </span>
                <span class="help-block city_id-error text-danger small"></span>
            </div>
        </div>
        @endif

        @if(!\App\Helpers\LocationHelper::showCountry())
            @php
                $eduHiddenCountry = (int) old('country_id', ($pe && (int) $pe->country_id > 0) ? $pe->country_id : ($siteSetting->default_country_id ?? 0));
            @endphp
            @if($eduHiddenCountry > 0)
                <input type="hidden" name="country_id" id="education_country_id" value="{{ $eduHiddenCountry }}">
            @endif
        @endif
    </div>
</div>
