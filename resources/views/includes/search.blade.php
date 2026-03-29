
@php
    $homeHeroWidget = widget(5);
    $heroBgFile = !empty($homeHeroWidget->extra_image_1)
        ? $homeHeroWidget->extra_image_1
        : (!empty($homeHeroWidget->extra_image_2) ? $homeHeroWidget->extra_image_2 : null);
    $homeHeroBg = $heroBgFile ? asset('images/' . $heroBgFile) : null;
@endphp

<div class="searchwrap home-search-hero {{ $homeHeroBg ? 'home-search-hero--image' : '' }}"
     @if($homeHeroBg) style="--home-hero-image: url('{{ $homeHeroBg }}');" @endif>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 text-center home-search-hero__inner">
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
    </div>
</div>
</div>















