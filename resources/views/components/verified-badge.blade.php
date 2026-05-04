@php
    $trustStatus = $company->getEmployerTrustStatus();
@endphp

@if($trustStatus === 'verified')
    <span class="verified-badge verified-status" title="Verified Employer" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 3px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
        🟢 Verified
    </span>
@elseif($trustStatus === 'reviewed')
    <span class="verified-badge reviewed-status" title="Reviewed Employer" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; padding: 3px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
        🟡 Reviewed
    </span>
@else
    <span class="verified-badge unverified-status" title="Unverified Employer" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 3px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
        🔴 Unverified
    </span>
@endif

