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
<div class="row">  
    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'title') !!}"> {!! Form::text('title', null, array('class'=>'form-control', 'id'=>'title', 'placeholder'=>__('Job title'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'title') !!} </div>
    </div>
    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'description') !!}">
           <label for="">Description</label>
        {!! Form::textarea('description', null, array('class'=>'form-control', 'id'=>'description', 'placeholder'=>__('Job description'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'description') !!} </div>
    </div>
	
	 <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'benefits') !!}">
        <label for="">Benefits</label>    
        {!! Form::textarea('benefits', null, array('class'=>'form-control', 'id'=>'benefits', 'placeholder'=>__('Job Benefits'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'benefits') !!} </div>
    </div>
	
	
    <div class="col-md-12">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'skills') !!}">
        <label for="">Skills</label>    
            <?php
            $skills = old('skills', $jobSkillIds);
            ?>
            @php
                $jobSkillsWithOther = $jobSkills + ['0' => __('Other — add custom skills below')];
            @endphp
            {!! Form::select('skills[]', $jobSkillsWithOther, $skills, array('class'=>'form-control select2-multiple', 'id'=>'skills', 'multiple'=>'multiple')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'skills') !!}
            <small class="text-muted d-block mt-1">{{ __('If your skills are not listed, choose “Other” and type one skill per line below.') }}</small>
            <div id="custom_job_skills_wrap_job" class="mt-2" style="display:none;">
                <label>{{ __('Custom skills (one per line)') }}</label>
                {!! Form::textarea('custom_job_skills_lines', old('custom_job_skills_lines'), array('class'=>'form-control', 'id'=>'custom_job_skills_lines', 'rows'=>3, 'placeholder'=>__('e.g. Pediatric care'))) !!}
                {!! APFrmErrHelp::showErrors($errors, 'custom_job_skills_lines') !!}
            </div>
            </div>
    </div>
    <div class="col-md-12">
        @include('includes.custom_fields_for_context', [
            'context' => \App\Models\CustomField::CONTEXT_JOB_LISTING,
            'values' => old('custom_fields', (isset($job) ? ($job->custom_field_data ?? []) : [])),
        ])
    </div>
    @if(\App\Helpers\LocationHelper::showCountry())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'country_id') !!}" id="country_id_div"> {!! Form::select('country_id', ['' => __('Select Country')]+$countries, old('country_id', (isset($job))? $job->country_id:$siteSetting->default_country_id), array('class'=>'form-control', 'id'=>'country_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'country_id') !!} </div>
    </div>
    @elseif(\App\Helpers\LocationHelper::showState() || \App\Helpers\LocationHelper::showCity())
    {{-- Country hidden: use default country from site settings (Canada, India, etc.) for state/city lists --}}
    {!! Form::hidden('country_id', old('country_id', (isset($job) ? $job->country_id : null) ?? $siteSetting->default_country_id), ['id' => 'country_id']) !!}
    @endif
    
    @if(\App\Helpers\LocationHelper::showState())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'state_id') !!}" id="state_id_div"> <span id="default_state_dd"> {!! Form::select('state_id', ['' => __('Select State/Province')], null, array('class'=>'form-control', 'id'=>'state_id')) !!} </span> {!! APFrmErrHelp::showErrors($errors, 'state_id') !!} </div>
    </div>
    @endif
    
    @if(\App\Helpers\LocationHelper::showCity())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'city_id') !!}" id="city_id_div"> <span id="default_city_dd"> {!! Form::select('city_id', ['' => __('Select City')]+['0'=>__('Other (specify)')], null, array('class'=>'form-control', 'id'=>'city_id')) !!} </span> {!! APFrmErrHelp::showErrors($errors, 'city_id') !!} </div>
    </div>
    <div class="col-md-12" id="custom_city_name_wrap_job" style="display:none;">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'custom_city_name') !!}">
            <label>{{ __('Custom city') }}</label>
            {!! Form::text('custom_city_name', old('custom_city_name'), array('class'=>'form-control', 'id'=>'custom_city_name', 'maxlength'=>30)) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_city_name') !!}
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_from') !!}" id="salary_from_div"> {!! Form::number('salary_from', null, array('class'=>'form-control', 'id'=>'salary_from', 'placeholder'=>__('Salary from'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_from') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_to') !!}" id="salary_to_div">
            {!! Form::number('salary_to', null, array('class'=>'form-control', 'id'=>'salary_to', 'placeholder'=>__('Salary to'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_to') !!} </div>
    </div>
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_currency') !!}" id="salary_currency_div">
            @php
            $salary_currency = Request::get('salary_currency', (isset($job))? $job->salary_currency:$siteSetting->default_currency_code);
            @endphp

            {!! Form::select('salary_currency', ['' => __('Select Salary Currency')]+$currencies, $salary_currency, array('class'=>'form-control', 'id'=>'salary_currency')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_currency') !!} </div>
    </div>
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'salary_period_id') !!}" id="salary_period_id_div"> {!! Form::select('salary_period_id', ['' => __('Select Salary Period')]+$salaryPeriods, null, array('class'=>'form-control', 'id'=>'salary_period_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'salary_period_id') !!} </div>
    </div>
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'hide_salary') !!}"> {!! Form::label('hide_salary', __('Hide Salary?'), ['class' => 'bold']) !!}
            <div class="radio-list">
                <?php
                $hide_salary_1 = '';
                $hide_salary_2 = 'checked="checked"';
                if (old('hide_salary', ((isset($job)) ? $job->hide_salary : 0)) == 1) {
                    $hide_salary_1 = 'checked="checked"';
                    $hide_salary_2 = '';
                }
                ?>
                <label class="radio-inline">
                    <input id="hide_salary_yes" name="hide_salary" type="radio" value="1" {{$hide_salary_1}}>
                    {{__('Yes')}} </label>
                <label class="radio-inline">
                    <input id="hide_salary_no" name="hide_salary" type="radio" value="0" {{$hide_salary_2}}>
                    {{__('No')}} </label>
            </div>
            {!! APFrmErrHelp::showErrors($errors, 'hide_salary') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'career_level_id') !!}" id="career_level_id_div"> {!! Form::select('career_level_id', ['' => __('Select Career level (optional)')]+$careerLevels, null, array('class'=>'form-control', 'id'=>'career_level_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'career_level_id') !!} </div>
    </div>

    <div class="col-md-6">
        @php
            $functionalAreasWithOther = $functionalAreas + ['0' => __('Other (specify)')];
        @endphp
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'functional_area_id') !!}" id="functional_area_id_div"> {!! Form::select('functional_area_id', ['' => __('Select Job Category')]+$functionalAreasWithOther, null, array('class'=>'form-control', 'id'=>'functional_area_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'functional_area_id') !!} </div>
    </div>
    <div class="col-md-12" id="custom_functional_area_wrap_job" style="display:none;">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'custom_functional_area') !!}">
            <label>{{ __('Custom job category') }}</label>
            {!! Form::text('custom_functional_area', old('custom_functional_area'), array('class'=>'form-control', 'maxlength'=>200, 'placeholder'=>__('Enter category name'))) !!}
            {!! APFrmErrHelp::showErrors($errors, 'custom_functional_area') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_type_id') !!}" id="job_type_id_div"> {!! Form::select('job_type_id', ['' => __('Select Job Type')]+$jobTypes, null, array('class'=>'form-control', 'id'=>'job_type_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_type_id') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_shift_id') !!}" id="job_shift_id_div"> {!! Form::select('job_shift_id', ['' => __('Select Job Shift')]+$jobShifts, null, array('class'=>'form-control', 'id'=>'job_shift_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_shift_id') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'num_of_positions') !!}" id="num_of_positions_div"> {!! Form::select('num_of_positions', ['' => __('Select number of Positions')]+MiscHelper::getNumPositions(), null, array('class'=>'form-control', 'id'=>'num_of_positions')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'num_of_positions') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'gender_id') !!}" id="gender_id_div"> {!! Form::select('gender_id', ['' => __('Gender (optional)')]+$genders, null, array('class'=>'form-control', 'id'=>'gender_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gender_id') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'expiry_date') !!}"> {!! Form::text('expiry_date', null, array('class'=>'form-control datepicker', 'id'=>'expiry_date', 'placeholder'=>__('Application Deadline date'), 'autocomplete'=>'off')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'expiry_date') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'display_duration_days') !!}">
            <label for="display_duration_days" class="bold">
                <i class="fas fa-calendar-alt"></i> {{__('Job Display Duration (optional)')}}
            </label>
            @php
                // Get duration options from site settings (or use defaults)
                $availableDurations = json_decode($siteSetting->job_duration_options ?? '[30,60,90,120,180,365]', true);
                $durationOptions = [];
                foreach ($availableDurations as $days) {
                    if ($days >= 365) {
                        $durationOptions[$days] = ($days / 365) . ' ' . __('Year') . (($days / 365) > 1 ? 's' : '');
                    } elseif ($days >= 30) {
                        $months = round($days / 30);
                        $durationOptions[$days] = $days . ' ' . __('Days') . ' (~' . $months . ' ' . __('months') . ')';
                    } else {
                        $durationOptions[$days] = $days . ' ' . __('Days');
                    }
                }
                $defaultDuration = old('display_duration_days', (isset($job) ? $job->display_duration_days : ($siteSetting->default_job_duration ?? 30)));
            @endphp
            {!! Form::select('display_duration_days', $durationOptions, $defaultDuration, array('class'=>'form-control', 'id'=>'display_duration_days')) !!}
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> {{__('How long this job will be visible on the site')}}
                @if(isset($job) && $job->display_end_date)
                 <br><strong>{{ __('Current display ends') }}: {{ \Carbon\Carbon::parse($job->display_end_date)->format('M d, Y') }}</strong>
                @endif
            </small>
            {!! APFrmErrHelp::showErrors($errors, 'display_duration_days') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'degree_level_id') !!}" id="degree_level_id_div"> {!! Form::select('degree_level_id', ['' =>__('Select Required Degree Level')]+$degreeLevels, null, array('class'=>'form-control', 'id'=>'degree_level_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'degree_level_id') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'job_experience_id') !!}" id="job_experience_id_div"> {!! Form::select('job_experience_id', ['' => __('Select Required job experience')]+$jobExperiences, null, array('class'=>'form-control', 'id'=>'job_experience_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'job_experience_id') !!} </div>
    </div>
    <div class="col-md-6">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'is_freelance') !!}"> {!! Form::label('is_freelance', __('Is Freelance?'), ['class' => 'bold']) !!}
            <div class="radio-list">
                <?php
                $is_freelance_1 = '';
                $is_freelance_2 = 'checked="checked"';
                if (old('is_freelance', ((isset($job)) ? $job->is_freelance : 0)) == 1) {
                    $is_freelance_1 = 'checked="checked"';
                    $is_freelance_2 = '';
                }
                ?>
                <label class="radio-inline">
                    <input id="is_freelance_yes" name="is_freelance" type="radio" value="1" {{$is_freelance_1}}>
                    {{__('Yes')}} </label>
                <label class="radio-inline">
                    <input id="is_freelance_no" name="is_freelance" type="radio" value="0" {{$is_freelance_2}}>
                    {{__('No')}} </label>
            </div>
            {!! APFrmErrHelp::showErrors($errors, 'is_freelance') !!} </div>
    </div>



    

    <div class="col-md-12">
    <div class="formrow">
            {!! Form::label('external_job', 'Do you want applicants to apply through an external application link?', ['class' => 'bold']) !!}
            <?php
            $is_external_1 = '';
            $is_external_2 = 'checked="checked"';
            if (old('is_external', ((isset($job)) ? $job->external_job : 'no')) == 'yes') {
                $is_external_1 = 'checked="checked"';
                $is_external_2 = '';
            }
            ?>
            <div class="radio-list">
                <label class="radio-inline">
                    <input id="external" name="external_job" type="radio" value="yes" {{$is_external_1}}>
                    Yes
                </label>
                <label class="radio-inline">
                    <input id="not_external" name="external_job" type="radio" value="no" {{$is_external_2}}>
                    No
                </label>
            </div>
        </div>


    <div class="form-group">
        <div id="externalLinkField" class="formrow" style="display: {{$is_external_1 ? 'block' : 'none'}}">
            {!! Form::label('job_link', 'External Link where applicant will visit and apply for this job.', ['class' => 'bold']) !!}
            {!! Form::text('job_link', isset($job) ? $job->job_link : '', ['class' => 'form-control']) !!}
        </div>

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
    .datepicker>div {
        display: block;
    }
    .job-post-form-actions .job-post-action-btn {
        font-weight: 600;
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
        border-radius: 8px;
    }
    .job-post-form-actions .job-post-action-btn--submit {
        background-color: #2557a7;
        border-color: #2557a7;
        color: #fff;
    }
    .job-post-form-actions .job-post-action-btn--submit:hover,
    .job-post-form-actions .job-post-action-btn--submit:focus {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
    }
</style>
@endpush
@push('scripts')
@include('includes.tinyMCEFront')
<script>
    $(document).ready(function() {
        function toggleExternalLinkField() {
            var isExternalChecked = $('#external').is(':checked');
            if (isExternalChecked) {
                $('#externalLinkField').show();
            } else {
                $('#externalLinkField').hide();
            }
        }
        toggleExternalLinkField();
        $('input[name="external_job"]').change(function() {
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
        var $fa = $('#functional_area_id');
        if ($fa.length) {
            $('#custom_functional_area_wrap_job').toggle(String($fa.val()) === '0');
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
        $(document).on('change', '#functional_area_id, #city_id', function () {
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