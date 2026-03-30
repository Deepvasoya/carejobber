@extends('layouts.app')

@section('content') 

<!-- Header start --> 

@include('includes.header') 

<!-- Header end --> 

<!-- Inner Page Title start --> 

@include('includes.inner_page_title', ['page_title'=>__('Welcome to Employer Dashboard')]) 

<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container">@include('flash::message')

        <div class="row"> @include('includes.company_dashboard_menu')
        <?php $company = auth()->guard('company')->user(); ?>

        <div class="col-lg-9"> 
            <?php if ($company->is_active == 1 && (($company->package_end_date === null) || 
                (\Carbon\Carbon::parse($company->package_end_date)->lt(\Carbon\Carbon::now())) || 
                ($company->jobs_quota <= $company->availed_jobs_quota))) { ?>    

                <div class="userprofilealert">
                    <h5>
                        <i class="fas fa-check"></i> 
                        {{ __('Your account is active now, you can start Posting Jobs.') }}
                    </h5>
                </div>

            <?php } elseif ($company->is_active != 1) { ?> 
                <div class="userprofilealert">
                    <h5>
                        <i class="fas fa-times"></i> 
                        {{__('Your account is currently inactive due to pending verification.')}}
                    </h5>
                </div>
            <?php } ?> 
      

            
            
            @include('includes.company_dashboard_stats')

            <div class="mt-3 mb-2">
                @include('includes.package_coupon_employer')
            </div>

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
        <?php }} ?>
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
        </div>
    </div>
</div>




@include('includes.footer')

@endsection


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
