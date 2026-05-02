<?php
$lang = config('default_lang');
if (isset($jobCategory))
    $lang = $jobCategory->lang;
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
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'job_category') !!}">
        {!! Form::label('job_category', 'Job Category', ['class' => 'bold']) !!}                    
        {!! Form::text('job_category', null, array('class'=>'form-control', 'id'=>'job_category', 'placeholder'=>'Job Category', 'dir'=>$direction)) !!}
        {!! APFrmErrHelp::showErrors($errors, 'job_category') !!}                                       
    </div>
    <div class="form-group mb-3">
            <label class="control-label" for="Upload Image">Image</label>
            <input type="file" class="form-control" name="image" id="image" autofocus>
            <div class="image_append" id="image_append">
                @if((isset($jobCategory)) && $jobCategory->image!='')
                <br>
                <div class='featured-images-main' id='listing_img_{{$jobCategory->id}}'><img  src="{{asset('uploads/job_category/thumbnail/')}}/{{$jobCategory->image}}"></div>
                @endif
            </div>
        </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_default') !!}">
        {!! Form::label('is_default', 'Is Default?', ['class' => 'bold']) !!}
        <div class="radio-list">
            <?php
            $is_default_1 = 'checked="checked"';
            $is_default_2 = '';
            if (old('is_default', ((isset($jobCategory)) ? $jobCategory->is_default : 1)) == 0) {
                $is_default_1 = '';
                $is_default_2 = 'checked="checked"';
            }
            ?>
            <label class="radio-inline">
                <input id="default" name="is_default" type="radio" value="1" {{$is_default_1}} onchange="showHideJobCategoryId();">
                Yes </label>
            <label class="radio-inline">
                <input id="not_default" name="is_default" type="radio" value="0" {{$is_default_2}} onchange="showHideJobCategoryId();">
                No </label>
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'is_default') !!}
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'job_category_id') !!}" id="job_category_id_div">
        {!! Form::label('job_category_id', 'Default Job Category', ['class' => 'bold']) !!}                    
        {!! Form::select('job_category_id', ['' => 'Select Default Job Category']+$jobCategories, null, array('class'=>'form-control', 'id'=>'job_category_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'job_category_id') !!}                                       
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_active') !!}">
        {!! Form::label('is_active', 'Active', ['class' => 'bold']) !!}
        <div class="radio-list">
            <?php
            $is_active_1 = 'checked="checked"';
            $is_active_2 = '';
            if (old('is_active', ((isset($jobCategory)) ? $jobCategory->is_active : 1)) == 0) {
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
    <div class="form-actions">
        {!! Form::button('Update <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!}
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    function setLang(lang) {
        window.location.href = "<?php echo url(Request::url()) . $queryString; ?>" + lang;
    }
    function showHideJobCategoryId() {
        $('#job_category_id_div').hide();
        var is_default = $("input[name='is_default']:checked").val();
        if (is_default == 0) {
            $('#job_category_id_div').show();
        }
    }
    showHideJobCategoryId();
</script>
@endpush