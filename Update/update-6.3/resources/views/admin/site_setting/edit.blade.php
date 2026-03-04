<?php
$lang = config('default_lang');
$direction = MiscHelper::getLangDirection($lang);
?>
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
                <li> <span>Edit Site Setting</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <!--<h3 class="page-title">Edit User <small>Users</small> </h3>-->
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <br />
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"> <i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">Site Setting Form</span> </div>
                    </div>
                    <div class="portlet-body form">          
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" href="#site" data-bs-toggle="tab" role="tab" aria-selected="true">Site</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#email" data-bs-toggle="tab" role="tab" aria-selected="false">Email</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#social" data-bs-toggle="tab" role="tab" aria-selected="false">Social Networks</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#ads" data-bs-toggle="tab" role="tab" aria-selected="false">Manage Ads</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#captcha" data-bs-toggle="tab" role="tab" aria-selected="false">Captcha</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#socialMediaLogin" data-bs-toggle="tab" role="tab" aria-selected="false">Social Media Login</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#paymentGateways" data-bs-toggle="tab" role="tab" aria-selected="false">Payment Gateways</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#homePageSlider" data-bs-toggle="tab" role="tab" aria-selected="false">Home Page Slider</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#googleAnalytics" data-bs-toggle="tab" role="tab" aria-selected="false">Google Analytics</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="#gdprCookie" data-bs-toggle="tab" role="tab" aria-selected="false">GDPR Cookie Policy</a>
                            </li>
                        </ul>
                        {!! Form::model($siteSetting, array('method' => 'put', 'route' => array('update.site.setting'), 'class' => 'form', 'files'=>true)) !!}
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="site" role="tabpanel">@include('admin.site_setting.forms.form')</div>
                            <div class="tab-pane fade" id="email" role="tabpanel">@include('admin.site_setting.forms.siteEmailSetting_form')</div>
                            <div class="tab-pane fade" id="social" role="tabpanel">@include('admin.site_setting.forms.siteSocialSetting_form')</div>
                            <div class="tab-pane fade" id="ads" role="tabpanel">@include('admin.site_setting.forms.siteAds_form')</div>
                            <div class="tab-pane fade" id="captcha" role="tabpanel">@include('admin.site_setting.forms.captchaSetting_form')</div>
                            <div class="tab-pane fade" id="socialMediaLogin" role="tabpanel">@include('admin.site_setting.forms.socialMediaLoginSetting_form')</div>
                            <div class="tab-pane fade" id="paymentGateways" role="tabpanel">@include('admin.site_setting.forms.paymentGatewaysSetting_form')</div>
                            <div class="tab-pane fade" id="homePageSlider" role="tabpanel">@include('admin.site_setting.forms.homePageSliderSetting_form')</div>
                            <div class="tab-pane fade" id="googleAnalytics" role="tabpanel">@include('admin.site_setting.forms.googleAnalytics_form')</div>
                            <div class="tab-pane fade" id="gdprCookie" role="tabpanel">@include('admin.site_setting.forms.gdprCookieSetting_form')</div>
                        </div>
                        <div class="form-actions mt-3">
                            {!! Form::button('Update <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY --> 
    </div>
    @endsection
    @push('scripts')
    @include('admin.shared.tinyMCE')
    @endpush