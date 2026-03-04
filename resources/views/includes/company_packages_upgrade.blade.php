@if($packages->count())

<div class="paypackages"> 

    <!---four-paln-->

    <div class="four-plan">

        <h3>{{__('Upgrade Job Packages')}}</h3>

        <div class="row"> @foreach($packages as $package)
		@if($package->package_price > 0)
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

                    <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Job Posting')}} {{$package->package_num_listings}}</li>
                    <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Job Displayed for')}} {{$package->package_num_days}} {{__('Days')}}</li>   

					@if($package->package_price == 10)
					<li class="plan-pages noadded"><i class="far fa-times-circle"></i> {{__('Highlights Jobs')}}</li>  
					<li class="plan-pages @if($package->package_price == 0) disabled @endif"><i class="far fa-times-circle"></i> {{__('Premium Support 24/7')}}</li> 
					@else
                    <li class="plan-pages"><i class="far fa-check-circle"></i> {{__('Highlights jobs on Demand')}}</li>  
					<li class="plan-pages @if($package->package_price == 0) disabled @endif"><i class="far fa-check-circle"></i> {{__('Premium Support 24/7')}}</li>  
					
					@endif
					
                   

                    <li class="order paypal"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buypack{{$package->id}}" class="reqbtn">{{__('Buy Now')}} <i class="fas fa-arrow-right"></i></a></li>

                </ul>
				
				
				<div class="modal fade" id="buypack{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
		<div class="modal-header">
        <h5 class="modal-title">{{__('Buy Now')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

		<div class="modal-body">
		
		<div class="invitereval">
		<h3>Please Choose Your Payment Method to Pay</h3>	
			
		<div class="totalpay">{{__('Total Amount to pay')}}: <strong>{{ $siteSetting->default_currency_code }} {{$package->package_price}}</strong></div>
			
		<ul class="btn2s">
		@if((bool)$siteSetting->is_paypal_active)
		<li class="order paypal p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.upgrade.package', $package->id)}}')">{{__('Paypal')}} <i class="fab fa-cc-paypal" aria-hidden="true"></i></a></li>
		@endif
		@if((bool)$siteSetting->is_paystack_active)
            <li class="order p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'upgrade'])}}')">{{__('Paystack')}} <i class="fas fa-credit-card" aria-hidden="true"></i></a></li>
        @endif
		@if((bool)$siteSetting->is_stripe_active)
		<li class="order p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'upgrade'])}}')">{{__('Stripe')}} <i class="fab fa-cc-stripe" aria-hidden="true"></i></a></li>
		@endif
		@if((bool)$siteSetting->is_razorpay_active)
		<li class="order p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'upgrade'])}}')">{{__('Razorpay')}} <i class="fas fa-credit-card" aria-hidden="true"></i></a></li>
		@endif
		@if((bool)$siteSetting->is_paytm_active)
		<li class="order p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'upgrade'])}}')">{{__('Paytm')}} <i class="fas fa-credit-card" aria-hidden="true"></i></a></li>
		@endif
		@if((bool)$siteSetting->is_payu_active)
		<li class="order payu p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=upgrade'])}}')">{{__('PayU')}} <i class="fas fa-credit-card" aria-hidden="true"></i></a></li>
		@endif
		@if((bool)$siteSetting->is_iyzico_active)
		<li class="order p-2"><a href="javascript:void(0)" onclick="checkPaymentGateway('iyzico', '{{!empty($siteSetting->iyzico_api_key) && !empty($siteSetting->iyzico_secret_key)}}', '{{route('iyzico.order.form', [$package->id, 'upgrade'])}}')"><i class="fas fa-credit-card" aria-hidden="true"></i> {{__('Iyzico')}}</a></li>
		@endif
		</ul>		
		</div>
		</div>
		</div>
		</div>
		</div>

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

            </div>
			@endif
            @endforeach </div>

    </div>

    <!---end four-paln--> 

</div>

@endif