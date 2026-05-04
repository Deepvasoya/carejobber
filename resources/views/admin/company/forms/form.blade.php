{!! APFrmErrHelp::showOnlyErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'logo') !!}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;"> <img src="{{ asset('/') }}admin_assets/no-image.png" alt="" /> </div>
                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                    <div> <span class="btn default btn-file"> <span class="fileinput-new"> Select Company logo </span> <span class="fileinput-exists"> Change </span> {!! Form::file('logo', null, array('id'=>'logo')) !!} </span> <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                </div>
                {!! APFrmErrHelp::showErrors($errors, 'logo') !!} </div>
        </div>
        @if(isset($company))
        <div class="col-md-6">
            {{ ImgUploader::print_image("company_logos/$company->logo") }}        
        </div>    
        @endif  
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'name') !!}"> {!! Form::label('name', 'Company Name', ['class' => 'bold']) !!}
        {!! Form::text('name', null, array('class'=>'form-control', 'id'=>'name', 'placeholder'=>'Company Name')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'name') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'email') !!}"> {!! Form::label('email', 'Company Email', ['class' => 'bold']) !!}
        {!! Form::text('email', null, array('class'=>'form-control', 'id'=>'email', 'placeholder'=>'Company Email')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'email') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'password') !!}"> {!! Form::label('password', 'Password', ['class' => 'bold']) !!}
        {!! Form::password('password', array('class'=>'form-control', 'id'=>'password', 'placeholder'=>'Password')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'password') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'ceo') !!}"> {!! Form::label('ceo', 'Company CEO', ['class' => 'bold']) !!}
        {!! Form::text('ceo', null, array('class'=>'form-control', 'id'=>'ceo', 'placeholder'=>'Company CEO')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'ceo') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'industry_id') !!}"> {!! Form::label('industry_id', 'Industry', ['class' => 'bold']) !!}                    
        {!! Form::select('industry_id', ['' => 'Select Industry']+$industries, null, array('class'=>'form-control', 'id'=>'industry_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'industry_id') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'ownership_type') !!}"> {!! Form::label('ownership_type', 'Ownership Type', ['class' => 'bold']) !!}
        {!! Form::select('ownership_type_id', ['' => 'Select Ownership type']+$ownershipTypes, null, array('class'=>'form-control', 'id'=>'ownership_type_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'ownership_type_id') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'description') !!}"> {!! Form::label('description', 'Company details', ['class' => 'bold']) !!}
        {!! Form::textarea('description', null, array('class'=>'form-control', 'id'=>'description', 'placeholder'=>'Company details')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'description') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'location') !!}"> {!! Form::label('location', 'Location', ['class' => 'bold']) !!}
        {!! Form::text('location', null, array('class'=>'form-control', 'id'=>'location', 'placeholder'=>'Location')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'location') !!} </div>     
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'map') !!}"> {!! Form::label('map', 'Google Map', ['class' => 'bold']) !!}
        {!! Form::textarea('map', null, array('class'=>'form-control', 'id'=>'map', 'placeholder'=>'Google Map')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'map') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'no_of_offices') !!}"> {!! Form::label('no_of_offices', 'Number of offices', ['class' => 'bold']) !!}
        {!! Form::select('no_of_offices', ['' => 'Select num. of offices']+MiscHelper::getNumOffices(), null, array('class'=>'form-control', 'id'=>'no_of_offices')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'no_of_offices') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'website') !!}"> {!! Form::label('website', 'Website', ['class' => 'bold']) !!}
        {!! Form::text('website', null, array('class'=>'form-control', 'id'=>'website', 'placeholder'=>'Website')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'website') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'no_of_employees') !!}"> {!! Form::label('no_of_employees', 'Number of employees', ['class' => 'bold']) !!}
        {!! Form::select('no_of_employees', ['' => 'Select num. of employees']+MiscHelper::getNumEmployees(), null, array('class'=>'form-control', 'id'=>'no_of_employees')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'no_of_employees') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'established_in') !!}"> {!! Form::label('established_in', 'Established in', ['class' => 'bold']) !!}
        {!! Form::select('established_in', ['' => 'Select Established In']+MiscHelper::getEstablishedIn(), null, array('class'=>'form-control', 'id'=>'established_in')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'established_in') !!} </div>
 
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'phone') !!}"> {!! Form::label('phone', 'Phone #', ['class' => 'bold']) !!}
        {!! Form::text('phone', null, array('class'=>'form-control', 'id'=>'phone', 'placeholder'=>'Phone #')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'phone') !!} </div>




    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'facebook') !!}"> {!! Form::label('facebook', 'Facebook Address', ['class' => 'bold']) !!}
        {!! Form::text('facebook', null, array('class'=>'form-control', 'id'=>'facebook', 'placeholder'=>'Facebook address')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'facebook') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'twitter') !!}"> {!! Form::label('twitter', 'Twitter', ['class' => 'bold']) !!}
        {!! Form::text('twitter', null, array('class'=>'form-control', 'id'=>'twitter', 'placeholder'=>'Twitter')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'twitter') !!} </div>

    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'linkedin') !!}"> {!! Form::label('linkedin', 'Linkedin', ['class' => 'bold']) !!}
        {!! Form::text('linkedin', null, array('class'=>'form-control', 'id'=>'linkedin', 'placeholder'=>'Linkedin')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'linkedin') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'google_plus') !!}"> {!! Form::label('google_plus', 'Google+', ['class' => 'bold']) !!}
        {!! Form::text('google_plus', null, array('class'=>'form-control', 'id'=>'google_plus', 'placeholder'=>'Google+')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'google_plus') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'pinterest') !!}"> {!! Form::label('pinterest', 'Pinterest', ['class' => 'bold']) !!}
        {!! Form::text('pinterest', null, array('class'=>'form-control', 'id'=>'pinterest', 'placeholder'=>'Pinterest')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'pinterest') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'country_id') !!}"> {!! Form::label('country_id', 'Country', ['class' => 'bold']) !!}                    
        {!! Form::select('country_id', ['' => 'Select Country']+$countries, old('country_id', (isset($company))? $company->country_id:$siteSetting->default_country_id), array('class'=>'form-control', 'id'=>'country_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'country_id') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'state_id') !!}"> {!! Form::label('state_id', 'State', ['class' => 'bold']) !!}
        <span id="default_state_dd">                    
            {!! Form::select('state_id', ['' => 'Select State'], null, array('class'=>'form-control', 'id'=>'state_id')) !!}
        </span>
        {!! APFrmErrHelp::showErrors($errors, 'state_id') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'city_id') !!}"> {!! Form::label('city_id', 'City', ['class' => 'bold']) !!}  
        <span id="default_city_dd">                  
            {!! Form::select('city_id', ['' => 'Select City'], null, array('class'=>'form-control', 'id'=>'city_id')) !!}
        </span>
        {!! APFrmErrHelp::showErrors($errors, 'city_id') !!} </div>

        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'company_package_id') !!}">
            {!! Form::label('company_package_id', 'Employer Package', ['class' => 'bold']) !!}
            {!! Form::select('company_package_id', 
                ['' => 'Select Employer Package'] + $employerPackages, 
                old('company_package_id', $selectedEmployerPackage), 
                ['class' => 'form-control', 'id' => 'company_package_id']
            ) !!}
            {!! APFrmErrHelp::showErrors($errors, 'company_package_id') !!}
        </div>



<div class="form-group mb-3">
    {!! Form::label('cv_package_id', 'CV Package', ['class' => 'bold']) !!}
    {!! Form::select('cv_package_id', ['' => 'Select CV Package'] + $cvSearchPackages, $selectedCvPackage, ['class' => 'form-control', 'id' => 'cv_package_id']) !!}
</div>




@if(isset($company) && $company->package_id > 0)
    <div class="form-group mb-3">
        {!! Form::label('package', 'Employer Package : ', ['class' => 'bold']) !!}
        <strong>{{ optional($company->getPackage())->package_title }}</strong>
    </div>
    <div class="form-group mb-3">
        {!! Form::label('package_Duration', 'Package Duration : ', ['class' => 'bold']) !!}
        <strong>{{ optional($company->package_start_date)->format('d M, Y') }}</strong> -
        <strong>{{ optional($company->package_end_date)->format('d M, Y') }}</strong>
    </div>
    <div class="form-group mb-3">
        {!! Form::label('package_quota', 'Availed quota : ', ['class' => 'bold']) !!}
        <strong>{{ $company->availed_jobs_quota }}</strong> / <strong>{{ $company->jobs_quota }}</strong>
    </div>
    <hr/>
@endif

@if(isset($company) && $company->cvs_package_id > 0)
    <div class="form-group mb-3">
        {!! Form::label('cvs_package', 'CV Search Package : ', ['class' => 'bold']) !!}
        <strong>{{ optional($company->cvs_getPackage())->package_title }}</strong>
    </div>

    <div class="form-group mb-3">
        {!! Form::label('cvs_package_Duration', 'CV Package Duration : ', ['class' => 'bold']) !!}
        <strong>{{ $company->cvs_package_start_date ? \Carbon\Carbon::parse($company->cvs_package_start_date)->format('d M, Y') : 'N/A' }}</strong> -
<strong>{{ $company->cvs_package_end_date ? \Carbon\Carbon::parse($company->cvs_package_end_date)->format('d M, Y') : 'N/A' }}</strong>

    </div>

    <div class="form-group mb-3">
        {!! Form::label('cvs_quota', 'CV unlock credits (used / total) : ', ['class' => 'bold']) !!}
        <strong>{{ $company->availed_cvs_quota ?? 0 }}</strong> /
        <strong>{{ $company->cvs_quota ?? 0 }}</strong>
        <span class="text-muted">({{ __(':remaining remaining', ['remaining' => max(0, (int) ($company->cvs_quota ?? 0) - (int) ($company->availed_cvs_quota ?? 0))]) }})</span>
    </div>
@endif





  



    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_active') !!}">
        {!! Form::label('is_active', 'Is Active?', ['class' => 'bold']) !!}
        <div class="radio-list">
            <?php
            $is_active_1 = 'checked="checked"';
            $is_active_2 = '';
            if (old('is_active', ((isset($company)) ? $company->is_active : 1)) == 0) {
                $is_active_1 = '';
                $is_active_2 = 'checked="checked"';
            }
            ?>
            <label class="radio-inline">
                <input id="active" name="is_active" type="radio" value="1" {{$is_active_1}}>
                Active </label>
            <label class="radio-inline">
                <input id="not_active" name="is_active" type="radio" value="0" {{$is_active_2}}>
                In-Active </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'is_active') !!}
    </div>



    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_featured') !!}">
        {!! Form::label('is_featured', 'Is Featured?', ['class' => 'bold']) !!}
        <div class="radio-list">
            <?php
            $is_featured_1 = '';
            $is_featured_2 = 'checked="checked"';
            if (old('is_featured', ((isset($company)) ? $company->is_featured : 0)) == 1) {
                $is_featured_1 = 'checked="checked"';
                $is_featured_2 = '';
            }
            ?>
            <label class="radio-inline">
                <input id="featured" name="is_featured" type="radio" value="1" {{$is_featured_1}}>
                Featured </label>
            <label class="radio-inline">
                <input id="not_featured" name="is_featured" type="radio" value="0" {{$is_featured_2}}>
                Not Featured </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'is_featured') !!} </div>
    
    @if(isset($company))
    <div class="form-group mb-3">
        {!! Form::label('employer_trust_status', 'Employer Verification Status', ['class' => 'bold']) !!}
        <div class="alert alert-info">
            <strong>Current Status:</strong> 
            <span class="badge {{ $company->getVerificationBadgeClass() }}">
                {{ $company->getVerificationStatusText() }}
            </span>
        </div>
        <div class="btn-group btn-group-justified" role="group">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger {{ $company->getEmployerTrustStatus() === 'unverified' ? 'active' : '' }}" 
                        onclick="setEmployerTrustStatus({{ $company->id }}, 'unverified')">
                    🔴 Unverified
                </button>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-warning {{ $company->getEmployerTrustStatus() === 'reviewed' ? 'active' : '' }}" 
                        onclick="setEmployerTrustStatus({{ $company->id }}, 'reviewed')">
                    🟡 Reviewed
                </button>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success {{ $company->getEmployerTrustStatus() === 'verified' ? 'active' : '' }}" 
                        onclick="setEmployerTrustStatus({{ $company->id }}, 'verified')">
                    🟢 Verified
                </button>
            </div>
        </div>
        <small class="form-text text-muted mt-2">
            <strong>Unverified:</strong> Max 2 active jobs, no resume access<br>
            <strong>Reviewed:</strong> Max 3 active jobs, no resume access<br>
            <strong>Verified:</strong> Unlimited jobs (with package), full resume access
        </small>
    </div>
    @endif
    
    <div class="form-actions"> {!! Form::button('Update <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!} </div>
</div>
@push('scripts')
@include('admin.shared.tinyMCEFront') 
<script type="text/javascript">
    $(document).ready(function () {
        $('#country_id').on('change', function (e) {
            e.preventDefault();
            filterDefaultStates(0);
        });
        $(document).on('change', '#state_id', function (e) {
            e.preventDefault();
            filterDefaultCities(0);
        });
        filterDefaultStates(<?php echo old('state_id', (isset($company)) ? $company->state_id : 0); ?>);
    });
    function filterDefaultStates(state_id)
    {
        var country_id = $('#country_id').val();
        if (country_id != '') {
            $.post("{{ route('filter.default.states.dropdown') }}", {country_id: country_id, state_id: state_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_state_dd').html(response);
                        filterDefaultCities(<?php echo old('city_id', (isset($company)) ? $company->city_id : 0); ?>);
                    });
        }
    }
    function filterDefaultCities(city_id)
    {
        var state_id = $('#state_id').val();
        if (state_id != '') {
            $.post("{{ route('filter.default.cities.dropdown') }}", {state_id: state_id, city_id: city_id, _method: 'POST', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        $('#default_city_dd').html(response);
                    });
        }
    }
    
    function setEmployerTrustStatus(companyId, status) {
        if (!confirm('Are you sure you want to set this employer\'s verification status to "' + status + '"?')) {
            return;
        }
        
        $.ajax({
            url: "{{ url('admin/company') }}/" + companyId + "/set-trust-status",
            type: 'POST',
            data: {
                trust_status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error updating status. Please try again.');
            }
        });
    }
</script>
@endpush