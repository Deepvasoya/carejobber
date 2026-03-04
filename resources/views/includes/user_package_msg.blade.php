@php
    $user = Auth::user();
    $isFeaturedPackage = ($user->package_id == 9);
    $siteSetting = App\SiteSetting::first();
    
    // Determine if package is expired
    if ($isFeaturedPackage) {
        $isExpired = $user->featured_package_end_at ? \Carbon\Carbon::parse($user->featured_package_end_at)->isPast() : false;
    } else {
        $isExpired = $user->package_end_date ? \Carbon\Carbon::parse($user->package_end_date)->isPast() : false;
    }
@endphp

@if($isExpired)
{{-- Expired Package Warning --}}
<div class="user-package-details expired">
    <div class="package-header expired-header pt-3 ps-3">
        <h3><i class="fas fa-exclamation-triangle"></i> {{__('Package Expired')}}</h3>
    </div>
    <div class="expired-message">
        <p>{{__('Your package has expired. Please renew or purchase a new package to continue enjoying premium features.')}}</p>
        <a href="{{ route('user.package') }}" class="btn btn-danger">
            <i class="fa fa-shopping-cart"></i> {{__('Renew Package')}}
        </a>
    </div>
</div>
@else
{{-- Active Package Display --}}
<div class="user-package-details">
    <div class="package-header">
        <h3><i class="fas fa-box-open"></i> {{__('Active Package Details')}}</h3>
    </div>
    
    <div class="package-info-grid">
        <!-- Package Name Card -->
        <div class="package-info-card user-package">
            <div class="package-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Name')}}</span>
                <h4 class="package-value">{{$package->package_title}}</h4>
            </div>
        </div>

        <!-- Price Card -->
        <div class="package-info-card user-package">
            <div class="package-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Price')}}</span>
                <h4 class="package-value">{{ $siteSetting->default_currency_code }} {{$package->package_price}}</h4>
            </div>
        </div>

        @if(!$isFeaturedPackage)
        <!-- Quota Card (Only for non-featured packages) -->
        <div class="package-info-card quota-card user-package">
            <div class="package-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Applications Quota')}}</span>
                <h4 class="package-value">
                    <span class="quota-available">{{$user->availed_jobs_quota ?? 0}}</span>
                    <span class="quota-separator">/</span>
                    <span class="quota-total">{{$user->jobs_quota ?? 0}}</span>
                </h4>
            </div>
        </div>
        @else
        <!-- Featured Badge for Featured Package -->
        <div class="package-info-card user-package featured-badge-card">
            <div class="package-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Type')}}</span>
                <h4 class="package-value">
                    <span class="badge bg-success">{{__('Featured Profile')}}</span>
                </h4>
            </div>
        </div>
        @endif

        <!-- Start Date Card -->
        <div class="package-info-card user-package">
            <div class="package-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Started On')}}</span>
                <h4 class="package-value">
                    @if($isFeaturedPackage)
                        {{$user->featured_package_start_at ? \Carbon\Carbon::parse($user->featured_package_start_at)->format('d M, Y') : 'N/A'}}
                    @else
                        {{$user->package_start_date ? \Carbon\Carbon::parse($user->package_start_date)->format('d M, Y') : 'N/A'}}
                    @endif
                </h4>
            </div>
        </div>

        <!-- End Date Card -->
        <div class="package-info-card user-package">
            <div class="package-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Expires On')}}</span>
                <h4 class="package-value">
                    @if($isFeaturedPackage)
                        {{$user->featured_package_end_at ? \Carbon\Carbon::parse($user->featured_package_end_at)->format('d M, Y') : 'N/A'}}
                    @else
                        {{$user->package_end_date ? \Carbon\Carbon::parse($user->package_end_date)->format('d M, Y') : 'N/A'}}
                    @endif
                </h4>
            </div>
        </div>
    </div>
</div>
@endif
