@if(Auth::guard('company')->check())
<form action="{{route('job.seeker.list')}}" method="get">
    <div class="searchbar">
        <h3>{{__('Resume Search for Employers')}}</h3>        
		<div class="srchbox">	
		<div class="input-group mt-3">
        <input type="text"  name="search" id="empsearch" value="{{Request::get('search', '')}}" class="form-control" placeholder="{{__('Search For Job Seekers')}}" autocomplete="off" />
        
        @php
            $locationLevels = $siteSetting->location_levels ?? 3;
        @endphp
        
        @if($locationLevels == 3)
            @if((bool)$siteSetting->country_specific_site)
                {!! Form::hidden('country_id[]', Request::get('country_id[]', $siteSetting->default_country_id), array('id'=>'country_id')) !!}
            @else
                {!! Form::select('country_id[]', ['' => __('Select Country')]+$countries, Request::get('country_id', $siteSetting->default_country_id), array('class'=>'form-control', 'id'=>'country_id')) !!}
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
</form>
@else
	<form action="{{route('job.list')}}" method="get">
		<!-- Modern Search Form - Matches Screenshot -->
		<div class="modern-search-form" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
			<div class="row g-2 align-items-center">
				<!-- Job Title Input -->
				<div class="col-md-5">
					<div class="input-group" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
						<span class="input-group-text" style="background: #fff; border: none; padding: 12px 15px;">
							<i class="fas fa-search" style="color: #999;"></i>
						</span>
						<input type="text" name="search" id="jbsearch" value="{{Request::get('search', '')}}" 
							   class="form-control" 
							   placeholder="{{__('Job title, keywords...')}}" 
							   autocomplete="off" 
							   style="border: none; padding: 12px 10px; font-size: 15px; box-shadow: none;" />
					</div>
				</div>
				
				<!-- Location Text Input -->
				<div class="col-md-5">
					<div class="input-group" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
						<span class="input-group-text" style="background: #fff; border: none; padding: 12px 15px;">
							<i class="fas fa-map-marker-alt" style="color: #999;"></i>
						</span>
						<input type="text" name="location_search" id="location_search" value="{{Request::get('location_search', '')}}" 
							   class="form-control" 
							   placeholder="{{__('City or postcode')}}" 
							   autocomplete="off" 
							   style="border: none; padding: 12px 10px; font-size: 15px; box-shadow: none;" />
					</div>
				</div>
				
				<!-- Find Jobs Button -->
				<div class="col-md-2">
					<button type="submit" class="btn btn-success w-100" style="padding: 12px 20px; font-size: 16px; font-weight: 600; border-radius: 8px; background: #28a745; border: none;">
						{{__('Find Jobs')}}
					</button>
				</div>
			</div>
		</div>
		
		<!-- Popular Searches -->
		<div class="popular-searches mt-3" style="text-align: left;">
			<span style="color: #666; font-size: 14px;">{{__('Popular Searches')}} :</span>
			<a href="{{route('job.list', ['search' => 'HCA'])}}" style="color: #666; font-size: 14px; margin-left: 10px; text-decoration: none;">HCA</a>,
			<a href="{{route('job.list', ['search' => 'LPN'])}}" style="color: #666; font-size: 14px; margin-left: 5px; text-decoration: none;">LPN</a>,
			<a href="{{route('job.list', ['search' => 'RN'])}}" style="color: #666; font-size: 14px; margin-left: 5px; text-decoration: none;">RN</a>,
			<a href="{{route('job.list', ['search' => 'Home Care'])}}" style="color: #666; font-size: 14px; margin-left: 5px; text-decoration: none;">Home Care</a>,
			<a href="{{route('job.list', ['search' => 'Recreation'])}}" style="color: #666; font-size: 14px; margin-left: 5px; text-decoration: none;">Recreation</a>
		</div>
	</form>
@endif

