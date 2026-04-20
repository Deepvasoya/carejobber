@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end --> 
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Frequently asked questions')])
<!-- Inner Page Title end -->
<style>
.faq-wrapper {
    padding: 40px 0;
}
.faq-tabs {
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 30px;
}
.faq-tabs .nav-link {
    color: #666;
    font-weight: 600;
    font-size: 16px;
    padding: 12px 24px;
    border: none;
    border-bottom: 3px solid transparent;
    background: transparent;
    text-transform: uppercase;
}
.faq-tabs .nav-link:hover {
    color: #007bff;
    border-bottom-color: #007bff;
}
.faq-tabs .nav-link.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: transparent;
}
.faq-content-wrapper {
    display: flex;
    gap: 30px;
}
.faq-sidebar {
    flex: 0 0 300px;
    background: #F2F7F3;
    padding: 20px;
    border-radius: 8px;
    height: fit-content;
    position: sticky;
    top: 20px;
}
.faq-sidebar-title {
    font-size: 14px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 15px;
}
.faq-category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.faq-category-item {
    margin-bottom: 8px;
}
.faq-category-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s;
    font-size: 14px;
}
.faq-category-link:hover {
    background: #fff;
    color: #007bff;
}
.faq-category-link.active {
    background: #007bff;
    color: #fff;
}
.faq-category-count {
    background: rgba(0,0,0,0.1);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}
.faq-category-link.active .faq-category-count {
    background: rgba(255,255,255,0.2);
}
.faq-main-content {
    flex: 1;
}
.faq-search {
    margin-bottom: 30px;
}
.faq-search input {
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
}
.faq-search input:focus {
    outline: none;
    border-color: #007bff;
}
.faq-question-item {
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}
.faq-question-header {
    padding: 15px 20px;
    background: #fff;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s;
}
.faq-question-header:hover {
    background: #f8f9fa;
}
.faq-question-title {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin: 0;
}
.faq-question-views {
    font-size: 13px;
    color: #999;
}
.faq-answer-body {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    display: none;
}
.faq-answer-body.show {
    display: block;
}
.no-faqs {
    text-align: center;
    padding: 40px;
    color: #999;
}
@media (max-width: 768px) {
    .faq-content-wrapper {
        flex-direction: column;
    }
    .faq-sidebar {
        flex: 1;
        position: static;
    }
}
</style>

<div class="faq-wrapper">
    <div class="container">
        @if(isset($sections) && count($sections) > 0)
            <!-- Section Tabs -->
            <ul class="nav faq-tabs" role="tablist">
                @foreach($sections as $index => $section)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="section-{{ $section->id }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#section-{{ $section->id }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="section-{{ $section->id }}" 
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $section->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                @foreach($sections as $index => $section)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="section-{{ $section->id }}" 
                         role="tabpanel" 
                         aria-labelledby="section-{{ $section->id }}-tab">
                        
                        @if($section->description)
                            <p class="text-muted mb-4">{{ $section->description }}</p>
                        @endif

                        <div class="faq-content-wrapper">
                            <!-- Sidebar with Categories -->
                            @if($section->categories && count($section->categories) > 0)
                                <aside class="faq-sidebar">
                                    <div class="faq-sidebar-title">Question Categories</div>
                                    <ul class="faq-category-list">
                                        @foreach($section->categories as $catIndex => $category)
                                            <li class="faq-category-item">
                                                <a href="#category-{{ $category->id }}" 
                                                   class="faq-category-link {{ $catIndex === 0 ? 'active' : '' }}" 
                                                   data-category="{{ $category->id }}">
                                                    <span>{{ $category->name }}</span>
                                                    <span class="faq-category-count">{{ $category->faqs_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </aside>

                                <!-- Main Content -->
                                <div class="faq-main-content">
                                    <!-- Search Box -->
                                    <div class="faq-search">
                                        <input type="text" 
                                               class="faq-search-input" 
                                               placeholder="Search your queries here..." 
                                               data-section="{{ $section->id }}">
                                    </div>

                                    <!-- Categories and Questions -->
                                    @foreach($section->categories as $category)
                                        <div class="faq-category-section" id="category-{{ $category->id }}">
                                            @if($category->faqs && count($category->faqs) > 0)
                                                @foreach($category->faqs as $faqIndex => $faq)
                                                    <div class="faq-question-item" data-question="{{ strtolower($faq->faq_question) }}">
                                                        <div class="faq-question-header" onclick="toggleFaq({{ $faq->id }})">
                                                            <div>
                                                                <span class="badge bg-primary me-2">{{ sprintf('%02d', $faqIndex + 1) }}</span>
                                                                <span class="faq-question-title">{!! strip_tags($faq->faq_question) !!}</span>
                                                            </div>
                                                            <span class="faq-question-views">Views: {{ rand(2000, 5000) }}</span>
                                                        </div>
                                                        <div class="faq-answer-body" id="faq-answer-{{ $faq->id }}">
                                                            {!! $faq->faq_answer !!}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="no-faqs">
                                                    <p>No questions available in this category yet.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="no-faqs">
                                    <p>No categories available in this section yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-faqs">
                <h4>No FAQ sections available</h4>
                <p>Please check back later for frequently asked questions.</p>
            </div>
        @endif

        <div class="row mt-5">
            <div class="col-md-3"></div>
            <div class="col-md-6">{!! $siteSetting->cms_page_ad !!}</div>
            <div class="col-md-3"></div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection

@push('scripts')
<script>
function toggleFaq(faqId) {
    const answerBody = document.getElementById('faq-answer-' + faqId);
    answerBody.classList.toggle('show');
}

// Category navigation
document.querySelectorAll('.faq-category-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links in this sidebar
        const sidebar = this.closest('.faq-sidebar');
        sidebar.querySelectorAll('.faq-category-link').forEach(l => l.classList.remove('active'));
        
        // Add active class to clicked link
        this.classList.add('active');
        
        // Scroll to category
        const categoryId = this.getAttribute('href');
        const categoryElement = document.querySelector(categoryId);
        if (categoryElement) {
            categoryElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Search functionality
document.querySelectorAll('.faq-search-input').forEach(input => {
    input.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const sectionId = this.getAttribute('data-section');
        const section = document.getElementById('section-' + sectionId);
        
        section.querySelectorAll('.faq-question-item').forEach(item => {
            const questionText = item.getAttribute('data-question');
            if (questionText.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush