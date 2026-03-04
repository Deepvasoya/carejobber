@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
<div class="pageSearch pt-md-5 pb-md-5">
<form action="{{route('job.list')}}" method="get">
	<!-- Page Title start -->
	<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-7">
					<h3 class="mt-0 text-center">{{__('Welcome to Your Candidate Dashboard')}}</h3>
						<div class="searchform">
						<div class="input-group">
							<input type="text"  name="search" id="jbsearch" value="{{Request::get('search', '')}}" class="form-control" placeholder="{{__('job title, HCA, LPN, RN, etc.')}}" autocomplete="off" />
							<button type="submit" class="btn"><i class="fas fa-search"></i></button>
						</div>
						</div>
					</div>
				</div>
	</div>
	<!-- Page Title end -->
</form>
</div>
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container">@include('flash::message')
        <div class="row"> @include('includes.user_dashboard_menu')
            <div class="col-lg-9">
            @if(count(auth()->user()->getProfileProjectsArray())==0 || count(auth()->user()->getProfileCvsArray())==0 || count(auth()->user()->profileExperience()->get()) == 0 || count(auth()->user()->profileEducation()->get()) == 0 || count(auth()->user()->profileSkills()->get()) == 0)
				<div class="userprofilealert"><h5><i class="fas fa-exclamation-triangle"></i> Your Resume is incomplete please update.</h5>
				<div class="editbtbn"><a href="{{ route('build.resume') }}"><i class="fas fa-user-edit"></i> Complete CV </a></div>	</div>
				@endif
            @include('includes.user_dashboard_stats')
            <div class="usercoverphoto">{{auth()->user()->printUserCoverImage()}}                    
                <a href="{{ route('my.profile') }}"><i class="fas fa-edit"></i></a>
            </div>
             <!-- Profile Information -->
			<div class="profileban">
				<div class="abtuser">
					<div class="row">
						<div class="col-lg-2 col-md-3">
							<div class="uavatar">{{auth()->user()->printUserImage()}}</div>						
						</div>
						<div class="col-lg-10 col-md-9">
							<h4>{{auth()->user()->name}}</h4>
							<ul class="userdata">
								<li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{Auth::user()->getLocation()}}</li>
								<li><i class="fas fa-phone" aria-hidden="true"></i> {{auth()->user()->phone}}</li>
								<li><i class="fas fa-envelope" aria-hidden="true"></i> {{auth()->user()->email}}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
            <!-- Applied jobs -->
			<div class="profbox">
                <h3>{{__('My Applied Jobs')}} <a href="{{route('my.job.applications')}}">{{__('View All')}} <i class="fas fa-arrow-right"></i></a></h3>
                <ul class="featuredlist row">	   	
                @if(isset($appliedJobs) && count($appliedJobs) > 0)
                            @foreach($appliedJobs as $appliedjob)
                                @php
                            $job = $appliedjob->job;
                            $company = $job ? $job->company : null;
                        @endphp
                        @if($job && $company)
                            <li class="col-lg-4 col-md-6 @if($job->is_featured == 1) featured @endif">
                                <div class="jobint mt-0 mb-3">
                                    @if($job->is_featured == 1) 
                                        <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('Featured Job')}}"></i></span> 
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fticon"><i class="fas fa-briefcase"></i> {{$job->getJobType('job_type')}}</div>
                                        @php
                                            $statusClass = 'secondary';
                                            if ($appliedjob->status == 'pending') {
                                                $statusClass = 'warning';
                                            } elseif (in_array($appliedjob->status, ['approved', 'hire', 'hired'])) {
                                                $statusClass = 'success';
                                            } elseif ($appliedjob->status == 'rejected') {
                                                $statusClass = 'danger';
                                            }
                                @endphp
                                        <strong class="badge bg-{{ $statusClass }}">
                                            {{ucfirst($appliedjob->status)}}
                                        </strong>
                                    </div>
                                    <h4>
                                        <a href="{{route('job.detail', [$job->slug])}}" title="{{$job->title}}">
                                            {!! \Illuminate\Support\Str::limit($job->title, 20, '...') !!}
                                        </a>
                                    </h4>                                        
                                    @if(!(bool)$job->hide_salary)                    
                                        <div class="salary mb-2">{{__('Salary')}}: 
                                            <strong>{{$job->salary_currency.''.$job->salary_from}} - {{$job->salary_currency.''.$job->salary_to}}/{{$job->getSalaryPeriod('salary_period')}}</strong>
                                        </div>
                                    @endif 
                                    <strong><i class="fas fa-map-marker-alt"></i> {{$job->getCity('city')}}</strong>                                         
                                    <div class="jobcompany">
                                        <div class="ftjobcomp">
                                            <span>{{__('Applied')}}: {{$appliedjob->created_at->format('M d, Y')}}</span>
                                            <a href="{{route('company.detail', $company->slug)}}" title="{{$company->name}}">{{$company->name}}</a>
                                        </div>
                                        <a href="{{route('company.detail', $company->slug)}}" class="company-logo" title="{{$company->name}}">{{$company->printCompanyImage()}}</a>
                                    </div>
                                </div>
                            </li>
                                @endif
                            @endforeach
                @else
                    <div class="alert alert-info">{{__('No applied jobs found')}}</div>
                @endif
                </ul>
			</div>

          


                {{-- Only show package section if packages are active --}}
                @if((bool)config('jobseeker.is_jobseeker_package_active'))
                    @php
                    $package = Auth::user()->getPackage();
                    @endphp
                    
                    {{-- Show package summary if user has one (already purchased) --}}
                    @if(null !== $package)
                        @include('includes.user_package_msg')
                    @else
                        {{-- Show buy package message if user doesn't have a package --}}
                        <div class="no-package-message">
                            <div class="no-package-content">
                                <i class="fa fa-info-circle no-package-icon"></i>
                                <div class="no-package-text">
                                    <h4>{{__('No Active Package')}}</h4>
                                    <p>{{__('Purchase a package to unlock premium features and boost your job search!')}}</p>
                                </div>
                            </div>
                            <a href="{{ route('user.package') }}" class="no-package-btn">
                                <i class="fa fa-shopping-cart"></i> {{__('View Available Packages')}}
                            </a>
                        </div>
                    @endif
                @endif 



                            <div class="profbox">
                                <h3 class="mb-0">{{__('Recommended Jobs')}}</h3>
                                <ul class="featuredlist row">
                                @if(!empty($matchingJobs) && count($matchingJobs) > 0)
    @foreach($matchingJobs as $match)
        <li class="col-lg-4 col-md-6 @if($match->is_featured == 1) featured @endif">
            <div class="jobint">
                @if($match->is_featured == 1) 
                    <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('This Match is Featured')}}"></i></span> 
                @endif
                <div class="d-flex">
                    <div class="fticon"><i class="fas fa-briefcase"></i> {{$match->getJobType('job_type')}}</div>                        
                </div>
                <h4>
                    <a href="{{route('job.detail', [$match->slug])}}" title="{{$match->title}}">
                        {!! \Illuminate\Support\Str::limit($match->title, 20, '...') !!}
                    </a>
                </h4>                                        
                @if(!(bool)$match->hide_salary)                    
                    <div class="salary mb-2">Salary: 
                        <strong>{{$match->salary_currency.''.$match->salary_from}} - {{$match->salary_currency.''.$match->salary_to}}/{{$match->getSalaryPeriod('salary_period')}}</strong>
                    </div>
                @endif 
                <strong><i class="fas fa-map-marker-alt"></i> {{$match->getCity('city')}}</strong>                                         
                <div class="jobcompany">
                    <div class="ftjobcomp">
                        <span>{{$match->created_at->format('M d, Y')}}</span>
                        @if(isset($match->company))
                            <a href="{{route('company.detail', $match->company->slug)}}" title="{{$match->company->name}}">{{$match->company->name}}</a>
                        @endif
                    </div>
                    @if(isset($match->company))
                        <a href="{{route('company.detail', $match->company->slug)}}" class="company-logo" title="{{$match->company->name}}">{{$match->company->printCompanyImage()}}</a>
                    @endif
                </div>
            </div>
        </li>
    @endforeach 
@else
    <div class="alert alert-danger">{{__('No matching jobs found')}}</div>
@endif

                                </ul>
                            </div>

 <!-- My Followings -->

                            <div class="profbox followbox">
								<h3>{{__('My Followings')}} <a href="{{route('my.followings')}}">{{__('View All')}} <i class="fas fa-arrow-right"></i></a></h3>
								<ul class="row compnaieslist">
								@if(isset($followers) && $followers->isNotEmpty())
                                @foreach($followers as $follow)
                                @php
                                    $company = \App\Company::where('slug', $follow->company_slug)
                                        ->where('is_active', 1)
                                        ->first();
                                @endphp
                                @if(isset($company))
                                    <li class="col-lg-4 col-md-6">
                                        <div class="empint">
                                            <a href="{{route('company.detail', $company->slug)}}" title="{{$company->name}}">
                                                <div class="emptbox">
                                                    <div class="comimg">{{$company->printCompanyImage()}}</div>
                                                    <div class="text-info-right">
                                                        <h4>{{$company->name}}</h4>    
                                                        @if($company->getIndustry('industry'))
                                                            <div class="indst">                            
                                                                {{ $company->getIndustry('industry') }}                          
                                                            </div>
                                                        @endif
                                                        <div class="emloc"><i class="fas fa-map-marker-alt"></i> {{$company->location}}</div>
                                                    </div>                                         
                                                    <div class="cm-info-bottom">
                                                        <span><i class="fas fa-briefcase"></i> {{$company->countNumJobs('company_id',$company->id)}} {{__('Open Jobs')}}</span>
                                                    </div>    
                                                </div>
                                            </a>                    
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                                @else
                                <li class="col-lg-12">{{ __('No Followings Found') }}</li>
                                @endif
								</ul>
								
							</div>


			</div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('scripts')
@include('includes.immediate_available_btn')
<script>
$(document).ready(function() {
    function fadeTextEffect(texts) {
        var index = 0;
        function fadeText() {
            $(texts[index])
                .fadeIn(500) // Fade in over 1 second
                .delay(8000) // Display for 8 seconds
                .fadeOut(500, function() { // Fade out over 1 second
                    index = (index + 1) % texts.length; // Move to the next text
                    fadeText(); // Recursively call to continue the loop
                });
        }
        fadeText(); // Start the animation loop
    }
    // Apply the fade effect to both fade-text and fadetext2
    fadeTextEffect($('.fade-text'));
    fadeTextEffect($('.fadetext2'));
});
</script>
@endpush