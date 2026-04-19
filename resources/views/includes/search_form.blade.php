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

    {{-- Outer pill container --}}
    <div style="
        background: #fff;
        border-radius: 50px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.13);
        padding: 10px 14px;
        max-width: 760px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 8px;
    ">
        {{-- Job title input --}}
        <div style="
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 25px;
            overflow: hidden;
            flex: 1;
            background: #fff;
        ">
            <span style="padding: 0 10px; color: #aaa; font-size: 13px; line-height: 1;">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" id="jbsearch"
                   value="{{Request::get('search', '')}}"
                   placeholder="{{__('Job title, keywords...')}}"
                   autocomplete="off"
                   style="
                       border: none;
                       outline: none;
                       box-shadow: none;
                       padding: 10px 10px 10px 0;
                       font-size: 15px;
                       width: 100%;
                       background: transparent;
                   " />
        </div>

        {{-- Location input --}}
        <div style="
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 25px;
            overflow: hidden;
            flex: 1;
            background: #fff;
        ">
            <span style="padding: 0 10px; color: #aaa; font-size: 13px; line-height: 1;">
                <i class="fas fa-map-marker-alt"></i>
            </span>
            <input type="text" name="location_search" id="location_search"
                   value="{{ \App\Helpers\MiscHelper::locationSearchFormValue() }}"
                   placeholder="{{__('City or postcode')}}"
                   autocomplete="off"
                   style="
                       border: none;
                       outline: none;
                       box-shadow: none;
                       padding: 10px 10px 10px 0;
                       font-size: 15px;
                       width: 100%;
                       background: transparent;
                   " />
        </div>

        {{-- Find Jobs button --}}
        <button type="submit" style="
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 10px 22px;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            flex-shrink: 0;
        ">{{__('Find Jobs')}}</button>
    </div>

    {{-- Popular Searches --}}
    <div style="text-align: center; margin-top: 14px;">
        <span style="color: rgba(255,255,255,0.9); font-size: 14px;">{{__('Popular Searches')}} :</span>
        <a href="{{route('job.list', ['search' => 'HCA'])}}"   style="color:rgba(255,255,255,0.95);font-size:14px;margin-left:6px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.4);">HCA</a>,
        <a href="{{route('job.list', ['search' => 'LPN'])}}"   style="color:rgba(255,255,255,0.95);font-size:14px;margin-left:6px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.4);">LPN</a>,
        <a href="{{route('job.list', ['search' => 'RN'])}}"    style="color:rgba(255,255,255,0.95);font-size:14px;margin-left:6px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.4);">RN</a>,
        <a href="{{route('job.list', ['search' => 'Home Care'])}}" style="color:rgba(255,255,255,0.95);font-size:14px;margin-left:6px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.4);">Home Care</a>,
        <a href="{{route('job.list', ['search' => 'Recreation'])}}" style="color:rgba(255,255,255,0.95);font-size:14px;margin-left:6px;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.4);">Recreation</a>
    </div>

</form>
@endif
