@php
    $company = Auth::guard('company')->user();
    $hasActivePackage = $company->package_id && $company->package_end_date && \Carbon\Carbon::parse($company->package_end_date)->isFuture();
    $remainingCredits = $hasActivePackage ? ($company->jobs_quota - $company->availed_jobs_quota) : 0;
    $packageName = $hasActivePackage ? ($company->getPackage('package_title') ?? __('Active Package')) : __('No Package');
    $packageExpiry = $hasActivePackage ? \Carbon\Carbon::parse($company->package_end_date)->format('M d, Y') : null;
@endphp

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
            {!! Form::select('skills[]', $jobSkills, $skills, array('class'=>'form-control select2-multiple', 'id'=>'skills', 'multiple'=>'multiple')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'skills') !!} </div>
    </div>
    @if(\App\Helpers\LocationHelper::showCountry())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'country_id') !!}" id="country_id_div"> {!! Form::select('country_id', ['' => __('Select Country')]+$countries, old('country_id', (isset($job))? $job->country_id:$siteSetting->default_country_id), array('class'=>'form-control', 'id'=>'country_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'country_id') !!} </div>
    </div>
    @endif
    
    @if(\App\Helpers\LocationHelper::showState())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'state_id') !!}" id="state_id_div"> <span id="default_state_dd"> {!! Form::select('state_id', ['' => __('Select State/Province')], null, array('class'=>'form-control', 'id'=>'state_id')) !!} </span> {!! APFrmErrHelp::showErrors($errors, 'state_id') !!} </div>
    </div>
    @endif
    
    @if(\App\Helpers\LocationHelper::showCity())
    <div class="col-md-4">
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'city_id') !!}" id="city_id_div"> <span id="default_city_dd"> {!! Form::select('city_id', ['' => __('Select City')], null, array('class'=>'form-control', 'id'=>'city_id')) !!} </span> {!! APFrmErrHelp::showErrors($errors, 'city_id') !!} </div>
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
        <div class="formrow {!! APFrmErrHelp::hasError($errors, 'functional_area_id') !!}" id="functional_area_id_div"> {!! Form::select('functional_area_id', ['' => __('Select Job Category')]+$functionalAreas, null, array('class'=>'form-control', 'id'=>'functional_area_id')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'functional_area_id') !!} </div>
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
            <p class="text-muted" style="font-size: 14px;">{{__('Optional. Urgent jobs appear first in search, then featured. Highlight adds a coloured background to your card.')}}</p>
            @php
                $isErr = isset($errors) && $errors->any();
                $pu = $isErr ? (bool) old('promote_urgent') : (isset($job) && !empty($job->is_urgent));
                $pf = $isErr ? (bool) old('promote_featured') : (isset($job) && !empty($job->is_featured));
                $ph = $isErr ? (bool) old('promote_highlighted') : (isset($job) && !empty($job->is_highlighted));
            @endphp
            <div class="row">
                <div class="col-md-4 col-sm-12 mb-2">
                    <label class="d-flex align-items-start" style="cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="promote_urgent" value="1" class="mt-1 me-2" @if($pu) checked @endif>
                        <span><i class="fas fa-fire text-danger"></i> {{__('Urgent')}}<br><small class="text-muted">{{__('Top of search results')}}</small></span>
                    </label>
                </div>
                <div class="col-md-4 col-sm-12 mb-2">
                    <label class="d-flex align-items-start" style="cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="promote_featured" value="1" class="mt-1 me-2" @if($pf) checked @endif>
                        <span><i class="fas fa-bolt text-warning"></i> {{__('Featured listing')}}<br><small class="text-muted">{{__('Shown after urgent jobs')}}</small></span>
                    </label>
                </div>
                <div class="col-md-4 col-sm-12 mb-2">
                    <label class="d-flex align-items-start" style="cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="promote_highlighted" value="1" class="mt-1 me-2" @if($ph) checked @endif>
                        <span><i class="fas fa-highlighter text-info"></i> {{__('Highlighted')}}<br><small class="text-muted">{{__('Distinct background on listings')}}</small></span>
                    </label>
                </div>
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
        <div class="formrow">
            <button type="submit" class="btn">{{__('Submit Job')}} <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
        </div>
    </div>
</div>
<input type="file" name="image" id="image" style="display:none;" accept="image/*"/>
{!! Form::close() !!}


@push('styles')
<style type="text/css">
    .datepicker>div {
        display: block;
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
    $(document).ready(function () {
        $('.select2-multiple').select2({
            placeholder: "{{__('Select Required Skills')}}",
            allowClear: true
        });
        $(".datepicker").datepicker({
            autoclose: true,
            startDate: new Date(),
            format: 'yyyy-m-d'
        });
        $('#country_id').on('change', function (e) {
            e.preventDefault();
            filterLangStates(0);
        });
        $(document).on('change', '#state_id', function (e) {
            e.preventDefault();
            filterLangCities(0);
        });
        filterLangStates(<?php echo old('state_id', (isset($job)) ? $job->state_id : 0); ?>);
    });
    function filterLangStates(state_id)
    {
        var country_id = $('#country_id').val();
        if (country_id != '') {
            $.post("{{ route('filter.lang.states.dropdown') }}", {country_id: country_id, state_id: state_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_state_dd').html(response);
                        filterLangCities(<?php echo old('city_id', (isset($job)) ? $job->city_id : 0); ?>);
                    });
        }
    }
    function filterLangCities(city_id)
    {
        var state_id = $('#state_id').val();
        if (state_id != '') {
            $.post("{{ route('filter.lang.cities.dropdown') }}", {state_id: state_id, city_id: city_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_city_dd').html(response);
                    });
        }
    }
    
    // Questions Management
    let questionIndex = {{isset($job) && $job->jobQuestions->count() > 0 ? $job->jobQuestions->count() : 0}};
    
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
    
    function removeQuestion(btn) {
        $(btn).closest('.question-item').remove();
    }
</script> 
@endpush