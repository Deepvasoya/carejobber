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
                <li> <a href="{{ route('list.faq.sections') }}">FAQ Sections</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Edit FAQ Section</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <!--<h3 class="page-title">Edit FAQ Section <small>FAQ Sections</small> </h3>-->
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <br />
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"> <i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">FAQ Section Form</span> </div>
                    </div>
                    <div class="portlet-body form">          
                        <ul class="nav nav-tabs">              
                            <li class="active"> <a href="#Details" data-bs-toggle="tab" aria-expanded="false"> FAQ Section </a> </li>
                        </ul>
                        {!! Form::model($faqSection, array('method' => 'put', 'route' => array('update.faq.section', $faqSection->id), 'class' => 'form')) !!}
                        <div class="tab-content">              
                            <div class="tab-pane fade active in show" id="Details"> @include('admin.faq_section.forms.form') </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY --> 
</div>
@endsection
