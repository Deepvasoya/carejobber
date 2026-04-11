<?php
$lang = config('default_lang');
if (isset($faqSection))
    $lang = $faqSection->lang;
$lang = MiscHelper::getLang($lang);
$direction = MiscHelper::getLangDirection($lang);
$queryString = MiscHelper::getLangQueryStr();
?>
{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'lang') !!}">
        {!! Form::label('lang', 'Language', ['class' => 'bold']) !!}                    
        {!! Form::select('lang', ['' => 'Select Language']+$languages, $lang, array('class'=>'form-control', 'id'=>'lang', 'onchange'=>'setLang(this.value)')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'lang') !!}                                       
    </div>      
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'name') !!}">
        {!! Form::label('name', 'Section Name', ['class' => 'bold']) !!}                    
        {!! Form::text('name', null, array('class'=>'form-control', 'id'=>'name', 'placeholder'=>'Section Name (e.g., JOBSEEKER, EMPLOYERS, TRAINING)')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'name') !!}                                       
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'description') !!}">
        {!! Form::label('description', 'Description (Optional)', ['class' => 'bold']) !!}                    
        {!! Form::textarea('description', null, array('class'=>'form-control', 'id'=>'description', 'placeholder'=>'Description', 'rows' => 3)) !!}
        {!! APFrmErrHelp::showErrors($errors, 'description') !!}                                       
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'sort_order') !!}">
        {!! Form::label('sort_order', 'Sort Order', ['class' => 'bold']) !!}                    
        {!! Form::number('sort_order', null, array('class'=>'form-control', 'id'=>'sort_order', 'placeholder'=>'Sort Order')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'sort_order') !!}
        <small class="form-text text-muted">Lower numbers appear first</small>                                       
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_active') !!}">
        {!! Form::label('is_active', 'Status', ['class' => 'bold']) !!}                    
        {!! Form::select('is_active', [1 => 'Active', 0 => 'Inactive'], null, array('class'=>'form-control', 'id'=>'is_active')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'is_active') !!}                                       
    </div>
    <div class="form-actions">
        {!! Form::button('Save <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!}
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    function setLang(lang) {
        window.location.href = "<?php echo url(Request::url()) . $queryString; ?>" + lang;
    }
</script>
@endpush
