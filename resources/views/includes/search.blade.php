
@php
    $homeHeroWidget = widget(5);
    $homeHeroBg = !empty($homeHeroWidget->extra_image_2)
        ? asset('images/' . $homeHeroWidget->extra_image_2)
        : null;
@endphp

<div class="searchwrap home-search-hero {{ $homeHeroBg ? 'home-search-hero--image' : '' }}"
     @if($homeHeroBg) style="--home-hero-image: url('{{ $homeHeroBg }}');" @endif>

<div class="container">
    
    <div class="row align-items-center">
        <div class="col-lg-9">
    
            <div class="srjobseeker">
                <div class="bxsrctxt">
                    @if(Auth::guard('company')->check())
                    <h1>{{__('Find Top Skilled Candidates')}}.</h1>
                    <p>{{__("Simply enter your resume criteria to instantly search from millions of live, top quality resumes")}}</p>
                    @else
                    <h1>{{ __($homeHeroWidget->extra_field_1) }}</h1>
                    <p>{{ __($homeHeroWidget->extra_field_2) }}</p>
                    @endif
                    
                    
                </div>
                <div class="searchbarbt">
                    @include('includes.search_form')
                </div>
                
                
               
                
            </div>
   
        </div>
        <div class="col-lg-3 mt-4 mt-lg-0">
            @if((bool)$siteSetting->is_slider_active)
            <!-- Revolution slider start -->
            <div class="tp-banner-container">
                <div class="tp-banner" >
                    <ul>
                    @if(isset($sliders) && count($sliders))
                        @foreach($sliders as $slide)
                        <!--Slide-->
                        <li data-slotamount="7" data-transition="slotzoom-horizontal" data-masterspeed="1000" data-saveperformance="on"> <img alt="{{$slide->slider_heading}}" src="{{asset('/')}}images/dummy.png" data-lazyload="{{ ImgUploader::print_image_src('/slider_images/'.$slide->slider_image) }}">
                            <div class="caption lft large-title tp-resizeme slidertext1" data-x="center" data-y="90" data-speed="600" data-start="1600">{{$slide->slider_heading}}</div>
                            <div class="caption lfb large-title tp-resizeme sliderpara" data-x="center" data-y="140" data-speed="600" data-start="2800">{!!$slide->slider_description!!}</div>
                            <div class="caption lfb large-title tp-resizeme slidertext5" data-x="center" data-y="200" data-speed="600" data-start="3500"><a href="{{$slide->slider_link}}">{{$slide->slider_link_text}}</a></div>
                        </li>
                        <!--Slide end--> 
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <!-- Revolution slider end --> 
            


            @else

            <div class="homesearchimg"><img src="{{asset('images/'.$homeHeroWidget->extra_image_1)}}" alt=""></div>


            @endif


        </div>
    </div>   

</div>
</div>















