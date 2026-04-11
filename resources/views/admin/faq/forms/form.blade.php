<?php
$lang = config('default_lang');
if (isset($faq))
    $lang = $faq->lang;
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
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'faq_category_id') !!}">
        {!! Form::label('faq_category_id', 'Category (Optional)', ['class' => 'bold']) !!}                    
        {!! Form::select('faq_category_id', ['' => 'Select Category']+$categories, null, array('class'=>'form-control', 'id'=>'faq_category_id')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'faq_category_id') !!}
        <small class="form-text text-muted">Select a category to organize this FAQ. <a href="{{ route('list.faq.categories') }}" target="_blank">Manage Categories</a></small>
    </div>      
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'faq_question') !!}">
        {!! Form::label('faq_question', 'Question', ['class' => 'bold']) !!}                    
        {!! Form::textarea('faq_question', null, array('class'=>'form-control', 'id'=>'faq_question', 'placeholder'=>'Question')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'faq_question') !!}                                       
    </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'faq_answer') !!}">
        {!! Form::label('faq_answer', 'Answer', ['class' => 'bold']) !!}                    
        {!! Form::textarea('faq_answer', null, array('class'=>'form-control', 'id'=>'faq_answer', 'placeholder'=>'Answer')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'faq_answer') !!}                                       
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
    
    // Load categories when language changes
    $('#lang').on('change', function() {
        var lang = $(this).val();
        if (lang) {
            $.ajax({
                url: "{{ route('get.faq.categories.by.lang') }}",
                type: 'GET',
                data: { lang: lang },
                success: function(data) {
                    var categorySelect = $('#faq_category_id');
                    categorySelect.empty();
                    categorySelect.append('<option value="">Select Category</option>');
                    $.each(data, function(id, name) {
                        categorySelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            });
        }
    });
</script>
@include('admin.shared.tinyMCE')
@endpush