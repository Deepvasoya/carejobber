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
    <div class="container-fluid" style="padding-left: 5px; padding-right: 5px;">@include('flash::message')
        <div class="row" style="gap: 20px; margin: 0;"> @include('includes.user_dashboard_menu')
            <div class="col-lg-7" style="flex: 1; min-width: 0;">
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
                                @if($appliedjob->job)
                                    @include('includes.job_seeker_dashboard_job_card', ['job' => $appliedjob->job, 'appliedJob' => $appliedjob])
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
                                <h3>{{__('Recommended Jobs')}} <a href="{{ route('recommended.jobs') }}">{{__('View All')}} <i class="fas fa-arrow-right"></i></a></h3>
                                <ul class="featuredlist row">
                                @if(!empty($matchingJobs) && count($matchingJobs) > 0)
                                    @foreach($matchingJobs as $match)
                                        @include('includes.job_seeker_dashboard_job_card', ['job' => $match])
                                    @endforeach
                                @else
                                    <div class="alert alert-info">{{__('No matching jobs found')}}</div>
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
                                    <li class="col-lg-6 col-md-6">
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

            <!-- Third Column - Right Sidebar -->
            <div class="col-lg-2" style="flex: 0 0 auto; min-width: 280px;">
                
                <!-- Top Employers Box -->
                <div class="sidebar-box top-employers-box" style="background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                    <h4 style="margin: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                        <i class="fas fa-building" style="color: #667eea; margin-right: 8px;"></i>{{__('Top Employers')}}
                    </h4>
                    @php
                        $topEmployers = \App\Company::where('is_active', 1)
                            ->where('is_featured', 1)
                            ->withCount(['jobs' => function($query) {
                                $query->where('is_active', 1);
                            }])
                            ->orderBy('jobs_count', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($topEmployers->count() > 0)
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach($topEmployers as $employer)
                                <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                                    <a href="{{route('company.detail', $employer->slug)}}" style="display: flex; align-items: center; text-decoration: none; color: #333;">
                                        <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; margin-right: 12px; flex-shrink: 0; border: 1px solid #e0e0e0;">
                                            {{$employer->printCompanyImage(50, 50)}}
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{$employer->name}}</h5>
                                            <p style="margin: 0; font-size: 12px; color: #666;">
                                                <i class="fas fa-briefcase" style="font-size: 10px;"></i> {{$employer->jobs_count}} {{__('Open Jobs')}}
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{route('company.listing')}}" style="display: block; text-align: center; margin-top: 15px; color: #667eea; font-weight: 600; font-size: 14px; text-decoration: none;">
                            {{__('View All Employers')}} <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        <p style="text-align: center; color: #999; font-size: 14px; margin: 20px 0;">{{__('No employers found')}}</p>
                    @endif
                </div>

                <!-- Mobile App CTA -->
                <div class="sidebar-box mobile-app-cta" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); text-align: center; color: #fff;">
                    <div style="font-size: 48px; margin-bottom: 15px;">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600; color: #fff;">{{__('Download Medojob App')}}</h4>
                    <p style="margin: 0 0 20px 0; font-size: 14px; color: rgba(255,255,255,0.9);">{{__('Find jobs on the go! Download our mobile app now.')}}</p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                            <i class="fab fa-apple"></i> {{__('App Store')}}
                        </a>
                        <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                            <i class="fab fa-google-play"></i> {{__('Google Play')}}
                        </a>
                    </div>
                </div>

                <!-- Social Media CTA -->
                <div class="sidebar-box social-cta" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
                    <h4 style="margin: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 600;">{{__('Follow Medojob')}}</h4>
                    <p style="margin: 0 0 20px 0; font-size: 14px; color: #666;">{{__('Stay connected with us on social media')}}</p>
                    <div style="display: flex; justify-content: center; gap: 15px;">
                        <a href="https://facebook.com/medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #1877f2; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://linkedin.com/company/medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #0077b5; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://youtube.com/@medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #ff0000; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('styles')
<style>
    .sidebar-box a[href*="facebook"]:hover { transform: scale(1.1); }
    .sidebar-box a[href*="linkedin"]:hover { transform: scale(1.1); }
    .sidebar-box a[href*="youtube"]:hover { transform: scale(1.1); }
    
    @media (max-width: 991px) {
        .listpgWraper .row { flex-direction: column; }
        .listpgWraper .col-lg-3,
        .listpgWraper .col-lg-6 { flex: 1 1 100%; max-width: 100%; }
    }
</style>
@endpush
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
