
<div class="pageSearch">
<form action="{{route('job.list')}}" method="get">
	<!-- Page Title start -->
	<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-10">
						@if(Auth::guard('company')->check())
						<h3 class="mb-3">{{__('Looking for the right candidate? Search Jobseekers Today')}}.</h3>
						@else
						<h3 class="mb-3">{{__('One million success stories. Start yours today')}}.</h3>
						@endif
						<div class="searchform">
							<div class="input-group flex-wrap">
								<input type="text" name="search" id="jbsearch" value="{{Request::get('search', '')}}" class="form-control" placeholder="{{__('Job title or keywords')}}" autocomplete="off" />
								
								@php
									$locationLevels = $siteSetting->location_levels ?? 3;
								@endphp
								
								@if($locationLevels == 3)
									@if((bool)$siteSetting->country_specific_site)
										{!! Form::hidden('country_id[]', Request::get('country_id', $siteSetting->default_country_id), array('id'=>'country_id')) !!}
									@else
										{!! Form::select('country_id[]', ['' => __('Select Country')]+$countries, Request::get('country_id', $siteSetting->default_country_id ?? null), array('class'=>'form-control', 'id'=>'country_id')) !!}
									@endif
								@endif
								
								@if(in_array($locationLevels, [2, 3]))
									<span id="state_dd">
										{!! Form::select('state_id[]', ['' => __('Select Province/State')], Request::get('state_id', null), array('class'=>'form-control', 'id'=>'state_id')) !!}
									</span>
								@endif
								
								<span id="city_dd">
									{!! Form::select('city_id[]', ['' => __('Select City')], Request::get('city_id', null), array('class'=>'form-control', 'id'=>'city_id')) !!}
								</span>
								<button type="submit" class="btn"><i class="fas fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
	</div>
	<!-- Page Title end -->
</form>
</div>
