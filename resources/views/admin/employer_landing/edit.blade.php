@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper"> 
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content"> 
        <!-- BEGIN PAGE HEADER--> 
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Employer Landing Page Editor</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <!--<h3 class="page-title">Edit Employer Landing Page</h3>-->
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <br />
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"> 
                            <i class="icon-settings font-red-sunglo"></i> 
                            <span class="caption-subject bold uppercase">Employer Landing Page Content</span> 
                        </div>
                    </div>
                    <div class="portlet-body form">          
                        <ul class="nav nav-tabs">              
                            <li class="nav-item active"> 
                                <a href="#Details" data-bs-toggle="tab" aria-expanded="false" class="nav-link active"> Content Editor </a> 
                            </li>
                        </ul>
                        {!! Form::model($cmsContent, array('method' => 'post', 'route' => 'admin.employer-landing.update', 'class' => 'form')) !!}
                        {!! Form::hidden('page_id', $cmsContent->page_id) !!}
                        {!! Form::hidden('lang', $lang) !!}
                        <div class="tab-content">              
                            <div class="tab-pane fade active in show" id="Details">
                                {!! APFrmErrHelp::showErrorsNotice($errors) !!}
                                <div class="form-body">
                                    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'page_title') !!}">
                                        {!! Form::label('page_title', 'Page Title', ['class' => 'bold']) !!}                    
                                        {!! Form::text('page_title', null, array('class'=>'form-control', 'id'=>'page_title', 'placeholder'=>'Page Title', 'dir'=>$direction)) !!}
                                        {!! APFrmErrHelp::showErrors($errors, 'page_title') !!}                                       
                                    </div>    
                                    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'page_content') !!}">
                                        {!! Form::label('page_content', 'Page Content', ['class' => 'bold']) !!}
                                        <small class="text-muted d-block mb-2">
                                            Use the editor below to customize the employer landing page content. 
                                            HTML content will be automatically sanitized for security.
                                        </small>
                                        {!! Form::textarea('page_content', null, array('class'=>'form-control', 'id'=>'page_content', 'placeholder'=>'Page Content')) !!}
                                        {!! APFrmErrHelp::showErrors($errors, 'page_content') !!}                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            {!! Form::button('Update Landing Page <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!}
                            <a href="{{ route('employer.landing') }}" class="btn btn-secondary" target="_blank">
                                <i class="fa fa-eye" aria-hidden="true"></i> Preview Landing Page
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY --> 
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/global/plugins/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
<script src="{{ asset('admin_assets/global/plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
    selector: '#page_content',
    height: 500,
    forced_root_block: '',
    language: '{{ $lang }}',
    directionality: '{{ $direction }}',
    entity_encoding : "raw",
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
    relative_urls: true,
    images_upload_url: "{{ route('tinymce.image_upload') }}",
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', "{{ route('tinymce.image_upload') }}");
        xhr.onload = function () {
            var json;
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            success(json.location);
        };
        formData = new FormData();
        formData.append('image', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }
});
</script>
@endpush
