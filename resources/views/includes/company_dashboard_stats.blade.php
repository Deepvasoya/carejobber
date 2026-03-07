@php
    $company = Auth::guard('company')->user();
    $hasActivePackage = $company->package_id && $company->package_end_date && \Carbon\Carbon::parse($company->package_end_date)->isFuture();
    $remainingCredits = $hasActivePackage ? ($company->jobs_quota - $company->availed_jobs_quota) : 0;
    $packageName = $hasActivePackage ? ($company->getPackage('package_title') ?? __('Active Package')) : __('No Package');
@endphp

<!-- Package Status Card -->
@if($hasActivePackage)
<div class="alert alert-success mb-4" style="border-radius: 12px; border-left: 4px solid #28a745;">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="mb-2"><i class="fas fa-check-circle"></i> {{__('Active Package')}}: <strong>{{ $packageName }}</strong></h5>
            <p class="mb-1"><i class="fas fa-calendar"></i> {{__('Valid until')}}: <strong>{{ \Carbon\Carbon::parse($company->package_end_date)->format('M d, Y') }}</strong></p>
            <p class="mb-0"><i class="fas fa-briefcase"></i> {{__('Remaining Credits')}}: <strong>{{ $remainingCredits }}</strong> / {{ $company->jobs_quota }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('recruiter.posting.packages') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-arrow-up"></i> {{__('Upgrade Package')}}
            </a>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning mb-4" style="border-radius: 12px; border-left: 4px solid #ffc107;">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="mb-2"><i class="fas fa-exclamation-triangle"></i> {{__('No Active Package')}}</h5>
            <p class="mb-0">{{__('Purchase a package to start posting jobs and access premium features.')}}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('recruiter.posting.packages') }}" class="btn btn-warning">
                <i class="fas fa-shopping-cart"></i> {{__('Buy Package Now')}}
            </a>
        </div>
    </div>
</div>
@endif

<ul class="row profilestat">
    <li class="col-md-4 col-6">
        <a href="{{route('posted.jobs')}}" class="inbox"> <i class="fas fa-clock" aria-hidden="true"></i>
            <h6>{{Auth::guard('company')->user()->countOpenJobs()}}
            <strong>{{__('Open Jobs')}}</strong>
        </h6>
</a>
    </li>
    <li class="col-md-4 col-6">
        <a href="{{route('company.followers')}}" class="inbox"> <i class="fas fa-user" aria-hidden="true"></i>
            <h6>{{Auth::guard('company')->user()->countFollowers()}}
            <strong>{{__('Followers')}}</strong> 
        </h6>
</a>
    </li>
     <li class="col-md-4 col-6">
        <a href="{{route('company.messages')}}" class="inbox"> <i class="fas fa-envelope" aria-hidden="true"></i>
            <h6>{{Auth::guard('company')->user()->countCompanyMessages()}}
            <strong>{{__('Messages')}}</strong>
        </h6>
</a>
    </li>
</ul>