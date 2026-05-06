@extends('layouts.app')

@section('content') 

<!-- Header start --> 

@include('includes.header') 

<!-- Header end --> 

<!-- Inner Page Title start --> 

@include('includes.inner_page_title', ['page_title'=>__('Welcome to Employer Dashboard')]) 

<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container-fluid" style="padding-left: 5px; padding-right: 5px;">@include('flash::message')

        <div class="row" style="gap: 20px; margin: 0;"> @include('includes.company_dashboard_menu')
        <?php $company = auth()->guard('company')->user(); ?>

        <div class="col-lg-7" style="flex: 1; min-width: 0;"> 
            
            {{-- Verification Status Banner for Unverified Employers --}}
            @if($company->getEmployerTrustStatus() === 'unverified' && !$company->isVerified())
            <div class="alert alert-warning" style="border-left: 4px solid #ffc107;">
                <h5 style="margin-top: 0;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    {{ __('Build trust and attract more healthcare candidates by getting verified.') }}
                </h5>
                <p style="margin-bottom: 15px;">
                    {{ __('Verified employers receive more applications and access to our full resume database.') }}
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('company.verification.upload') }}" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> {{ __('Get Reviewed (Free)') }}
                    </a>
                    <a href="{{ url('/cms/employer-verification-info') }}" class="btn btn-outline-primary">
                        <i class="fas fa-info-circle"></i> {{ __('Learn What Verification Means') }}
                    </a>
                </div>
            </div>
            @endif
            
            <?php if ($company->is_active == 1 && (($company->package_end_date === null) || 
                (\Carbon\Carbon::parse($company->package_end_date)->lt(\Carbon\Carbon::now())) || 
                ($company->jobs_quota <= $company->availed_jobs_quota))) { ?>    

                <div class="userprofilealert">
                    <h5>
                        <i class="fas fa-check"></i> 
                        {{ __('Your account is active now, you can start Posting Jobs.') }}
                    </h5>
                </div>

            <?php } elseif ($company->isVerificationRejected()) { ?>
                <div class="userprofilealert">
                    <h5>
                        <i class="fas fa-times"></i> 
                        {{__('Your company verification was rejected. Please review the reason and upload corrected documents.')}}
                    </h5>
                </div>
            <?php } elseif ($company->hasPendingVerification()) { ?> 
                <div class="userprofilealert">
                    <h5>
                        <i class="fas fa-clock"></i> 
                        {{__('Your account is currently inactive because your business documents are under review.')}}
                    </h5>
                </div>
            <?php } ?> 
      

            
            
            @include('includes.company_dashboard_stats')

            @if(!$company->isVerified())
                <div class="company-verification-reminder">
                    <div class="company-verification-reminder__content">
                        <div>
                            <h4>{{ __('Verify your company registration') }}</h4>
                            @if($company->isVerificationRejected())
                                <p>{{ __('Your previous submission was rejected. Upload corrected documents so admin can review them again.') }}</p>
                                @if($company->verification_rejection_reason)
                                    <p class="company-verification-reminder__reason"><strong>{{ __('Reason:') }}</strong> {{ $company->verification_rejection_reason }}</p>
                                @endif
                            @elseif($company->hasPendingVerification())
                                <p>{{ __('Your business registration has been uploaded and is currently under review. You can still update any document below if needed.') }}</p>
                            @else
                                <p>{{ __('You can use the dashboard now, but job posting and candidate resume access will stay locked until your company documents are approved.') }}</p>
                            @endif
                        </div>
                        <div class="company-verification-reminder__actions">
                            <button type="button" class="btn btn-primary" id="toggle-company-verification">
                                {{ $company->hasBusinessRegistration() ? __('Manage verification documents') : __('Verify now') }}
                            </button>
                            <a href="{{ route('company.verification.upload') }}" class="btn btn-link">
                                {{ __('Open full verification page') }}
                            </a>
                        </div>
                    </div>

                    <div id="company-verification-panel" class="company-verification-panel" style="display: {{ $errors->has('document_upload') || $errors->has('business_registration') || $errors->has('tax_document') || $errors->has('establishment_photo') ? 'block' : 'none' }};">
                        @include('company.verification._upload_cards', ['latestVerificationDocuments' => $latestVerificationDocuments])
                    </div>
                </div>
            @endif


            <!-- Suggested Candidates Section -->
            <div class="suggested-candidates-section mt-4 mb-4" style="background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <div class="section-header" style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                    <h3 style="margin: 0 0 5px 0; font-size: 22px; color: #333; font-weight: 600;"><i class="fas fa-users" style="color: #667eea; margin-right: 8px;"></i> {{__('Suggested Candidates')}}</h3>
                    <p class="text-muted" style="margin: 0; font-size: 14px;">{{__('Candidates in the same job category as your active listings')}}</p>
                </div>
                
                @if(isset($suggestedCandidates) && $suggestedCandidates->count() > 0)
                <ul class="userlisting row" style="margin: 0; list-style: none; padding: 0;">
                    @foreach($suggestedCandidates as $jobSeeker)
                        <li class="col-lg-4 col-md-6">
                            <div class="seekerbox" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; transition: all 0.3s ease; position: relative; height: 100%;">  
                                {{-- @if($jobSeeker->is_featured)
                                    <div class="ribbon ribbon-top-left"><span><i class="fas fa-star"></i> Featured</span></div>  
                                @endif --}}

                                <div class="ltisusrinf">
                                    <div class="userltimg">{{$jobSeeker->printUserImage(100, 100)}}</div>
                                </div>                                

                                <div class="hmseekerinfo">
                                    <h3>{{$jobSeeker->getName()}}</h3>                
                                    <div class="hmcate justify-content-center" title="Job Category">{{$jobSeeker->getFunctionalArea('functional_area')}}</div>
                                    <div class="hmcate justify-content-center" title="Career Level"><i class="fas fa-chart-line"></i> {{$jobSeeker->getCareerLevel('career_level')}}</div>
                                    <div class="hmcate justify-content-center"><i class="fas fa-map-marker-alt"></i> {{$jobSeeker->getCity('city')}}</div>  
                                    
                                    <div class="listbtn">
                                        <a href="{{route('user.profile', $jobSeeker->id)}}" style="display: block; width: 100%; text-align: center; padding: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 5px; text-decoration: none; margin-top: 15px;">{{__('View Profile')}}</a>
                                    </div>
                                </div>    
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <div class="text-center mt-3">
                    <a href="{{route('job.seeker.list')}}" class="btn btn-primary">
                        <i class="fas fa-search"></i> {{__('View All Candidates')}}
                    </a>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">{{__('No suggested candidates yet. Post an active job and choose a job category so we can show seekers whose profile uses that same category.')}}</p>
                    <a href="{{route('job.seeker.list')}}" class="btn btn-primary mt-2">
                        <i class="fas fa-search"></i> {{__('Browse All Candidates')}}
                    </a>
                </div>
                @endif
            </div>


            <div class="mt-3 mb-2">
                @include('includes.package_coupon_employer')
            </div>

           @if($company->getPackage('id') == 13 && $company->package_end_date !== null && Carbon\Carbon::parse($company->package_end_date)->gt(Carbon\Carbon::now()) && $company->jobs_quota > $company->availed_jobs_quota)
                <div class="freepackagebox">                   
                    <div class="frpkgct">                    
                        <h5>{{__('Congratulations Your Account is Active now')}}</h5>
                        <p>{{__('You have got')}} {{$company->jobs_quota - $company->availed_jobs_quota}} {{__('free jobs postings remaining, valid for 30 days. Hurry Up before it expired.')}}</p>
                    </div>
                    <a href="{{url('/post-job')}}">{{_('Post a Job')}}</a>
                </div>
            @endif



        <div id="paypackages-job">
        <?php
        if((bool)config('company.is_company_package_active')){        
        $packages = App\Package::where('package_for', 'like', 'employer')->get();
        $package = Auth::guard('company')->user()->getPackage();
        ?>

        

        <?php if(null !== $package){ ?>
        @include('includes.company_package_msg')
     {{-- @include('includes.company_packages_upgrade') --}} <!---Remove the curly brackets to show job packages on the employer dashboardn-->
        <?php }elseif(null !== $packages){ ?>
        @include('includes.company_packages_new')
        <?php } ?>
        <?php } else { ?>
        {{-- Free-posting notice: shown when admin has disabled monetization --}}
        <div class="free-posting-notice" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 1px solid #28a745; border-radius: 12px; padding: 20px 25px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <div style="font-size: 36px; color: #28a745; flex-shrink: 0;">
                <i class="fas fa-gift"></i>
            </div>
            <div>
                <h5 style="margin: 0 0 5px 0; color: #155724; font-weight: 700;">{{ __('Free Job Posting Active') }}</h5>
                <p style="margin: 0; color: #155724; font-size: 14px;">{{ __('Job packages are currently disabled. You can post jobs for free with no limits.') }}</p>
            </div>
            <div style="margin-left: auto;">
                <a href="{{ url('/post-job') }}" class="btn btn-success" style="font-weight: 600;">
                    <i class="fas fa-plus-circle me-1"></i> {{ __('Post a Job') }}
                </a>
            </div>
        </div>
        <?php } ?>
        </div>



        <div class="paypackages mt-5">
    <!---four-plan-->
    <?php 
        $company = Auth::guard('company')->user(); 
        $currentPackage = $company->cvs_getPackage(); 
    ?>
    @if(null !== $currentPackage && !empty($currentPackage))
        @php
            $isExpired = $company->cvs_package_end_date ? \Carbon\Carbon::parse($company->cvs_package_end_date)->isPast() : true;
        @endphp
        
        @if($isExpired)
            <!-- Expired Package Message -->
            <div class="company-payment-no-records">
                <i class="fas fa-exclamation-triangle" style="color: #ff6348; font-size: 64px; margin-bottom: 20px;"></i>
                <h3>{{__('Your CVs Package Has Expired')}}</h3>
                <p>{{__('Your package expired on')}} <strong>{{ \Carbon\Carbon::parse($company->cvs_package_end_date)->format('d M, Y') }}</strong></p>
                <p>{{__('Please purchase a new package to continue accessing candidate CVs')}}</p>
                <a href="{{ route('company.packages') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> {{__('Buy New Package')}}
                </a>
            </div>
        @else
            <div class="company-cvs-package-details">
                <div class="package-header">
                    <h3><i class="fas fa-file-alt"></i> {{__('Purchased CVs Package Details')}}</h3>
                </div>
                
                <div class="package-info-grid">
        <!-- Package Name Card -->
        <div class="package-info-card package-name-card cvs-package">
            <div class="package-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Name')}}</span>
                <h4 class="package-value">{{$currentPackage->package_title}}</h4>
            </div>
        </div>

        <!-- Price Card -->
        <div class="package-info-card cvs-package">
            <div class="package-icon">
                <i class="fas fa-tag"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Price')}}</span>
                <h4 class="package-value">{{ $siteSetting->default_currency_code }} {{$currentPackage->package_price}}</h4>
            </div>
        </div>

        <!-- CV Quota Card -->
        <div class="package-info-card quota-card cvs-package">
            <div class="package-icon">
                <i class="fas fa-file-download"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Available CV Quota')}}</span>
                <h4 class="package-value">
                    <span class="quota-available">{{ $company->getRemainingCvsQuota() }}</span>
                    <span class="quota-separator">/</span>
                    <span class="quota-total">{{$company->cvs_quota}}</span>
                </h4>
            </div>
        </div>

        <!-- Start Date Card -->
        <div class="package-info-card cvs-package">
            <div class="package-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Purchased On')}}</span>
                <h4 class="package-value">{{Carbon\Carbon::parse($company->cvs_package_start_date)->format('d M, Y')}}</h4>
            </div>
        </div>

        <!-- End Date Card -->
        <div class="package-info-card cvs-package">
            <div class="package-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div class="package-content">
                <span class="package-label">{{__('Package Expires')}}</span>
                <h4 class="package-value">{{Carbon\Carbon::parse($company->cvs_package_end_date)->format('d M, Y')}}</h4>
            </div>
        </div>
                </div>
            </div>
        @endif
    @else
        <!-- No Package Message -->
        <div class="company-payment-no-records">
            <i class="fas fa-inbox"></i>
            <h3>{{__('No Active CVs Package Found')}}</h3>
            <p>{{__('You haven\'t purchased any CVs package yet')}}</p>
            <p>{{__('Purchase a package to unlock and view candidate CVs')}}</p>
            <a href="{{ route('company.packages') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> {{__('Buy Package')}}
            </a>
        </div>
    @endif
    
    @if(null !== $currentPackage && !empty($currentPackage))
    <div class="four-plan">
            <h3>{{__('Upgrade CV Search Package')}}</h3>
            <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> {{ __('Unlock applicant resumes by chosing a package below, or Activate free trail if eligible.') }}</p>
            <div class="row">
                <?php $packages = App\Package::get(); ?>
                @foreach($packages as $package)
                    @if($package->package_for == 'cv_search')
                        @if((float) $package->package_price > 0)
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">{{$package->package_price}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Premium Support 24/7')}}</li> 
                                <li class="order paypal"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buypack{{$package->id}}" class="reqbtn">{{__('Buy Now')}} <i class="fas fa-arrow-right"></i></a></li>
                            </ul>
                        </div>

                        <div class="modal fade" id="buypack{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('Buy Now')}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                       
                        <div class="invitereval">
                        <h3>{{__('Choose Your Payment Method')}}</h3>	
                            
                        <div class="totalpay">{{__('Total Amount to pay')}}: <strong>{{ $siteSetting->default_currency_code }} {{$package->package_price}}</strong></div>
                            
                        <ul class="btn2s">
                        
                                @if((bool)$siteSetting->is_paypal_active)
                                <li class="order paypal p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.upgrade.package', $package->id)}}')" class="paypal">
                                            {{__('PayPal')}} <i class="fab fa-cc-paypal" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_stripe_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Stripe')}} <i class="fab fa-cc-stripe" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paystack_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paystack')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_razorpay_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Razorpay')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paytm_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paytm')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_payu_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=upgrade'])}}')">
                                            {{__('PayU')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif

                        </ul>		
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        @else
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">0</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Premium Support 24/7')}}</li>
                                @if(! $company->canActivateFreeCvSearchPackage())
                                    <li class="order paypal"><span class="reqbtn" style="opacity: 0.6; cursor: not-allowed;" title="{{ $company->getFreeCvPackageNextAvailableAt() ? __('Available again from :date', ['date' => $company->getFreeCvPackageNextAvailableAt()->format('d M Y H:i')]) : '' }}">{{__('Silver — one activation per 30 days')}} <i class="fas fa-check"></i></span></li>
                                @else
                                    <li class="order paypal"><a href="{{ route('order.free.package', $package->id) }}" class="reqbtn">{{__('Activate Now')}} <i class="fas fa-arrow-right"></i></a></li>
                                @endif
                            </ul>
                        </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="four-plan">
            <h3>{{__('CV Search Packages')}}</h3>
            <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> {{ __('Paid: Buy Now. Silver: Activate Now when eligible.') }}</p>
            <div class="row">
                <?php $packages = App\Package::get(); ?>
                @foreach($packages as $package)
                    @if($package->package_for == 'cv_search')
                        @if((float) $package->package_price > 0)
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">{{$package->package_price}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Premium Support 24/7')}}</li> 
                                <li class="order paypal"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buypack{{$package->id}}" class="reqbtn">{{__('Buy Now')}} <i class="fas fa-arrow-right"></i></a></li>

                            </ul>
                        </div>

                        <div class="modal fade" id="buypack{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title">{{__('Buy Now')}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        <div class="modal-body">
            
                        <div class="invitereval">
                        <h3> Choose Your Payment Method</h3>	
                            
                        <div class="totalpay">{{__('Total Amount to pay')}}: <strong>{{ $siteSetting->default_currency_code }} {{$package->package_price}}</strong></div>
                            
                        <ul class="btn2s">
                        
                                @if((bool)$siteSetting->is_paypal_active)
                                <li class="order paypal p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.upgrade.package', $package->id)}}')" class="paypal">
                                            <i class="fab fa-cc-paypal" aria-hidden="true"></i> {{__('PayPal')}}
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_stripe_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'upgrade'])}}')">
                                            <i class="fab fa-cc-stripe" aria-hidden="true"></i> {{__('Stripe')}}
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paystack_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'upgrade'])}}')">
                                            <i class="fas fa-credit-card" aria-hidden="true"></i> {{__('Paystack')}}
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_razorpay_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'upgrade'])}}')">
                                            <i class="fas fa-credit-card" aria-hidden="true"></i> {{__('Razorpay')}}
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paytm_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'upgrade'])}}')">
                                            <i class="fas fa-credit-card" aria-hidden="true"></i> {{__('Paytm')}}
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_payu_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=upgrade'])}}')">
                                            <i class="fas fa-credit-card" aria-hidden="true"></i> {{__('PayU')}}
                                        </a>
                                        </li>
                                @endif

                        </ul>		
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        @else
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">0</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Premium Support 24/7')}}</li>
                                @if(! $company->canActivateFreeCvSearchPackage())
                                    <li class="order paypal"><span class="reqbtn" style="opacity: 0.6; cursor: not-allowed;" title="{{ $company->getFreeCvPackageNextAvailableAt() ? __('Available again from :date', ['date' => $company->getFreeCvPackageNextAvailableAt()->format('d M Y H:i')]) : '' }}">{{__('Silver — one activation per 30 days')}} <i class="fas fa-check"></i></span></li>
                                @else
                                    <li class="order paypal"><a href="{{ route('order.free.package', $package->id) }}" class="reqbtn">{{__('Activate Now')}} <i class="fas fa-arrow-right"></i></a></li>
                                @endif
                            </ul>
                        </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    <!---end four-plan-->
</div>




        </div>

        <!-- Third Column - Right Sidebar -->
        <div class="col-lg-2" style="flex: 0 0 auto;">
            
            <!-- Mobile App CTA -->
            <div class="sidebar-box mobile-app-cta" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); text-align: center; color: #fff;">
                <div style="font-size: 48px; margin-bottom: 15px;">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h4 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600; color: #fff;">{{__('Download Medojob App')}}</h4>
                <p style="margin: 0 0 20px 0; font-size: 14px; color: rgba(255,255,255,0.9);">{{__('Manage your jobs on the go! Download our mobile app now.')}}</p>
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
<style type="text/css">
    .company-verification-reminder {
        background: linear-gradient(135deg, #fff8eb 0%, #fff 100%);
        border: 1px solid #f3d9a4;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 25px;
    }
    .company-verification-reminder__content {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
    }
    .company-verification-reminder h4 {
        margin: 0 0 8px;
        font-weight: 700;
        color: #7c4a03;
    }
    .company-verification-reminder p {
        margin: 0;
        color: #6b7280;
    }
    .company-verification-reminder__reason {
        margin-top: 10px !important;
        color: #991b1b !important;
    }
    .company-verification-reminder__actions {
        min-width: 220px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .company-verification-panel {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #f3d9a4;
    }
    .verification-upload-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }
    .verification-upload-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        background: #fff;
    }
    .verification-upload-card__meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
        color: #6b7280;
        margin: 15px 0;
    }
    .verification-upload-card__current {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
    }
    .verification-upload-card__actions {
        margin-top: 15px;
    }
    .verification-required {
        color: #dc2626;
    }
    .verification-optional {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }
    @media (max-width: 767px) {
        .company-verification-reminder__content {
            flex-direction: column;
        }
        .company-verification-reminder__actions {
            min-width: 0;
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleButton = document.getElementById('toggle-company-verification');
        var panel = document.getElementById('company-verification-panel');

        if (!toggleButton || !panel) {
            return;
        }

        toggleButton.addEventListener('click', function () {
            var isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';
        });
    });
</script>
@endpush

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://sandbox.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID')}}"></script>
@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __("Success") }}',
            text: '{{ session("success") }}',
            confirmButtonText: '{{ __("OK") }}'
        });
    </script>
@endif
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return fetch('/paypal/order', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    package_id:'3'  // Pass the relevant package_id
                })
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                return orderData.id;
            });
        },
        onApprove: function(data, actions) {
            return fetch('/paypal/order/3/capture', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                // Handle the captured order details
                console.log('Capture result', orderData);
            });
        }
    }).render('#paypal-button-container');
</script>

@include('includes.immediate_available_btn')

<!-- Payment Gateway Error Modal -->
<div class="modal fade" id="paymentGatewayErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Payment Gateway Error')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <p id="paymentGatewayErrorMsg"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
function checkPaymentGateway(gateway, isConfigured, redirectUrl) {
    // Close the payment selection modal if one is open
    const openModal = document.querySelector('.modal.show');
    if (openModal) {
        const modalInstance = bootstrap.Modal.getInstance(openModal);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
    
    if (isConfigured === '1') {
        window.location.href = redirectUrl;
    } else {
        document.getElementById('paymentGatewayErrorMsg').innerHTML = '{{__("This payment gateway is not properly configured. Please contact the administrator.")}}';
        new bootstrap.Modal(document.getElementById('paymentGatewayErrorModal')).show();
    }
}
</script>

@endpush

@livewire('apply-job-modal')
