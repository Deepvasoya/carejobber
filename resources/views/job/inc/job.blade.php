@php
    $company = Auth::guard('company')->user();
    $hasActivePackage = $company->package_id && $company->package_end_date && \Carbon\Carbon::parse($company->package_end_date)->isFuture();
    $remainingCredits = $hasActivePackage ? ($company->jobs_quota - $company->availed_jobs_quota) : 0;
    $packageName = $hasActivePackage ? ($company->getPackage('package_title') ?? __('Active Package')) : __('No Package');
    $packageExpiry = $hasActivePackage ? \Carbon\Carbon::parse($company->package_end_date)->format('M d, Y') : null;
    $isEditingJob = isset($job);
    $canPostNewJob = \Illuminate\Support\Facades\Gate::forUser($company)->allows('canPostJob');
    $showJobForm = true;
@endphp

@if ($showJobForm)
@if($hasActivePackage)
<div class="alert alert-success mb-4" style="border-radius: 12px; border-left: 4px solid #28a745; background: #d4edda;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="margin: 0 0 8px 0; color: #155724; font-weight: 600;">
                <i class="fas fa-check-circle"></i> {{ $packageName }}
            </h5>
            <p style="margin: 0; color: #155724; font-size: 14px;">
                <strong>{{ $remainingCredits }}</strong> {{ __('job posting credits remaining') }} • 
                {{ __('Expires') }}: {{ $packageExpiry }}
            </p>
        </div>
        <a href="{{ route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']) }}" class="btn btn-sm" style="background: #28a745; color: #fff; border-radius: 8px; padding: 8px 20px;">
            <i class="fas fa-arrow-up"></i> {{ __('Upgrade') }}
        </a>
    </div>
</div>
@else
<div class="alert alert-warning mb-4" style="border-radius: 12px; border-left: 4px solid #ffc107; background: #fff3cd;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="margin: 0 0 8px 0; color: #856404; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> {{ __('No Active Package') }}
            </h5>
            <p style="margin: 0; color: #856404; font-size: 14px;">
                {{ __('Purchase a package to start posting jobs') }}
            </p>
        </div>
        <a href="{{ route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']) }}" class="btn btn-sm" style="background: #ffc107; color: #000; border-radius: 8px; padding: 8px 20px; font-weight: 600;">
            <i class="fas fa-shopping-cart"></i> {{ __('Buy Package') }}
        </a>
    </div>
</div>
@endif

<h5>{{__('Job Details')}}</h5>
@if(isset($job))
{!! Form::model($job, array('method' => 'put', 'route' => array('update.front.job', $job->id), 'class' => 'form')) !!}
{!! Form::hidden('id', $job->id) !!}
@else
{!! Form::open(array('method' => 'post', 'route' => array('store.front.job'), 'class' => 'form')) !!}
@endif
<div class="row job-post-form">
    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'title') !!}">
            <label>{{ __('Job title') }} <span>*</span></label>
            {!! Form::text('title', null, array('class'=>'form-control', 'id'=>'title', 'placeholder'=>__('Job title'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'title') !!}
        </div>
    </div>
    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'description') !!}">
           <label for="description">{{ __('Description') }} <span>*</span></label>
        {!! Form::textarea('description', null, array('class'=>'form-control', 'id'=>'description', 'placeholder'=>__('Job description'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'description') !!} </div>
    </div>
	
	 <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'benefits') !!}">
        <label for="benefits">{{ __('Benefits') }}</label>    
        {!! Form::textarea('benefits', null, array('class'=>'form-control', 'id'=>'benefits', 'placeholder'=>__('Job Benefits'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'benefits') !!} </div>
    </div>

    <!-- New Fields -->
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_id') !!}">
            <label>{{ __('Job ID') }}</label>
            {!! Form::text('job_id', null, array('class'=>'form-control', 'id'=>'job_id', 'placeholder'=>__('Job ID'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'union') !!}">
            <label>{{ __('Union') }}</label>
            {!! Form::text('union', null, array('class'=>'form-control', 'id'=>'union', 'placeholder'=>__('Union'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'union') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'fte') !!}">
            <label>{{ __('FTE') }}</label>
            {!! Form::text('fte', null, array('class'=>'form-control', 'id'=>'fte', 'placeholder'=>__('FTE'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'fte') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'hours_per_shift') !!}">
            <label>{{ __('Hours per Shift') }}</label>
            {!! Form::text('hours_per_shift', null, array('class'=>'form-control', 'id'=>'hours_per_shift', 'placeholder'=>__('Hours per Shift'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'hours_per_shift') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'shifts_per_cycle') !!}">
            <label>{{ __('Shifts per Cycle') }}</label>
            {!! Form::text('shifts_per_cycle', null, array('class'=>'form-control', 'id'=>'shifts_per_cycle', 'placeholder'=>__('Shifts per Cycle'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'shifts_per_cycle') !!}
        </div>
    </div>

    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_primary_location') !!}">
            <label>{{ __('Job Primary Location') }}</label>
            <div id="job-locations-container">
                @php
                    $locations = old('job_primary_location', isset($job) ? $job->job_primary_location : '');
                    $locationsArray = $locations ? (is_array($locations) ? $locations : explode("\n", $locations)) : [''];
                @endphp
                @foreach($locationsArray as $index => $location)
                    <div class="input-group mb-2 location-item">
                        <input type="text" name="job_primary_location[]" value="{{ $location }}" class="form-control" placeholder="{{__('Job Primary Location')}}">
                        @if($index > 0)
                            <button type="button" class="btn btn-danger remove-location">{{__('Remove')}}</button>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-location-btn">{{__('Add Multi-Site Location')}}</button>
            {!! APFrmErrHelp::showErrors($errors, 'job_primary_location') !!}
        </div>
    </div>

    <!-- 2 Column Layout Starts -->
    <div class="col-md-6">
        @php
            $jobCategoriesWithOther = $jobCategories + ['0' => __('Other (specify)')];
        @endphp
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'functional_area_id') !!}" id="functional_area_id_div">
            <label>{{ __('Category') }} <span>*</span></label>
            {!! Form::select('functional_area_id', ['' => __('Select Category')]+$jobCategoriesWithOther, null, array('class'=>'form-control', 'id'=>'functional_area_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'functional_area_id') !!}
        </div>
        <div id="custom_functional_area_wrap_job" style="display:none;" class="formrow {!! APFrmErrHelp::hasError($errors, 'custom_functional_area') !!}">
            <label>{{ __('Custom Category') }}</label>
            {!! Form::text('custom_functional_area', old('custom_functional_area'), array('class'=>'form-control', 'maxlength'=>200, 'placeholder'=>__('Enter category name'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_functional_area') !!}
        </div>
    </div>

    <div class="col-md-6">
        @php
            $industriesWithOther = $industries + ['0' => __('Other (specify)')];
        @endphp
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'industry_id') !!}" id="industry_id_div">
            <label>{{ __('Facility Type') }} <span>*</span></label>
            {!! Form::select('industry_id', ['' => __('Select Facility Type')]+$industriesWithOther, null, array('class'=>'form-control', 'id'=>'industry_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'industry_id') !!}
        </div>
        <div id="custom_industry_wrap_job" style="display:none;" class="formrow {!! APFrmErrHelp::hasError($errors, 'custom_industry') !!}">
            <label>{{ __('Custom Facility Type') }}</label>
            {!! Form::text('custom_industry', old('custom_industry'), array('class'=>'form-control', 'id'=>'custom_industry', 'maxlength'=>200, 'placeholder'=>__('Enter facility type'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_industry') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_type_id') !!}" id="job_type_id_div">
            <label>{{ __('Job Type') }} <span>*</span></label>
            {!! Form::select('job_type_id', ['' => __('Select Job Type')]+$jobTypes, null, array('class'=>'form-control', 'id'=>'job_type_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_type_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'skills') !!}">
            <label for="skills">{{ __('Skills / Tags') }} <span>*</span></label>    
            <?php $skills = old('skills', $jobSkillIds); ?>
            @php $jobSkillsWithOther = $jobSkills + ['0' => __('Other — add custom skills below')]; @endphp
            {!! Form::select('skills[]', $jobSkillsWithOther, $skills, array('class'=>'form-control select2-multiple', 'id'=>'skills', 'multiple'=>'multiple')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'skills') !!}
        </div>
        <div id="custom_job_skills_wrap_job" style="display:none;" class="formrow">
            <label>{{ __('Custom skills (one per line)') }}</label>
            {!! Form::textarea('custom_job_skills_lines', old('custom_job_skills_lines'), array('class'=>'form-control', 'id'=>'custom_job_skills_lines', 'rows'=>3, 'placeholder'=>__('e.g. Pediatric care'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_job_skills_lines') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'gender_id') !!}" id="gender_id_div">
            <label>{{ __('Gender Needed') }}</label>
            {!! Form::select('gender_id', ['' => __('No Preference')]+$genders, null, array('class'=>'form-control', 'id'=>'gender_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gender_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow">
            <label>{{ __('Job Apply Type') }}</label>
            <?php
            $selectedApplyType = old('apply_type');
            if (!$selectedApplyType) {
                $selectedApplyType = isset($job)
                    ? $job->getEffectiveApplyType()
                    : (old('external_job', 'no') === 'yes' ? 'external' : 'internal');
            }
            ?>
            {!! Form::select('apply_type', [
                'internal' => __('Internal'),
                'external' => __('Apply on the company site'),
                'email' => __('Through Email'),
                'phone' => __('Call To Apply')
            ], $selectedApplyType, ['class' => 'form-control', 'id' => 'apply_type']) !!}
            <input type="hidden" name="external_job" id="external_job" value="{{ $selectedApplyType === 'internal' ? 'no' : 'yes' }}">
        </div>
        <div id="externalLinkField" class="formrow" style="display: {{ $selectedApplyType !== 'internal' ? 'block' : 'none' }}">
            <label id="jobLinkLabel">{{ __('External Application URL') }}</label>
            {!! Form::text('job_link', old('job_link', isset($job) ? $job->job_link : ''), ['class' => 'form-control', 'id' => 'job_link', 'placeholder'=>'https://']) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_link') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_period_id') !!}" id="salary_period_id_div">
            <label>{{ __('Salary Type') }} <span>*</span></label>
            {!! Form::select('salary_period_id', ['' => __('Select Salary Type')]+$salaryPeriods, null, array('class'=>'form-control', 'id'=>'salary_period_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_period_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_from') !!}" id="salary_from_div">
            <label>{{ __('Min. Salary') }}</label>
            {!! Form::number('salary_from', null, array('class'=>'form-control', 'id'=>'salary_from', 'placeholder'=>__('Min. Salary'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_from') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_to') !!}" id="salary_to_div">
            <label>{{ __('Max. Salary') }}</label>
            {!! Form::number('salary_to', null, array('class'=>'form-control', 'id'=>'salary_to', 'placeholder'=>__('Max. Salary'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_to') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_experience_id') !!}" id="job_experience_id_div">
            <label>{{ __('Experience') }} <span>*</span></label>
            {!! Form::select('job_experience_id', ['' => __('Select Experience')]+$jobExperiences, null, array('class'=>'form-control', 'id'=>'job_experience_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_experience_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'degree_level_id') !!}" id="degree_level_id_div">
            <label>{{ __('Qualification') }} <span>*</span></label>
            {!! Form::select('degree_level_id', ['' =>__('Select Qualification')]+$degreeLevels, null, array('class'=>'form-control', 'id'=>'degree_level_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'degree_level_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_shift_id') !!}" id="job_shift_id_div">
            <label>{{ __('Job Shift') }}</label>
            {!! Form::select('job_shift_id', ['' => __('Select Job Shift')]+$jobShifts, null, array('class'=>'form-control', 'id'=>'job_shift_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_shift_id') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'num_of_positions') !!}" id="num_of_positions_div">
            <label>{{ __('Positions Available') }}</label>
            {!! Form::select('num_of_positions', ['' => __('Select Positions')]+MiscHelper::getNumPositions(), null, array('class'=>'form-control', 'id'=>'num_of_positions')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'num_of_positions') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'expiry_date') !!}">
            <label>{{ __('Application Deadline') }}</label>
            {!! Form::text('expiry_date', null, array('class'=>'form-control datepicker', 'id'=>'expiry_date', 'placeholder'=>__('Deadline date'), 'autocomplete'=>'off')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'expiry_date') !!}
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_currency') !!}" id="salary_currency_div">
            <label>{{ __('Salary Currency') }}</label>
            @php $salary_currency = Request::get('salary_currency', (isset($job))? $job->salary_currency:$siteSetting->default_currency_code); @endphp
            {!! Form::select('salary_currency', ['' => __('Select Currency')]+$currencies, $salary_currency, array('class'=>'form-control', 'id'=>'salary_currency')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_currency') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'career_level_id') !!}" id="career_level_id_div">
            <label>{{ __('Career Level') }}</label>
            {!! Form::select('career_level_id', ['' => __('Select Career Level')]+$careerLevels, null, array('class'=>'form-control', 'id'=>'career_level_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'career_level_id') !!}
        </div>
    </div>

    <!-- Location Information -->
    @if(\App\Helpers\LocationHelper::showCountry())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'country_id') !!}" id="country_id_div">
            <label>{{ __('Country') }}</label>
            {!! Form::select('country_id', ['' => __('Select Country')]+$countries, old('country_id', (isset($job))? $job->country_id:$siteSetting->default_country_id), array('class'=>'form-control', 'id'=>'country_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'country_id') !!}
        </div>
    </div>
    @elseif(\App\Helpers\LocationHelper::showState() || \App\Helpers\LocationHelper::showCity())
    {!! Form::hidden('country_id', old('country_id', (isset($job) ? $job->country_id : null) ?? $siteSetting->default_country_id), ['id' => 'country_id']) !!}
    @endif
    
    @if(\App\Helpers\LocationHelper::showState())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'state_id') !!}" id="state_id_div">
            <label>{{ __('State/Province') }}</label>
            <span id="default_state_dd"> {!! Form::select('state_id', ['' => __('Select State/Province')], null, array('class'=>'form-control', 'id'=>'state_id')) !!} </span>
            {!! APFrmErrHelp::showErrors($errors, 'state_id') !!}
        </div>
    </div>
    @endif
    
    @if(\App\Helpers\LocationHelper::showCity())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'city_id') !!}" id="city_id_div">
            <label>{{ __('City') }}</label>
            <span id="default_city_dd"> {!! Form::select('city_id', ['' => __('Select City')]+['0'=>__('Other (specify)')], null, array('class'=>'form-control', 'id'=>'city_id')) !!} </span>
            {!! APFrmErrHelp::showErrors($errors, 'city_id') !!}
        </div>
        <div id="custom_city_name_wrap_job" style="display:none;" class="formrow {!! APFrmErrHelp::hasError($errors, 'custom_city_name') !!}">
            <label>{{ __('Custom city') }}</label>
            {!! Form::text('custom_city_name', old('custom_city_name'), array('class'=>'form-control', 'id'=>'custom_city_name', 'maxlength'=>30)) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_city_name') !!}
        </div>
    </div>
    @endif

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'hide_salary') !!}">
            <label>{{ __('Hide Salary?') }}</label>
            <div class="radio-list d-flex gap-3 align-items-center" style="height: 45px;">
                <?php
                $hide_salary_1 = '';
                $hide_salary_2 = 'checked="checked"';
                if (old('hide_salary', ((isset($job)) ? $job->hide_salary : 0)) == 1) {
                    $hide_salary_1 = 'checked="checked"';
                    $hide_salary_2 = '';
                }
                ?>
                <label class="radio-inline mb-0" style="font-weight: normal;">
                    <input id="hide_salary_yes" name="hide_salary" type="radio" value="1" {{$hide_salary_1}}>
                    {{__('Yes')}}
                </label>
                <label class="radio-inline mb-0" style="font-weight: normal;">
                    <input id="hide_salary_no" name="hide_salary" type="radio" value="0" {{$hide_salary_2}}>
                    {{__('No')}}
                </label>
            </div>
            {!! APFrmErrHelp::showErrors($errors, 'hide_salary') !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'is_freelance') !!}">
            <label>{{ __('Is Freelance?') }}</label>
            <div class="radio-list d-flex gap-3 align-items-center" style="height: 45px;">
                <?php
                $is_freelance_1 = '';
                $is_freelance_2 = 'checked="checked"';
                if (old('is_freelance', ((isset($job)) ? $job->is_freelance : 0)) == 1) {
                    $is_freelance_1 = 'checked="checked"';
                    $is_freelance_2 = '';
                }
                ?>
                <label class="radio-inline mb-0" style="font-weight: normal;">
                    <input id="is_freelance_yes" name="is_freelance" type="radio" value="1" {{$is_freelance_1}}>
                    {{__('Yes')}}
                </label>
                <label class="radio-inline mb-0" style="font-weight: normal;">
                    <input id="is_freelance_no" name="is_freelance" type="radio" value="0" {{$is_freelance_2}}>
                    {{__('No')}}
                </label>
            </div>
            {!! APFrmErrHelp::showErrors($errors, 'is_freelance') !!}
        </div>
    </div>

    <div class="col-md-12">
        @include('includes.custom_fields_for_context', [
            'context' => \App\Models\CustomField::CONTEXT_JOB_LISTING,
            'values' => old('custom_fields', (isset($job) ? ($job->custom_field_data ?? []) : [])),
        ])
    </div>

</div>





<hr>


    <!-- Listing promotion options (employer) -->
    <div class="col-md-12">
        <div class="formrow job-promotion-options" style="background: #f8f9fc; border: 1px solid #e8eaf0; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
            <h5 style="margin-top: 0;"><i class="fas fa-bullhorn"></i> {{__('Boost your listing')}}</h5>
            @php
                $promoCfg = \App\Services\JobPromotionPricing::config();
                $promoCur = $siteSetting->default_currency_code ?? strtoupper($promoCfg['currency']);
                $hlDays = (int) $promoCfg['highlighted_days'];
            @endphp
            <p class="text-muted" style="font-size: 14px;">
                {{ __('Choose how long each upgrade runs. Urgent appears first in search, then featured. Highlight adds a coloured background to your card.') }}
                <strong>{{ __('Paid options are charged via Stripe after you submit this form.') }}</strong>
            </p>
            @php
                $isErr = isset($errors) && $errors->any();
                $defaultUrgent = old('promote_urgent_days',0);
                $defaultFeatured = old('promote_featured_days', 0);
                $defaultHl = (int) old('promote_highlighted', 0);
                $promoJobUrgent = isset($job) && $job->isPromotionUrgentActive();
                $promoJobFeatured = isset($job) && $job->isPromotionFeaturedActive();
                $promoJobHighlighted = isset($job) && $job->isPromotionHighlightedActive();
                $__jobPromoPricesForJs = [
                    'urgent_7' => (float) $promoCfg['urgent_7_price'],
                    'urgent_15' => (float) $promoCfg['urgent_15_price'],
                    'featured_15' => (float) $promoCfg['featured_15_price'],
                    'featured_30' => (float) $promoCfg['featured_30_price'],
                    'highlighted' => (float) $promoCfg['highlighted_price'],
                ];
            @endphp
            @if(isset($job) && ($promoJobUrgent || $promoJobFeatured || $promoJobHighlighted))
                <div class="alert alert-light border small mb-3">
                    @if($promoJobUrgent)
                        @php $uEnd = $job->promotion_urgent_until ?? $job->display_end_date; @endphp
                        <div><i class="fas fa-fire text-danger"></i> {{ __('Urgent active until :date', ['date' => $uEnd ? $uEnd->format('M d, Y') : '—']) }}</div>
                    @endif
                    @if($promoJobFeatured)
                        @php $fEnd = $job->promotion_featured_until ?? $job->display_end_date; @endphp
                        <div><i class="fas fa-bolt text-warning"></i> {{ __('Featured active until :date', ['date' => $fEnd ? $fEnd->format('M d, Y') : '—']) }}</div>
                    @endif
                    @if($promoJobHighlighted)
                        @php $hEnd = $job->promotion_highlighted_until ?? $job->display_end_date; @endphp
                        <div><i class="fas fa-highlighter text-info"></i> {{ __('Highlighted active until :date', ['date' => $hEnd ? $hEnd->format('M d, Y') : '—']) }}</div>
                    @endif
                    <div class="mt-1 text-muted">{{ __('You can renew a boost after it expires.') }}</div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="fw-semibold mb-2"><i class="fas fa-fire text-danger"></i> {{ __('Urgent') }}</div>
                    <div class="d-flex flex-column gap-1">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_urgent_days" value="0" class="mt-0" @if((int)$defaultUrgent === 0) checked @endif>
                            <span>{{ __('No urgent boost') }}</span>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_urgent_days" value="7" class="mt-0" @if((int)$defaultUrgent === 7) checked @endif>
                            <span>{{ __('7 days') }} — <strong>{{ $promoCur }}{{ number_format($promoCfg['urgent_7_price'], 2) }}</strong></span>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_urgent_days" value="15" class="mt-0" @if((int)$defaultUrgent === 15) checked @endif>
                            <span>{{ __('15 days') }} — <strong>{{ $promoCur }}{{ number_format($promoCfg['urgent_15_price'], 2) }}</strong></span>
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">{{ __('Top of search results') }}</small>
                </div>
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="fw-semibold mb-2"><i class="fas fa-bolt text-warning"></i> {{ __('Featured listing') }}</div>
                    <div class="d-flex flex-column gap-1">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_featured_days" value="0" class="mt-0" @if((int)$defaultFeatured === 0) checked @endif>
                            <span>{{ __('No featured boost') }}</span>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_featured_days" value="15" class="mt-0" @if((int)$defaultFeatured === 15) checked @endif>
                            <span>{{ __('15 days') }} — <strong>{{ $promoCur }}{{ number_format($promoCfg['featured_15_price'], 2) }}</strong></span>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_featured_days" value="30" class="mt-0" @if((int)$defaultFeatured === 30) checked @endif>
                            <span>{{ __('30 days') }} — <strong>{{ $promoCur }}{{ number_format($promoCfg['featured_30_price'], 2) }}</strong></span>
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">{{ __('Shown after urgent jobs') }}</small>
                </div>
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="fw-semibold mb-2"><i class="fas fa-highlighter text-info"></i> {{ __('Highlighted') }}</div>
                    <div class="d-flex flex-column gap-1">
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_highlighted" value="0" class="mt-0" @if($defaultHl !== 1) checked @endif>
                            <span>{{ __('No highlight') }}</span>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer; font-weight: 500;">
                            <input type="radio" name="promote_highlighted" value="1" class="mt-0" @if($defaultHl === 1) checked @endif>
                            <span>{{ __(':days days', ['days' => $hlDays]) }} — <strong>{{ $promoCur }}{{ number_format($promoCfg['highlighted_price'], 2) }}</strong></span>
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">{{ __('Distinct background on listings') }}</small>
                </div>
            </div>
            <div id="job-promotion-total-panel" class="mt-3 p-3" style="display: none; background: #fff; border: 1px solid #c7d2fe; border-radius: 8px;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <strong><i class="fas fa-receipt text-primary"></i> {{ __('Total due for selected listing upgrades (paid on Stripe after submit)') }}</strong>
                    <span id="job-promotion-total-amount" class="fs-4 fw-bold text-primary ms-md-auto"></span>
                </div>
                <p class="mb-0 mt-2 small text-muted">{{ __('The total updates when you add or remove options. You pay once per selected upgrade.') }}</p>
            </div>
        </div>
    </div>

    <!-- Additional Questions Section -->
    <div class="col-md-12">
        <div class="formrow">
            <h5>{{__('Additional Questions for Jobseekers')}} <small>({{__('Optional')}})</small></h5>
            <p class="text-muted">{{__('Add custom questions that jobseekers will answer when applying for this job.')}}</p>
            
            <div id="questionsContainer">
                @if(isset($job) && $job->jobQuestions->count() > 0)
                    @foreach($job->jobQuestions as $index => $question)
                        <div class="question-item mb-3" data-question-id="{{$question->id}}">
                            <div class="input-group">
                                <input type="text" name="questions[{{$index}}][title]" value="{{$question->question_title}}" class="form-control" placeholder="{{__('Enter Question Title')}}" required>
                                <input type="hidden" name="questions[{{$index}}][id]" value="{{$question->id}}">
                                <button type="button" class="btn-danger remove-question-btn" onclick="removeQuestion(this)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>


            <button type="button" class="btn-primary mb-3" id="addQuestionBtn">
                <i class="fa fa-plus"></i> {{__('ADD QUESTIONS')}}
            </button>
            
            
        </div>
    </div>

    <div class="col-md-12">
        <div class="formrow job-post-form-actions">
            @if(!isset($job) || $job->is_draft)
                <div class="row g-2 align-items-stretch">
                    <div class="col-12 col-lg-3">
                        <button type="submit" name="job_action" value="draft" class="btn btn-secondary w-100 job-post-action-btn" formnovalidate>{{ __('Save as draft') }}</button>
                    </div>
                    <div class="col-12 col-lg-6">
                        <button type="submit" name="job_action" value="submit" class="btn btn-primary w-100 job-post-action-btn job-post-action-btn--submit">{{ __('Submit Job') }} <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-12 col-lg-3">
                        <a href="{{ route('posted.jobs') }}" class="btn btn-outline-secondary w-100 job-post-action-btn">{{ __('Cancel') }}</a>
                    </div>
                </div>
            @else
                <div class="row g-2 align-items-stretch justify-content-lg-center">
                    <div class="col-12 col-lg-6">
                        <button type="submit" name="job_action" value="submit" class="btn btn-primary w-100 job-post-action-btn job-post-action-btn--submit">{{ __('Submit Job') }} <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-12 col-lg-6">
                        <a href="{{ route('posted.jobs') }}" class="btn btn-outline-secondary w-100 job-post-action-btn">{{ __('Cancel') }}</a>
                    </div>
                </div>
            @endif
        </div>
        @if(! $canPostNewJob && (!isset($job) || $job->is_draft))
            <p class="small text-muted mt-2 mb-0">{{ __('Submit Job requires an active package and available credits. You can still save a draft and publish later.') }}</p>
        @endif
    </div>
</div>
<input type="file" name="image" id="image" style="display:none;" accept="image/*"/>
{!! Form::close() !!}

@push('styles')
<style type="text/css">
    /* ── Datepicker ── */
    .datepicker > div { display: block; }

    /* ── Page wrapper ── */
    .formpanel { background: #f0f4f8; padding: 24px; border-radius: 12px; }

    /* ── Section card ── */
    .job-post-form .row > [class*="col-"] { margin-bottom: 0; }
    .job-post-form { background: #fff; border-radius: 12px; padding: 28px 28px 8px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }

    /* ── Labels ── */
    .job-post-form label {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #1e3a5f;
        margin-bottom: 6px;
        display: block;
    }
    .job-post-form label span { color: #e53e3e; margin-left: 2px; }

    /* ── Inputs & selects ── */
    .job-post-form .form-control {
        background: #f7faff !important;
        border: 1.5px solid #d0dff0 !important;
        border-radius: 8px !important;
        min-height: 46px;
        font-size: 14px;
        color: #1a2332;
        box-shadow: none !important;
        transition: border-color .2s, background .2s;
    }
    .job-post-form .form-control:focus {
        background: #fff !important;
        border-color: #2557a7 !important;
        box-shadow: 0 0 0 3px rgba(37,87,167,.12) !important;
    }
    .job-post-form textarea.form-control { min-height: 110px; }

    /* ── Formrow spacing ── */
    .job-post-form .formrow { margin-bottom: 22px; }

    /* ── Select2 ── */
    .job-post-form .select2-container--default .select2-selection--single,
    .job-post-form .select2-container--default .select2-selection--multiple {
        background: #f7faff;
        border: 1.5px solid #d0dff0;
        border-radius: 8px;
        min-height: 46px;
    }
    .job-post-form .select2-container--default.select2-container--focus .select2-selection--single,
    .job-post-form .select2-container--default.select2-container--focus .select2-selection--multiple {
        background: #fff;
        border-color: #2557a7;
        box-shadow: 0 0 0 3px rgba(37,87,167,.12);
    }
    .job-post-form .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px;
        padding-left: 14px;
        color: #1a2332;
        font-size: 14px;
    }
    .job-post-form .select2-container--default .select2-selection--single .select2-selection__arrow { height: 44px; }
    .job-post-form .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #e8f0fe;
        border: 1px solid #b3c9f5;
        border-radius: 6px;
        color: #1e3a5f;
        padding: 4px 10px;
        margin: 6px 0 0 6px;
        font-size: 13px;
    }
    .job-post-form .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #2557a7; margin-right: 5px; }

    /* ── Section divider ── */
    .job-post-form hr { border-color: #e2eaf4; margin: 28px 0; }

    /* ── Submit buttons ── */
    .job-post-form-actions .job-post-action-btn {
        font-weight: 600;
        padding: .65rem 1.2rem;
        border-radius: 8px;
        font-size: 14px;
    }
    .job-post-form-actions .job-post-action-btn--submit {
        background: #2557a7;
        border-color: #2557a7;
        color: #fff;
    }
    .job-post-form-actions .job-post-action-btn--submit:hover { background: #1d4ed8; border-color: #1d4ed8; }

    /* ── Promotion panel ── */
    .job-promotion-options { background: #f7faff !important; border: 1.5px solid #d0dff0 !important; border-radius: 12px !important; }
    .job-promotion-options h5 { color: #1e3a5f; font-weight: 700; }
</style>
@endpush
@push('scripts')
@include('includes.tinyMCEFront')
<script>
    $(document).ready(function() {
        // Multi-site location management
        $('#add-location-btn').on('click', function() {
            var locationHtml = '<div class="input-group mb-2 location-item">' +
                '<input type="text" name="job_primary_location[]" value="" class="form-control" placeholder="{{__('Job Primary Location')}}">' +
                '<button type="button" class="btn btn-danger remove-location">{{__('Remove')}}</button>' +
                '</div>';
            $('#job-locations-container').append(locationHtml);
        });

        $(document).on('click', '.remove-location', function() {
            $(this).closest('.location-item').remove();
        });

        function toggleExternalLinkField() {
            var applyType = $('#apply_type').val() || 'internal';
            var fieldConfig = {
                external: {
                    label: @json(__('External Application URL')),
                    placeholder: 'https://company.com/apply',
                    type: 'text'
                },
                email: {
                    label: @json(__('Application Email')),
                    placeholder: 'jobs@example.com',
                    type: 'email'
                },
                phone: {
                    label: @json(__('Application Phone Number')),
                    placeholder: '+1 555 123 4567',
                    type: 'tel'
                }
            };

            $('#external_job').val(applyType === 'internal' ? 'no' : 'yes');

            if (fieldConfig[applyType]) {
                $('#jobLinkLabel').text(fieldConfig[applyType].label);
                $('#job_link')
                    .attr('type', fieldConfig[applyType].type)
                    .attr('placeholder', fieldConfig[applyType].placeholder);
                $('#externalLinkField').show();
            } else {
                $('#externalLinkField').hide();
            }
        }
        toggleExternalLinkField();
        $('#apply_type').change(function() {
            toggleExternalLinkField();
        });
    });
</script>
<script type="text/javascript">
(function () {
    window.__jobPostingDefaultCountryId = {{ (int) ($siteSetting->default_country_id ?? 0) }};
    window.__jobPostingLocationLevel = {{ (int) \App\Helpers\LocationHelper::getLocationLevels() }};
    window.__jobPostingInitialCityId = {{ (int) old('city_id', (isset($job)) ? $job->city_id : 0) }};

    window.jobFormCountryId = function () {
        var $c = $('#country_id');
        if ($c.length && $c.val()) {
            return $c.val();
        }
        return window.__jobPostingDefaultCountryId ? String(window.__jobPostingDefaultCountryId) : '';
    };

    window.filterJobLangStates = function (state_id) {
        var country_id = window.jobFormCountryId();
        if (country_id !== '') {
            $.post("{{ route('filter.lang.states.dropdown') }}", {country_id: country_id, state_id: state_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_state_dd').html(response);
                        window.filterJobCitiesByState(window.__jobPostingInitialCityId);
                    });
        }
    };

    window.filterJobCitiesByState = function (city_id) {
        var state_id = $('#state_id').val();
        if (state_id && state_id !== '') {
            $.post("{{ route('filter.lang.cities.dropdown') }}", {state_id: state_id, city_id: city_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_city_dd').html(response);
                        if (typeof window.syncJobPostingOtherFields === 'function') {
                            window.syncJobPostingOtherFields();
                        }
                    });
        }
    };

    window.filterJobCitiesByCountry = function (city_id) {
        var country_id = window.jobFormCountryId();
        if (country_id === '') {
            return;
        }
        $.post("{{ route('filter.lang.cities.dropdown') }}", {country_id: country_id, city_id: city_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    $('#default_city_dd').html(response);
                    if (typeof window.syncJobPostingOtherFields === 'function') {
                        window.syncJobPostingOtherFields();
                    }
                });
    };
})();

    window.syncJobPostingOtherFields = function () {
        var $industry = $('#industry_id');
        if ($industry.length) {
            $('#custom_industry_wrap_job').toggle(String($industry.val()) === '0');
        }
        var $jc = $('#functional_area_id');
        if ($jc.length) {
            $('#custom_functional_area_wrap_job').toggle(String($jc.val()) === '0');
        }
        var $ct = $('#city_id');
        if ($ct.length) {
            $('#custom_city_name_wrap_job').toggle(String($ct.val()) === '0');
        }
        var $sk = $('#skills');
        if ($sk.length) {
            var vals = $sk.val() || [];
            var show = false;
            if (Array.isArray(vals)) {
                show = vals.indexOf('0') !== -1 || vals.indexOf(0) !== -1;
            }
            $('#custom_job_skills_wrap_job').toggle(show);
        }
    };

    $(document).ready(function () {
        $('.select2-multiple').select2({
            placeholder: "{{__('Select Required Skills')}}",
            allowClear: true
        });
        $('#skills').on('change', function () {
            window.syncJobPostingOtherFields();
        });
        $(document).on('change', '#industry_id, #functional_area_id, #city_id', function () {
            window.syncJobPostingOtherFields();
        });
        window.syncJobPostingOtherFields();
        $(".datepicker").datepicker({
            autoclose: true,
            startDate: new Date(),
            format: 'yyyy-m-d'
        });
        (function () {
            var prices = @json($__jobPromoPricesForJs);
            var currency = @json((string) $promoCur);
            var already = {
                featured: @json($promoJobFeatured),
                urgent: @json($promoJobUrgent),
                highlighted: @json($promoJobHighlighted),
            };
            function jobPromoPayableTotal() {
                var t = 0;
                var u = parseInt($('input[name="promote_urgent_days"]:checked').val() || '0', 10);
                if (u === 7 && !already.urgent) {
                    t += prices.urgent_7;
                } else if (u === 15 && !already.urgent) {
                    t += prices.urgent_15;
                }
                var f = parseInt($('input[name="promote_featured_days"]:checked').val() || '0', 10);
                if (f === 15 && !already.featured) {
                    t += prices.featured_15;
                } else if (f === 30 && !already.featured) {
                    t += prices.featured_30;
                }
                var h = parseInt($('input[name="promote_highlighted"]:checked').val() || '0', 10);
                if (h === 1 && !already.highlighted) {
                    t += prices.highlighted;
                }
                return t;
            }
            function refreshJobPromoTotal() {
                var total = jobPromoPayableTotal();
                var $panel = $('#job-promotion-total-panel');
                var $amt = $('#job-promotion-total-amount');
                if (total > 0.0001) {
                    $panel.show();
                    $amt.text(currency + total.toFixed(2));
                } else {
                    $panel.hide();
                }
            }
            $(document).on('change', 'input[name="promote_featured_days"], input[name="promote_urgent_days"], input[name="promote_highlighted"]', refreshJobPromoTotal);
            refreshJobPromoTotal();
        })();

        var initialStateId = {{ (int) old('state_id', (isset($job)) ? $job->state_id : 0) }};

        if (window.__jobPostingLocationLevel === 1) {
            window.filterJobCitiesByCountry(window.__jobPostingInitialCityId);
            setTimeout(function () { window.syncJobPostingOtherFields(); }, 0);
        } else {
            $('#country_id').on('change', function (e) {
                e.preventDefault();
                window.filterJobLangStates(0);
            });
            $(document).on('change', '#state_id', function (e) {
                e.preventDefault();
                window.filterJobCitiesByState(0);
            });
            window.filterJobLangStates(initialStateId);
        }

        let questionIndex = {{ isset($job) && $job->jobQuestions->count() > 0 ? $job->jobQuestions->count() : 0 }};

        $('#addQuestionBtn').on('click', function() {
            const questionHtml = `
            <div class="question-item mb-3">
                <div class="input-group">
                    <input type="text" name="questions[${questionIndex}][title]" class="form-control" placeholder="{{__('Enter Question Title')}}" required>
                    <button type="button" class="btn btn-danger remove-question-btn" onclick="removeQuestion(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            $('#questionsContainer').append(questionHtml);
            questionIndex++;
        });
    });

    function removeQuestion(btn) {
        $(btn).closest('.question-item').remove();
    }
</script> 
@endpush
@endif
