{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'image') !!}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: 100px; overflow: hidden; border:1px solid #ddd; display: flex; justify-content: center; align-items: center;">
                    @if(isset($siteSetting))
            {{ ImgUploader::print_image("sitesetting_images/thumb/$siteSetting->site_logo") }}        
            @else
            <img src="{{ asset('/') }}admin_assets/no-image.png" alt="" style="object-fit: cover;"/>
            @endif  
                
                </div>
                    <div class="fileinput-preview fileinput-exists thumbnail mt-2" style="max-width: 200px; max-height: 150px;"> </div>
                    <div> <span class="btn btn-default btn-file"> <span class="fileinput-new"> Site Logo </span> <span class="fileinput-exists"> Change </span> {!! Form::file('image', null, array('id'=>'image')) !!} </span> <a href="javascript:;" class="btn btn-danger red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                </div>
                {!! APFrmErrHelp::showErrors($errors, 'image') !!} </div>
        </div>
       
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'favicon') !!}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 24px; height: 24px; overflow: hidden;"> 
                        @if(isset($siteSetting))
                        {{ ImgUploader::print_image("favicon.ico") }} 
                        @else
                        <img src="{{ asset('/') }}admin_assets/no-image.png" alt="" />
                        @endif
                    </div>
                    <div class="fileinput-preview fileinput-exists thumbnail mt-2" style="max-width: 16px; max-height: 16px;"> </div>
                    <div> <span class="btn btn-default btn-file"> <span class="fileinput-new"> Favicon </span> <span class="fileinput-exists"> Change </span> {!! Form::file('favicon', null, array('id'=>'favicon')) !!} </span> <a href="javascript:;" class="btn btn-danger red fileinput-exists" data-dismiss="fileinput"> Remove </a> </div>
                </div>
                <span id="name-error" class="help-block help-block-error">The favicon must be a file of type/extension ".ico"</span>
            </div>
        </div>
        @if(isset($siteSetting))
        <div class="col-md-6">
                   
        </div>    
        @endif  
    </div>
    
    <hr>
    
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_name') !!}">
        {!! Form::label('site_name', 'Site Name', ['class' => 'bold']) !!}                    
        {!! Form::text('site_name', null, array('class'=>'form-control', 'id'=>'site_name', 'placeholder'=>'Site Name')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_name') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_slogan') !!}">
        {!! Form::label('site_slogan', 'Site Slogan', ['class' => 'bold']) !!}                    
        {!! Form::text('site_slogan', null, array('class'=>'form-control', 'id'=>'site_slogan', 'placeholder'=>'Site Slogan')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_slogan') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_phone_primary') !!}">
        {!! Form::label('site_phone_primary', 'Primary Phone#', ['class' => 'bold']) !!}                    
        {!! Form::text('site_phone_primary', null, array('class'=>'form-control', 'id'=>'site_phone_primary', 'placeholder'=>'Primary Phone#')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_phone_primary') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_phone_secondary') !!}">
        {!! Form::label('site_phone_secondary', 'Secondary Phone#', ['class' => 'bold']) !!}                    
        {!! Form::text('site_phone_secondary', null, array('class'=>'form-control', 'id'=>'site_phone_secondary', 'placeholder'=>'Secondary Phone#')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_phone_secondary') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'mail_from_address') !!}">
        {!! Form::label('mail_from_address', 'From Email Address', ['class' => 'bold']) !!}                    
        {!! Form::text('mail_from_address', null, array('class'=>'form-control', 'id'=>'mail_from_address', 'placeholder'=>'From Email Address')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'mail_from_address') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'mail_from_name') !!}">
        {!! Form::label('mail_from_name', 'From Email Name', ['class' => 'bold']) !!}                    
        {!! Form::text('mail_from_name', null, array('class'=>'form-control', 'id'=>'mail_from_name', 'placeholder'=>'From Email Name')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'mail_from_name') !!}                                       
    </div>    
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'mail_to_address') !!}">
        {!! Form::label('mail_to_address', 'To Email Address', ['class' => 'bold']) !!}                    
        {!! Form::text('mail_to_address', null, array('class'=>'form-control', 'id'=>'mail_to_address', 'placeholder'=>'To Email Address')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'mail_to_address') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'mail_to_name') !!}">
        {!! Form::label('mail_to_name', 'To Email Name', ['class' => 'bold']) !!}                    
        {!! Form::text('mail_to_name', null, array('class'=>'form-control', 'id'=>'mail_to_name', 'placeholder'=>'To Email Name')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'mail_to_name') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'default_country_id') !!}">
        {!! Form::label('default_country_id', 'Default Country', ['class' => 'bold']) !!}                    
        {!! Form::select('default_country_id',$countries, null, array('class'=>'form-control', 'id'=>'default_country_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'default_country_id') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'country_specific_site') !!}">
        {!! Form::label('country_specific_site', 'Make site specific to this Country?', ['class' => 'bold']) !!}        <div class="radio-list">
            <label class="radio-inline">{!! Form::radio('country_specific_site', 1, true, ['id' => 'country_specific_site_yes']) !!} Yes </label>
            <label class="radio-inline">{!! Form::radio('country_specific_site', 0, null, ['id' => 'country_specific_site_no']) !!} No </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'country_specific_site') !!}                                       
    </div>

    <!-- Location Settings Section -->
    <div class="card mb-4" style="border: 1px solid #ddd; border-radius: 8px;">
        <div class="card-header" style="background: #f8f9fa; padding: 15px;">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Location Settings</h5>
        </div>
        <div class="card-body" style="padding: 20px;">
            
            <!-- Location Multiple Fields Toggle -->
            <div class="form-group mb-4 {!! APFrmErrHelp::hasError($errors, 'location_multiple_fields') !!}">
                {!! Form::label('location_multiple_fields', 'Location Multiple Fields', ['class' => 'bold']) !!}
                <div class="radio-list">
                    <label class="radio-inline">
                        {!! Form::radio('location_multiple_fields', 1, null, ['id' => 'location_multiple_fields_yes', 'onchange' => 'toggleLocationFields()']) !!} Yes
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('location_multiple_fields', 0, null, ['id' => 'location_multiple_fields_no', 'onchange' => 'toggleLocationFields()']) !!} No
                    </label>
                </div>
                <small class="form-text text-muted">You can set 4 fields for regions like: Country, State, City, District</small>
                {!! APFrmErrHelp::showErrors($errors, 'location_multiple_fields') !!}
            </div>

            <!-- Number of Location Fields -->
            <div id="location_fields_container" style="display: none;">
                <div class="form-group mb-4 {!! APFrmErrHelp::hasError($errors, 'location_levels') !!}">
                    {!! Form::label('location_levels', 'Number Fields', ['class' => 'bold']) !!}
                    {!! Form::select('location_levels', [
                        1 => '1 Field',
                        2 => '2 Fields', 
                        3 => '3 Fields',
                        4 => '4 Fields'
                    ], null, ['class' => 'form-control', 'id' => 'location_levels', 'style' => 'max-width: 200px;', 'onchange' => 'updateLocationLabels()']) !!}
                    <small class="form-text text-muted">You can set 4 fields for regions like: Country, State, City, District</small>
                    {!! APFrmErrHelp::showErrors($errors, 'location_levels') !!}
                </div>

                <!-- Field Labels -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'location_field_1_label') !!}">
                            {!! Form::label('location_field_1_label', 'First Field Label', ['class' => 'bold']) !!}
                            {!! Form::text('location_field_1_label', null, ['class' => 'form-control', 'id' => 'location_field_1_label', 'placeholder' => 'country']) !!}
                            <small class="form-text text-muted">Empty for translate multiple languages</small>
                            {!! APFrmErrHelp::showErrors($errors, 'location_field_1_label') !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'location_field_2_label') !!}">
                            {!! Form::label('location_field_2_label', 'Second Field Label', ['class' => 'bold']) !!}
                            {!! Form::text('location_field_2_label', null, ['class' => 'form-control', 'id' => 'location_field_2_label', 'placeholder' => 'state']) !!}
                            <small class="form-text text-muted">Empty for translate multiple languages</small>
                            {!! APFrmErrHelp::showErrors($errors, 'location_field_2_label') !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'location_field_3_label') !!}">
                            {!! Form::label('location_field_3_label', 'Third Field Label', ['class' => 'bold']) !!}
                            {!! Form::text('location_field_3_label', null, ['class' => 'form-control', 'id' => 'location_field_3_label', 'placeholder' => 'city']) !!}
                            <small class="form-text text-muted">Empty for translate multiple languages</small>
                            {!! APFrmErrHelp::showErrors($errors, 'location_field_3_label') !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'location_field_4_label') !!}">
                            {!! Form::label('location_field_4_label', 'Fourth Field Label', ['class' => 'bold']) !!}
                            {!! Form::text('location_field_4_label', null, ['class' => 'form-control', 'id' => 'location_field_4_label', 'placeholder' => 'district']) !!}
                            <small class="form-text text-muted">Empty for translate multiple languages</small>
                            {!! APFrmErrHelp::showErrors($errors, 'location_field_4_label') !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleLocationFields() {
            const isEnabled = document.getElementById('location_multiple_fields_yes').checked;
            const container = document.getElementById('location_fields_container');
            container.style.display = isEnabled ? 'block' : 'none';
        }

        function updateLocationLabels() {
            const numFields = document.getElementById('location_levels').value;
            // Show/hide label inputs based on number of fields selected
            for (let i = 1; i <= 4; i++) {
                const labelInput = document.querySelector(`[name="location_field_${i}_label"]`);
                if (labelInput) {
                    labelInput.closest('.form-group').style.display = i <= numFields ? 'block' : 'none';
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleLocationFields();
            updateLocationLabels();
        });
    </script>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'auto_approval_company') !!}">
        {!! Form::label('auto_approval_company', 'Auto approve company', ['class' => 'bold']) !!}        <div class="radio-list">
            <label class="radio-inline">{!! Form::radio('auto_approval_company', 1, true, ['id' => 'auto_approval_company']) !!} Yes </label>
            <label class="radio-inline">{!! Form::radio('auto_approval_company', 0, null, ['id' => 'auto_approval_company']) !!} No </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'auto_approval_company') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'auto_approval_job') !!}">
        {!! Form::label('auto_approval_job', 'Auto approve Job', ['class' => 'bold']) !!}        <div class="radio-list">
            <label class="radio-inline">{!! Form::radio('auto_approval_job', 1, true, ['id' => 'auto_approval_job']) !!} Yes </label>
            <label class="radio-inline">{!! Form::radio('auto_approval_job', 0, null, ['id' => 'auto_approval_job']) !!} No </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'auto_approval_job') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'default_currency_code') !!}">
        {!! Form::label('default_currency_code', 'Default Currency Code', ['class' => 'bold']) !!}                    
        {!! Form::select('default_currency_code',$currency_codes, null, array('class'=>'form-control', 'id'=>'default_currency_code')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'default_currency_code') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_street_address') !!}">
        {!! Form::label('site_street_address', 'Street Address', ['class' => 'bold']) !!}                    
        {!! Form::textarea('site_street_address', null, array('class'=>'form-control', 'id'=>'site_street_address', 'placeholder'=>'Street Address')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_street_address') !!}                                       
    </div>
    <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'site_google_map') !!}">
        {!! Form::label('site_google_map', 'Site Google Map', ['class' => 'bold']) !!}                    
        {!! Form::textarea('site_google_map', null, array('class'=>'form-control', 'id'=>'site_google_map', 'placeholder'=>'Site Google Map')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'site_google_map') !!}                                       
    </div>
</div>
