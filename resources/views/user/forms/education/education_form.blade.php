<div class="modal-body">
    <div class="form-body">
        <div class="formrow" id="div_degree_level_id">
            <?php
            $degree_level_id = (isset($profileEducation) ? $profileEducation->degree_level_id : null);
            ?>
            {!! Form::select('degree_level_id', [''=>__('Select Highest Level of Education')]+$degreeLevels, $degree_level_id, array('class'=>'form-control', 'id'=>'degree_level_id')) !!}
            <span class="help-block degree_level_id-error"></span> </div>


        <div class="formrow" id="div_degree_type_id">
            <?php
            $degree_type_id = (isset($profileEducation) ? $profileEducation->degree_type_id : null);
            ?>
            <span id="degree_types_dd">
                {!! Form::select('degree_type_id', [''=>__('Select Certification Type')], $degree_type_id, array('class'=>'form-control', 'id'=>'degree_type_id')) !!}
            </span>
            <span class="help-block degree_type_id-error"></span> </div>

        <div class="formrow" id="div_degree_title">
            <input class="form-control" id="degree_title" placeholder="{{__('Type Certification Title')}}" name="degree_title" type="text" value="{{(isset($profileEducation)? $profileEducation->degree_title:'')}}">
            <span class="help-block degree_title-error"></span> </div>
            
            
        <div class="formrow" id="div_institution">
            <input class="form-control" id="institution" placeholder="{{__('School Name')}}" name="institution" type="text" value="{{(isset($profileEducation)? $profileEducation->institution:'')}}">
            <span class="help-block institution-error"></span> </div>
            
            
        <div class="formrow" id="div_date_completion">
            <?php
            $date_completion = (isset($profileEducation) ? $profileEducation->date_completion : null);
            ?>
            {!! Form::select('date_completion', [''=>__('Year Graduated')]+MiscHelper::getEstablishedIn(), $date_completion, array('class'=>'form-control', 'id'=>'date_completion')) !!}
            <span class="help-block date_completion-error"></span> </div>
            

        @if(\App\Helpers\LocationHelper::showCountry())
        <div class="formrow" id="div_country_id">
            <?php
            $country_id = (isset($profileEducation) ? $profileEducation->country_id : $siteSetting->default_country_id);
            ?>
            {!! Form::select('country_id', [''=>__('Select Country')]+$countries, $country_id, array('class'=>'form-control', 'id'=>'education_country_id')) !!}
            <span class="help-block country_id-error"></span> </div>
        @endif

        @if(\App\Helpers\LocationHelper::showState())
        <div class="formrow" id="div_state_id">
            <span id="default_state_education_dd">
                {!! Form::select('state_id', [''=>__('Select State')], null, array('class'=>'form-control', 'id'=>'education_state_id')) !!}
            </span>
            <span class="help-block state_id-error"></span> </div>
        @endif

        @if(\App\Helpers\LocationHelper::showCity())
        <div class="formrow" id="div_city_id">
            <span id="default_city_education_dd">
                {!! Form::select('city_id', [''=>__('Select City')], null, array('class'=>'form-control', 'id'=>'city_id')) !!}
            </span>
            <span class="help-block city_id-error"></span> </div>
        @endif


    </div>
</div>