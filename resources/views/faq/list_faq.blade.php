@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end --> 
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Frequently asked questions')])
<!-- Inner Page Title end -->
<!-- Page Title End -->
<style>
.faq-category {
    margin-bottom: 3rem;
}
.category-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #007bff;
}
.category-description {
    color: #666;
    font-size: 1rem;
    margin-bottom: 1.5rem;
}
</style>
<div class="listpgWraper">
    <div class="container"> 
        <!--Question-->
        <div class="faqs">
            @if(isset($categories) && count($categories) > 0)
                @foreach($categories as $category)
                    @if($category->faqs && count($category->faqs) > 0)
                        <div class="faq-category mb-5">
                            <h3 class="category-title mb-3">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="category-description mb-3">{{ $category->description }}</p>
                            @endif
                            <div class="accordion" id="accordion-{{ $category->id }}">
                                @foreach($category->faqs as $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="false" aria-controls="collapse{{ $faq->id }}">
                                            {!! $faq->faq_question !!}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-{{ $category->id }}">
                                        <div class="accordion-body">
                                            {!! $faq->faq_answer !!}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            @if(isset($uncategorizedFaqs) && count($uncategorizedFaqs) > 0)
                <div class="faq-category mb-5">
                    <h3 class="category-title mb-3">General Questions</h3>
                    <div class="accordion" id="accordion-uncategorized">
                        @foreach($uncategorizedFaqs as $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="false" aria-controls="collapse{{ $faq->id }}">
                                    {!! $faq->faq_question !!}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-uncategorized">
                                <div class="accordion-body">
                                    {!! $faq->faq_answer !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">{!! $siteSetting->cms_page_ad !!}</div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection