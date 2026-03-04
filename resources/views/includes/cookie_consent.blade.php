@php
// Check if GDPR is enabled - handle different data types
$gdprEnabled = false;
if (isset($siteSetting)) {
    $enabled = $siteSetting->gdpr_enabled;
    $gdprEnabled = ($enabled == 1 || $enabled === true || $enabled === '1' || $enabled == 'true');
}
@endphp

@if($gdprEnabled)
<div id="cookieConsentBanner" class="cookie-consent-banner" style="display: none;">
    <div class="cookie-consent-content">
        <div class="cookie-text">
            <i class="fas fa-cookie-bite"></i>
            <div class="cookie-text-content">
                <span>{{ $siteSetting->gdpr_cookie_text ?? __('We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.') }}</span>
                @if($siteSetting->gdpr_privacy_policy_url)
                <a href="{{ $siteSetting->gdpr_privacy_policy_url }}" target="_blank" class="learn-more-link">{{__('Learn More')}}</a>
                @endif
            </div>
        </div>
        <div class="cookie-buttons">
            <button class="btn-cookie-settings" onclick="showCookieSettings()">
                {{__('Customize')}}
            </button>
            <button class="btn-cookie-accept" onclick="acceptAllCookies()">
                {{ $siteSetting->gdpr_cookie_button_text ?? __('Accept') }}
            </button>
            <button class="btn-cookie-decline" onclick="declineCookies()">
                {{__('Decline')}}
            </button>
        </div>
    </div>
</div>

<!-- Cookie Settings Modal -->
<div id="cookieSettingsModal" class="cookie-settings-modal" style="display: none;">
    <div class="cookie-settings-content">
        <div class="cookie-settings-header">
            <h3><i class="fas fa-cog"></i> {{__('Cookie Preferences')}}</h3>
            <button class="close-modal" onclick="closeCookieSettings()">&times;</button>
        </div>
        <div class="cookie-settings-body">
            <p class="cookie-settings-intro">{{__('Manage your cookie preferences. You can enable or disable different types of cookies below.')}}</p>
            
            <div id="cookieCategoriesList">
                @php
                    $cookieDetails = json_decode($siteSetting->gdpr_cookie_details ?? '{}', true);
                    if (empty($cookieDetails)) {
                        $cookieDetails = [
                            'essential' => [
                                'enabled' => true,
                                'required' => true,
                                'title' => 'Essential Cookies',
                                'description' => 'These cookies are necessary for the website to function properly. They cannot be disabled.'
                            ],
                            'analytics' => [
                                'enabled' => false,
                                'required' => false,
                                'title' => 'Analytics Cookies',
                                'description' => 'These cookies help us analyze how visitors use our website.'
                            ],
                            'marketing' => [
                                'enabled' => false,
                                'required' => false,
                                'title' => 'Marketing Cookies',
                                'description' => 'These cookies are used to deliver relevant advertisements.'
                            ]
                        ];
                    }
                @endphp
                @foreach($cookieDetails as $key => $category)
                <div class="cookie-category-item">
                    <div class="cookie-category-header">
                        <div class="cookie-category-info">
                            <h4>{{ $category['title'] ?? ucfirst($key) }}</h4>
                            <p>{{ $category['description'] ?? '' }}</p>
                        </div>
                        <div class="cookie-category-toggle">
                            @if(isset($category['required']) && $category['required'])
                                <span class="cookie-required-badge">{{__('Required')}}</span>
                                <input type="checkbox" class="cookie-toggle" id="cookie_{{ $key }}" checked disabled>
                            @else
                                <label class="cookie-switch">
                                    <input type="checkbox" class="cookie-toggle" id="cookie_{{ $key }}" 
                                           data-category="{{ $key }}"
                                           {{ (isset($category['enabled']) && $category['enabled']) ? 'checked' : '' }}>
                                    <span class="cookie-slider"></span>
                                </label>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="cookie-settings-footer">
            <button class="btn-cookie-save" onclick="saveCookiePreferences()">
                {{__('Save Preferences')}}
            </button>
            <button class="btn-cookie-accept-all" onclick="acceptAllCookies()">
                {{__('Accept All')}}
            </button>
        </div>
    </div>
</div>

<style>
.cookie-consent-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #fff;
    padding: 20px;
    z-index: 99999;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.cookie-consent-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 30px;
}

.cookie-text {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 14px;
    line-height: 1.5;
}

.cookie-text i {
    font-size: 28px;
    color: #f39c12;
    margin-top: 2px;
    flex-shrink: 0;
}

.cookie-text-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cookie-text-content span {
    display: block;
}

.learn-more-link {
    color: #3498db;
    text-decoration: underline;
    white-space: nowrap;
    font-size: 14px;
    transition: color 0.2s;
}

.learn-more-link:hover {
    color: #2980b9;
    text-decoration: underline;
}

.cookie-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-cookie-accept, .btn-cookie-decline, .btn-cookie-settings {
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-cookie-accept {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}

.btn-cookie-accept:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
}

.btn-cookie-settings {
    background: transparent;
    color: #fff;
    border: 1px solid #fff;
}

.btn-cookie-settings:hover {
    background: rgba(255, 255, 255, 0.1);
}

.btn-cookie-decline {
    background: transparent;
    color: #aaa;
    border: 1px solid #555;
}

.btn-cookie-decline:hover {
    border-color: #888;
    color: #fff;
}

/* Cookie Settings Modal */
.cookie-settings-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.cookie-settings-content {
    background: #fff;
    border-radius: 10px;
    max-width: 700px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.cookie-settings-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: 10px 10px 0 0;
}

.cookie-settings-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.close-modal {
    background: none;
    border: none;
    color: #fff;
    font-size: 28px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    line-height: 1;
    border-radius: 50%;
    transition: background 0.2s;
}

.close-modal:hover {
    background: rgba(255, 255, 255, 0.2);
}

.cookie-settings-body {
    padding: 20px;
}

.cookie-settings-intro {
    margin-bottom: 20px;
    color: #666;
    line-height: 1.6;
}

.cookie-category-item {
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f9f9f9;
}

.cookie-category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
}

.cookie-category-info {
    flex: 1;
}

.cookie-category-info h4 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 16px;
}

.cookie-category-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

.cookie-category-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cookie-required-badge {
    background: #e74c3c;
    color: #fff;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.cookie-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.cookie-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.cookie-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.3s;
    border-radius: 24px;
}

.cookie-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.cookie-switch input:checked + .cookie-slider {
    background-color: #11998e;
}

.cookie-switch input:checked + .cookie-slider:before {
    transform: translateX(26px);
}

.cookie-settings-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-cookie-save, .btn-cookie-accept-all {
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-cookie-save {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.btn-cookie-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-cookie-accept-all {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}

.btn-cookie-accept-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
}

@media (max-width: 768px) {
    .cookie-consent-content {
        flex-direction: column;
        text-align: center;
    }
    .cookie-text {
        flex-direction: column;
    }
    .cookie-buttons {
        width: 100%;
        justify-content: center;
    }
    .cookie-settings-content {
        margin: 10px;
        max-height: calc(100vh - 20px);
    }
    .cookie-category-header {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cookieBanner = document.getElementById('cookieConsentBanner');
    if (!cookieBanner) {
        console.log('Cookie banner element not found - GDPR may not be enabled');
        return; // Banner doesn't exist, GDPR not enabled
    }
    
    var consent = getCookie('cookie_consent');
    var preferences = getCookie('cookie_preferences');
    
    console.log('Cookie consent check:', consent);
    
    // Show banner if no consent cookie exists
    if (!consent || consent === '' || consent === null) {
        console.log('Showing cookie banner');
        cookieBanner.style.display = 'block';
    } else {
        console.log('Cookie consent already exists, not showing banner');
    }
    
    // Load saved preferences
    if (preferences) {
        try {
            var savedPrefs = JSON.parse(preferences);
            Object.keys(savedPrefs).forEach(function(category) {
                var checkbox = document.getElementById('cookie_' + category);
                if (checkbox && !checkbox.disabled) {
                    checkbox.checked = savedPrefs[category];
                }
            });
        } catch(e) {
            console.error('Error loading cookie preferences:', e);
        }
    }
});

function showCookieSettings() {
    document.getElementById('cookieSettingsModal').style.display = 'flex';
}

function closeCookieSettings() {
    document.getElementById('cookieSettingsModal').style.display = 'none';
}

function acceptAllCookies() {
    // Enable all non-required cookies
    var checkboxes = document.querySelectorAll('.cookie-toggle:not(:disabled)');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = true;
    });
    
    // Save consent immediately when accepting all
    setCookie('cookie_consent', 'accepted', 365);
    @if(Auth::check())
    saveConsentToDatabase();
    @endif
    
    saveCookiePreferences();
}

function declineCookies() {
    // Only keep required cookies
    var checkboxes = document.querySelectorAll('.cookie-toggle:not(:disabled)');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
    
    var preferences = {};
    checkboxes.forEach(function(checkbox) {
        preferences[checkbox.dataset.category] = false;
    });
    
    // Get required cookies
    var requiredCheckboxes = document.querySelectorAll('.cookie-toggle:disabled');
    requiredCheckboxes.forEach(function(checkbox) {
        var category = checkbox.id.replace('cookie_', '');
        preferences[category] = true;
    });
    
    setCookie('cookie_consent', 'declined', 365);
    setCookie('cookie_preferences', JSON.stringify(preferences), 365);
    
    document.getElementById('cookieConsentBanner').style.display = 'none';
    document.getElementById('cookieSettingsModal').style.display = 'none';
    
    applyCookiePreferences(preferences);
}

function saveCookiePreferences() {
    var preferences = {};
    var allCheckboxes = document.querySelectorAll('.cookie-toggle');
    
    allCheckboxes.forEach(function(checkbox) {
        var category = checkbox.id.replace('cookie_', '');
        preferences[category] = checkbox.checked || checkbox.disabled;
    });
    
    setCookie('cookie_consent', 'accepted', 365);
    setCookie('cookie_preferences', JSON.stringify(preferences), 365);
    
    // Save consent to database if user is logged in
    @if(Auth::check())
    saveConsentToDatabase();
    @endif
    
    document.getElementById('cookieConsentBanner').style.display = 'none';
    document.getElementById('cookieSettingsModal').style.display = 'none';
    
    applyCookiePreferences(preferences);
}

@if(Auth::check())
function saveConsentToDatabase() {
    $.ajax({
        url: "{{ route('save.cookie.consent') }}",
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            consent: 'accepted'
        },
        success: function(response) {
            console.log('Consent saved to database');
        },
        error: function(xhr) {
            console.error('Error saving consent:', xhr);
        }
    });
}
@endif

function applyCookiePreferences(preferences) {
    // Trigger custom event for other scripts to listen
    var event = new CustomEvent('cookiePreferencesUpdated', {
        detail: preferences
    });
    document.dispatchEvent(event);
    
    // You can add logic here to enable/disable analytics, marketing scripts, etc.
    // For example:
    /*
    if (preferences.analytics) {
        // Enable Google Analytics
    } else {
        // Disable Google Analytics
    }
    
    if (preferences.marketing) {
        // Enable marketing scripts
    } else {
        // Disable marketing scripts
    }
    */
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('cookieSettingsModal');
    if (event.target == modal) {
        closeCookieSettings();
    }
}
</script>
@endif
