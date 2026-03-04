<div class="company-package-details">
    <div class="package-header">
        <h3><i class="fas fa-box-open"></i> {{__('Purchased Job Package Details')}}</h3>
    </div>
    
    <div class="package-info-grid">
        <!-- Package Name Card -->
        <div class="package-info-card package-name-card">
            <div class="package-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Name')}}</span>
                <h4 class="package-value">{{$package->package_title}}</h4>
            </div>
        </div>

        <!-- Price Card -->
        <div class="package-info-card">
            <div class="package-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Price')}}</span>
                <h4 class="package-value">{{ $siteSetting->default_currency_code }} {{$package->package_price}}</h4>
            </div>
        </div>

        <!-- Quota Card -->
        <div class="package-info-card quota-card">
            <div class="package-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Available Quota')}}</span>
                <h4 class="package-value">
                    <span class="quota-available">{{Auth::guard('company')->user()->availed_jobs_quota}}</span>
                    <span class="quota-separator">/</span>
                    <span class="quota-total">{{Auth::guard('company')->user()->jobs_quota}}</span>
                </h4>
            </div>
        </div>

        <!-- Start Date Card -->
        <div class="package-info-card">
            <div class="package-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Purchased On')}}</span>
                <h4 class="package-value">{{ Auth::guard('company')->user()->package_start_date }}</h4>
            </div>
        </div>

        <!-- End Date Card -->
        <div class="package-info-card">
            <div class="package-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Expires')}}</span>
                <h4 class="package-value">{{ Auth::guard('company')->user()->package_end_date }}</h4>
            </div>
        </div>
    </div>
</div>
