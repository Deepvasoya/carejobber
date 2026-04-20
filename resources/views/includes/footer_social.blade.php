@if ((string)$siteSetting->facebook_address !== '')
<a href="{{ $siteSetting->facebook_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-facebook" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->pinterest_address !== '')
<a href="{{ $siteSetting->pinterest_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-pinterest" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->twitter_address !== '')
<a href="{{ $siteSetting->twitter_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-twitter" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->instagram_address !== '')
<a href="{{ $siteSetting->instagram_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-instagram" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->linkedin_address !== '')
<a href="{{ $siteSetting->linkedin_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-linkedin" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->youtube_address !== '')
<a href="{{ $siteSetting->youtube_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-youtube" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->tumblr_address !== '')
<a href="{{ $siteSetting->tumblr_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-tumblr" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif

@if ((string)$siteSetting->flickr_address !== '')
<a href="{{ $siteSetting->flickr_address }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#ffffff;color:#000000;margin:0 6px;text-decoration:none;">
    <i class="fab fa-flickr" aria-hidden="true" style="color:#000000;font-size:12px;line-height:1;"></i>
</a>
@endif