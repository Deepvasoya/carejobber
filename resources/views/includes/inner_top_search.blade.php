
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
								
								<input type="text" name="location_search" id="location_search_inner" value="{{Request::get('location_search', '')}}" 
									   class="form-control" 
									   placeholder="{{__('City or postcode')}}" 
									   autocomplete="off" />
								
								<button type="submit" class="btn"><i class="fas fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
	</div>
	<!-- Page Title end -->
</form>
</div>
