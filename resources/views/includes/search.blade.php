@php
    $homeHeroWidget = widget(5);
    $heroBgFile = !empty($homeHeroWidget->extra_image_1)
        ? $homeHeroWidget->extra_image_1
        : (!empty($homeHeroWidget->extra_image_2) ? $homeHeroWidget->extra_image_2 : null);
    $homeHeroBg = $heroBgFile ? asset('images/' . $heroBgFile) : null;
@endphp

<div class="searchwrap"
     style="
        position: relative;
        padding: 3.5rem 0 3.25rem;
        {{ $homeHeroBg
            ? 'background-image: linear-gradient(105deg, rgba(15,38,48,0.88) 0%, rgba(15,38,48,0.72) 45%, rgba(15,38,48,0.65) 100%), url(\''.e($homeHeroBg).'\'); background-size: cover; background-position: center center; background-repeat: no-repeat; min-height: 420px; display: flex; align-items: center;'
            : 'background: linear-gradient(135deg, #174a5e 0%, #1f6b82 38%, #1a8a7e 100%);'
        }}
     ">

<div class="container" style="width:100%;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 text-center">
            <div class="srjobseeker">
                <div class="bxsrctxt">
                    @if(Auth::guard('company')->check())
                    <h1 style="color:#fff; font-weight:700; text-shadow:0 2px 18px rgba(0,0,0,0.35);">{{__('Find Top Skilled Candidates')}}.</h1>
                    <p style="color:rgba(255,255,255,0.92); text-shadow:0 1px 10px rgba(0,0,0,0.3);">{{__("Simply enter your resume criteria to instantly search from millions of live, top quality resumes")}}</p>
                    @else
                    <h1 style="color:#fff; font-weight:700; text-shadow:0 2px 18px rgba(0,0,0,0.35);">{{ __($homeHeroWidget->extra_field_1) }}</h1>
                    <p style="color:rgba(255,255,255,0.92); text-shadow:0 1px 10px rgba(0,0,0,0.3);">{{ __($homeHeroWidget->extra_field_2) }}</p>
                    @endif
                </div>
                <div class="searchbarbt" style="margin-top:30px; position:relative;">
                    @include('includes.search_form')
                </div>
            </div>
        </div>
    </div>
</div>
</div>
