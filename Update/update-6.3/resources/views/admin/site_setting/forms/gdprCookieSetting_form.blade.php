{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <fieldset>
        <legend>GDPR Cookie Policy Settings:</legend>
        
        <div class="form-group mb-3 mb-3">
            <label class="control-label">Enable GDPR Cookie Consent</label>
            <div class="radio-list">
                <label class="radio-inline">
                    {!! Form::radio('gdpr_enabled', 1, (isset($siteSetting) && $siteSetting->gdpr_enabled == 1) ? true : false, ['id' => 'gdpr_enabled_yes']) !!}
                    Yes
                </label>
                <label class="radio-inline">
                    {!! Form::radio('gdpr_enabled', 0, (isset($siteSetting) && (!$siteSetting || $siteSetting->gdpr_enabled == 0)) ? true : false, ['id' => 'gdpr_enabled_no']) !!}
                    No
                </label>
            </div>
            <span class="help-block">Enable or disable the GDPR cookie consent banner on your website.</span>
        </div>

        <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'gdpr_cookie_text') !!}">
            <label class="control-label">Cookie Consent Message</label>
            {!! Form::textarea('gdpr_cookie_text', isset($siteSetting) ? $siteSetting->gdpr_cookie_text : null, array('class'=>'form-control', 'id'=>'gdpr_cookie_text', 'placeholder'=>'We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.', 'rows' => 3)) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gdpr_cookie_text') !!}
            <span class="help-block">This message will be displayed in the cookie consent banner.</span>
        </div>

        <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'gdpr_cookie_button_text') !!}">
            <label class="control-label">Accept Button Text</label>
            {!! Form::text('gdpr_cookie_button_text', isset($siteSetting) ? $siteSetting->gdpr_cookie_button_text : null, array('class'=>'form-control', 'id'=>'gdpr_cookie_button_text', 'placeholder'=>'Accept')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gdpr_cookie_button_text') !!}
            <span class="help-block">Text for the accept button (default: Accept).</span>
        </div>

        <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'gdpr_privacy_policy_url') !!}">
            <label class="control-label">Privacy Policy URL</label>
            {!! Form::text('gdpr_privacy_policy_url', isset($siteSetting) ? $siteSetting->gdpr_privacy_policy_url : null, array('class'=>'form-control', 'id'=>'gdpr_privacy_policy_url', 'placeholder'=>'https://yoursite.com/privacy-policy')) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gdpr_privacy_policy_url') !!}
            <span class="help-block">Link to your privacy policy page. Leave empty if you don't have one.</span>
        </div>

        <div class="form-group mb-3 mb-3 {!! APFrmErrHelp::hasError($errors, 'gdpr_cookie_details') !!}">
            <label class="control-label">Cookie Categories (JSON Format)</label>
            {!! Form::textarea('gdpr_cookie_details', isset($siteSetting) ? $siteSetting->gdpr_cookie_details : null, array('class'=>'form-control', 'id'=>'gdpr_cookie_details', 'placeholder'=>'{"essential":{"enabled":true,"required":true,"title":"Essential Cookies","description":"These cookies are necessary for the website to function properly."},"analytics":{"enabled":false,"required":false,"title":"Analytics Cookies","description":"These cookies help us analyze how visitors use our website."},"marketing":{"enabled":false,"required":false,"title":"Marketing Cookies","description":"These cookies are used to deliver relevant advertisements."}}', 'rows' => 8)) !!}
            {!! APFrmErrHelp::showErrors($errors, 'gdpr_cookie_details') !!}
            <span class="help-block">
                Define cookie categories in JSON format. Each category should have: enabled (boolean), required (boolean), title (string), and description (string).
                <br><strong>Example:</strong>
                <pre style="margin-top: 5px;">{
  "essential": {
    "enabled": true,
    "required": true,
    "title": "Essential Cookies",
    "description": "These cookies are necessary for the website to function properly."
  },
  "analytics": {
    "enabled": false,
    "required": false,
    "title": "Analytics Cookies",
    "description": "These cookies help us analyze how visitors use our website."
  },
  "marketing": {
    "enabled": false,
    "required": false,
    "title": "Marketing Cookies",
    "description": "These cookies are used to deliver relevant advertisements."
  }
}</pre>
            </span>
        </div>
    </fieldset>
</div>
