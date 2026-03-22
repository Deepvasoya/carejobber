{!! APFrmErrHelp::showOnlyErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_for') !!}">
        {!! Form::label('package_for', 'Package for?', ['class' => 'bold']) !!}
        <div class="radio-list">
            <?php
            $package_for_1 = 'checked="checked"';
            $package_for_2 = '';
            $package_for_3 = '';
            $package_for_4 = '';
            
            $selected_package = old('package_for', isset($package) ? $package->package_for : 'job_seeker');
            
            if ($selected_package == 'employer') {
                $package_for_1 = '';
                $package_for_2 = 'checked="checked"';
            } elseif ($selected_package == 'cv_search') {
                $package_for_1 = '';
                $package_for_3 = 'checked="checked"';
            } elseif ($selected_package == 'make_featured') {
                $package_for_1 = '';
                $package_for_4 = 'checked="checked"';
            }
            ?>

            @if(isset($package) && $package->id == 9)
                <label class="radio-inline">
                    <input id="make_featured" name="package_for" type="radio" value="make_featured" {{$package_for_4}}>
                    Candidate Featured Profile
                </label>
            @else
            <label class="radio-inline">
                <input id="job_seeker" name="package_for" type="radio" value="job_seeker" {{$package_for_1}}>
                Job Seeker </label>
            <label class="radio-inline">
                <input id="employer" name="package_for" type="radio" value="employer" {{$package_for_2}}>
                Employer </label>
            <label class="radio-inline">
                <input id="cv_search" name="package_for" type="radio" value="cv_search" {{$package_for_3}}>
                Cv Search </label>
                @endif
            
        </div>
        {!! APFrmErrHelp::showErrors($errors, 'package_for') !!}
    </div>
    
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_title') !!}"> {!! Form::label('package_title', 'Package Title', ['class' => 'bold']) !!}
        {!! Form::text('package_title', null, array('class'=>'form-control', 'id'=>'package_title', 'placeholder'=>'Package Title')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'package_title') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_price') !!}"> {!! Form::label('package_price', 'Package Price(In ' . $siteSetting->default_currency_code . ')', ['class' => 'bold']) !!}
        {!! Form::text('package_price', null, array('class'=>'form-control', 'id'=>'package_price', 'placeholder'=>'Package Price')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'package_price') !!} </div>
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_num_days') !!}"> {!! Form::label('package_num_days', 'Package num days', ['class' => 'bold']) !!}
        {!! Form::text('package_num_days', null, array('class'=>'form-control', 'id'=>'package_num_days', 'placeholder'=>'Package num days')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'package_num_days') !!} </div>
    <div id="package_num_listings_group" class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_num_listings') !!}">
        {!! Form::label('package_num_listings', 'Package num listings*', ['class' => 'bold']) !!}
        {!! Form::text('package_num_listings', null, array('class'=>'form-control', 'id'=>'package_num_listings', 'placeholder'=>'Package num listings')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'package_num_listings') !!}
        *On how many jobs a job seeker can apply<br />
        **How many jobs an employer can post (ignored for employer subscriptions with &quot;unlimited&quot; checked)
    </div>

    @php
        $employerPkgType = old('package_type', isset($package) ? ($package->type ?: \App\Package::TYPE_ONE_TIME_CREDITS) : \App\Package::TYPE_ONE_TIME_CREDITS);
    @endphp
    <div id="employer_recruiter_options" class="form-group mb-3 border rounded p-3" style="display:none;">
        <p class="bold mb-2">Employer — credits &amp; Stripe subscriptions</p>
        {!! Form::hidden('is_active', 0) !!}
        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'package_type') !!}">
            {!! Form::label('package_type', 'Billing type', ['class' => 'bold']) !!}
            {!! Form::select('package_type', [
                \App\Package::TYPE_ONE_TIME_CREDITS => 'Pay-per-post (one-time credits)',
                \App\Package::TYPE_MONTHLY_RECURRING => 'Subscription (recurring — Subscriptions tab)',
            ], $employerPkgType, ['class' => 'form-control', 'id' => 'package_type']) !!}
            {!! APFrmErrHelp::showErrors($errors, 'package_type') !!}
            <small class="text-muted d-block mt-1">Subscriptions use Stripe Checkout in subscription mode. Define the billing period in days below (e.g. 90 ≈ 3 months per invoice).</small>
        </div>
        <div id="subscription_term_fields" style="display:none;">
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'duration_days') !!}">
                {!! Form::label('duration_days', 'Subscription period (days)', ['class' => 'bold']) !!}
                {!! Form::number('duration_days', null, ['class' => 'form-control', 'id' => 'duration_days', 'min' => 1, 'placeholder' => 'e.g. 90']) !!}
                {!! APFrmErrHelp::showErrors($errors, 'duration_days') !!}
            </div>
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'subscription_unlimited_jobs') !!}">
                <label class="bold d-block">Unlimited job postings during each active period</label>
                <label class="d-block">
                    {!! Form::checkbox('subscription_unlimited_jobs', 1, old('subscription_unlimited_jobs', isset($package) ? (bool) $package->subscription_unlimited_jobs : false), ['id' => 'subscription_unlimited_jobs']) !!}
                    Employer can post unlimited jobs while the package period is active
                </label>
                {!! APFrmErrHelp::showErrors($errors, 'subscription_unlimited_jobs') !!}
            </div>
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'stripe_price_id') !!}">
                {!! Form::label('stripe_price_id', 'Stripe Price ID (optional)', ['class' => 'bold']) !!}
                {!! Form::text('stripe_price_id', null, ['class' => 'form-control', 'placeholder' => 'price_...']) !!}
                {!! APFrmErrHelp::showErrors($errors, 'stripe_price_id') !!}
                <small class="text-muted">If set, must be a <em>recurring</em> Price in Stripe; it overrides auto-built pricing.</small>
            </div>
        </div>
        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'country_code') !!}">
            {!! Form::label('country_code', 'Country (ISO, optional)', ['class' => 'bold']) !!}
            {!! Form::text('country_code', null, ['class' => 'form-control', 'maxlength' => 2, 'placeholder' => 'CA or leave empty for all']) !!}
            {!! APFrmErrHelp::showErrors($errors, 'country_code') !!}
        </div>
        <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'rebate_percent') !!}">
            {!! Form::label('rebate_percent', 'Rebate % (credits packages)', ['class' => 'bold']) !!}
            {!! Form::number('rebate_percent', null, ['class' => 'form-control', 'min' => 0, 'max' => 100]) !!}
            {!! APFrmErrHelp::showErrors($errors, 'rebate_percent') !!}
        </div>
        <div class="form-group mb-3">
            <label class="d-block bold">
                {!! Form::checkbox('is_active', 1, old('is_active', isset($package) ? (bool) ($package->is_active ?? true) : true), ['id' => 'is_active']) !!}
                Active (visible on recruiter pricing pages)
            </label>
        </div>
    </div>
    
    <div class="form-actions"> {!! Form::button('Update <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!} </div>
</div>

<script>
    function setEmployerRecruiterFieldsDisabled(disabled) {
        var block = document.getElementById('employer_recruiter_options');
        if (!block) return;
        block.querySelectorAll('input, select, textarea').forEach(function (el) {
            el.disabled = disabled;
        });
    }

    function toggleSubscriptionFields() {
        var sel = document.getElementById('package_type');
        var sub = sel && sel.value === 'monthly_recurring';
        var tf = document.getElementById('subscription_term_fields');
        if (tf) {
            tf.style.display = sub ? 'block' : 'none';
            tf.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.disabled = !sub;
            });
        }
    }

    function toggleEmployerRecruiter() {
        var checked = document.querySelector('input[name="package_for"]:checked');
        var selectedValue = checked ? checked.value : '';
        var block = document.getElementById('employer_recruiter_options');
        var isEmployer = selectedValue === 'employer';
        if (block) {
            block.style.display = isEmployer ? 'block' : 'none';
        }
        setEmployerRecruiterFieldsDisabled(!isEmployer);
        if (isEmployer) {
            toggleSubscriptionFields();
        } else {
            var tf = document.getElementById('subscription_term_fields');
            if (tf) {
                tf.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.disabled = true;
                });
            }
        }
    }

    function togglePackageListings() {
        var selectedValue = document.querySelector('input[name="package_for"]:checked').value;
        var packageNumListingsGroup = document.getElementById('package_num_listings_group');

        if (selectedValue === 'make_featured') {
            packageNumListingsGroup.style.display = 'none';
        } else {
            packageNumListingsGroup.style.display = 'block';
        }
        toggleEmployerRecruiter();
    }

    document.querySelectorAll('input[name="package_for"]').forEach(function (radio) {
        radio.addEventListener('change', togglePackageListings);
    });

    var packageTypeEl = document.getElementById('package_type');
    if (packageTypeEl) {
        packageTypeEl.addEventListener('change', toggleSubscriptionFields);
    }

    document.addEventListener('DOMContentLoaded', function() {
        togglePackageListings();
    });
</script>