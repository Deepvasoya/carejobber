<ul class="side-nav">
    <li class="side-nav-title mt-2">Main</li>
    <li class="side-nav-item">
        <a href="{{ route('admin.home') }}" class="side-nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}">
            <span class="menu-icon"><i class="ri ri-dashboard-2-line"></i></span>
            <span class="menu-text">Dashboard</span>
        </a>
    </li>

    @if(APAuthHelp::can('admin_users.view') || APAuthHelp::can('admin_users.create') || APAuthHelp::can('roles.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#admin-user" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-admin-line"></i></span>
            <span class="menu-text">Admin Users</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="admin-user">
            <ul class="sub-menu">
                @if(APAuthHelp::can('admin_users.view'))<li class="side-nav-item"><a href="{{ route('list.admin.users') }}" class="side-nav-link">List Admin Users</a></li>@endif
                @if(APAuthHelp::can('admin_users.create'))<li class="side-nav-item"><a href="{{ route('create.admin.user') }}" class="side-nav-link">Add Admin User</a></li>@endif
                @if(APAuthHelp::can('roles.manage'))<li class="side-nav-item"><a href="{{ route('list.roles') }}" class="side-nav-link">Manage Roles</a></li>@endif
            </ul>
        </div>
    </li>
    @endif

    @if(APAuthHelp::can('jobs.view') || APAuthHelp::can('jobs.create') || APAuthHelp::can('jobs.import'))
    <li class="side-nav-title mt-2">Modules</li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#job" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-briefcase-line"></i></span>
            <span class="menu-text">Jobs</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="job">
            <ul class="sub-menu">
                @if(APAuthHelp::can('jobs.import'))<li class="side-nav-item"><a href="{{ route('list.jobsB') }}" class="side-nav-link">Import Jobs</a></li>@endif
                @if(APAuthHelp::can('jobs.view'))<li class="side-nav-item"><a href="{{ route('list.jobs') }}" class="side-nav-link">List Jobs</a></li>@endif
                @if(APAuthHelp::can('jobs.create'))<li class="side-nav-item"><a href="{{ route('create.job') }}" class="side-nav-link">Add New Job</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('companies.view') || APAuthHelp::can('companies.create'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#company" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-building-line"></i></span>
            <span class="menu-text">Companies</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="company">
            <ul class="sub-menu">
                @if(APAuthHelp::can('companies.view'))<li class="side-nav-item"><a href="{{ route('list.companies') }}" class="side-nav-link">List Companies</a></li>@endif
                @if(APAuthHelp::can('companies.create'))<li class="side-nav-item"><a href="{{ route('create.company') }}" class="side-nav-link">Add New Company</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('site_users.view') || APAuthHelp::can('site_users.create'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#site-user" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-user-line"></i></span>
            <span class="menu-text">User Profiles</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="site-user">
            <ul class="sub-menu">
                @if(APAuthHelp::can('site_users.view'))<li class="side-nav-item"><a href="{{ route('list.users') }}" class="side-nav-link">List Users</a></li>@endif
                @if(APAuthHelp::can('site_users.create'))<li class="side-nav-item"><a href="{{ route('create.user') }}" class="side-nav-link">Add New User</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('cms.view') || APAuthHelp::can('cms.create'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#cms" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-file-text-line"></i></span>
            <span class="menu-text">C.M.S</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="cms">
            <ul class="sub-menu">
                @if(APAuthHelp::can('cms.view'))<li class="side-nav-item"><a href="{{ route('list.cms') }}" class="side-nav-link">List C.M.S Pages</a></li>@endif
                @if(APAuthHelp::can('cms.create'))<li class="side-nav-item"><a href="{{ route('create.cms') }}" class="side-nav-link">Add C.M.S Page</a></li>@endif
                @if(APAuthHelp::can('cms.view'))<li class="side-nav-item"><a href="{{ route('list.cmsContent') }}" class="side-nav-link">List Translated Pages</a></li>@endif
                @if(APAuthHelp::can('cms.create'))<li class="side-nav-item"><a href="{{ route('create.cmsContent') }}" class="side-nav-link">Add Translate Page</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('blog.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#blogs" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-article-line"></i></span>
            <span class="menu-text">Blog</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="blogs">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('blog') }}" class="side-nav-link">List Blogs</a></li>
                <li class="side-nav-item"><a href="{{ route('add-new-blog') }}" class="side-nav-link">Add New Post</a></li>
                <li class="side-nav-item"><a href="{{ URL::asset('/admin/blog_category')}}" class="side-nav-link">Categories</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('seo.manage'))
    <li class="side-nav-item">
        <a href="{{ route('list.seo') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-search-line"></i></span>
            <span class="menu-text">SEO</span>
        </a>
    </li>
    @endif
    @if(APAuthHelp::can('faq.view') || APAuthHelp::can('faq.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#faq" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-question-line"></i></span>
            <span class="menu-text">FAQs</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="faq">
            <ul class="sub-menu">
                @if(APAuthHelp::can('faq.view'))<li class="side-nav-item"><a href="{{ route('list.faqs') }}" class="side-nav-link">List FAQs</a></li>@endif
                @if(APAuthHelp::can('faq.manage'))<li class="side-nav-item"><a href="{{ route('create.faq') }}" class="side-nav-link">Add FAQ</a></li>@endif
                @if(APAuthHelp::can('faq.manage'))<li class="side-nav-item"><a href="{{ route('list.faq.categories') }}" class="side-nav-link">FAQ Categories</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('site_settings.manage'))
    <li class="side-nav-item">
        <a href="{{ route('list.email.templates') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-mail-settings-line"></i></span>
            <span class="menu-text">Email Templates</span>
        </a>
    </li>
    @endif
    @if(APAuthHelp::can('videos.view') || APAuthHelp::can('videos.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#video" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-video-line"></i></span>
            <span class="menu-text">Videos</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="video">
            <ul class="sub-menu">
                @if(APAuthHelp::can('videos.view'))<li class="side-nav-item"><a href="{{ route('list.videos') }}" class="side-nav-link">List Videos</a></li>@endif
                @if(APAuthHelp::can('videos.manage'))<li class="side-nav-item"><a href="{{ route('create.video') }}" class="side-nav-link">Add Video</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('testimonials.view') || APAuthHelp::can('testimonials.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#testimonial" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-chat-quote-line"></i></span>
            <span class="menu-text">Testimonials</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="testimonial">
            <ul class="sub-menu">
                @if(APAuthHelp::can('testimonials.view'))<li class="side-nav-item"><a href="{{ route('list.testimonials') }}" class="side-nav-link">List Testimonials</a></li>@endif
                @if(APAuthHelp::can('testimonials.manage'))<li class="side-nav-item"><a href="{{ route('create.testimonial') }}" class="side-nav-link">Add Testimonial</a></li>@endif
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('sliders.view') || APAuthHelp::can('sliders.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#slider" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-image-line"></i></span>
            <span class="menu-text">Slider</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="slider">
            <ul class="sub-menu">
                @if(APAuthHelp::can('sliders.view'))<li class="side-nav-item"><a href="{{ route('list.sliders') }}" class="side-nav-link">List Sliders</a></li>@endif
                @if(APAuthHelp::can('sliders.manage'))<li class="side-nav-item"><a href="{{ route('create.slider') }}" class="side-nav-link">Add Slider</a></li>@endif
            </ul>
        </div>
    </li>
    @endif

    @if(APAuthHelp::can('languages.manage'))
    <li class="side-nav-title mt-2">Translation</li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#language" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-translate-2"></i></span>
            <span class="menu-text">Languages</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="language">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.languages') }}" class="side-nav-link">List Languages</a></li>
                <li class="side-nav-item"><a href="{{ route('create.language') }}" class="side-nav-link">Add Language</a></li>
            </ul>
        </div>
    </li>
    @endif

    @if(APAuthHelp::can('countries.manage') || APAuthHelp::can('country_details.manage') || APAuthHelp::can('states.manage') || APAuthHelp::can('cities.manage'))
    <li class="side-nav-title mt-2">Location</li>
    @if(APAuthHelp::can('countries.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#country" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-global-line"></i></span>
            <span class="menu-text">Countries</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="country">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.countries') }}" class="side-nav-link">List Countries</a></li>
                <li class="side-nav-item"><a href="{{ route('create.country') }}" class="side-nav-link">Add Country</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('country_details.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#country-detail" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-map-pin-line"></i></span>
            <span class="menu-text">Country Detail</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="country-detail">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.country.details') }}" class="side-nav-link">List</a></li>
                <li class="side-nav-item"><a href="{{ route('create.country.detail') }}" class="side-nav-link">Add</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('states.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#state" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-map-line"></i></span>
            <span class="menu-text">States</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="state">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.states') }}" class="side-nav-link">List States</a></li>
                <li class="side-nav-item"><a href="{{ route('create.state') }}" class="side-nav-link">Add State</a></li>
            </ul>
        </div>
    </li>
    @endif
    @if(APAuthHelp::can('cities.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#city" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-map-pin-2-line"></i></span>
            <span class="menu-text">Cities</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="city">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.cities') }}" class="side-nav-link">List Cities</a></li>
                <li class="side-nav-item"><a href="{{ route('create.city') }}" class="side-nav-link">Add City</a></li>
            </ul>
        </div>
    </li>
    @endif
    @endif

    @if(APAuthHelp::can('packages.manage'))
    <li class="side-nav-title mt-2">Packages</li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#package" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-price-tag-3-line"></i></span>
            <span class="menu-text">Packages</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="package">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.packages') }}" class="side-nav-link">List Packages</a></li>
                <li class="side-nav-item"><a href="{{ route('create.package') }}" class="side-nav-link">Add Package</a></li>
                <li class="side-nav-item"><a href="{{ route('list.package.coupons') }}" class="side-nav-link">Package coupons</a></li>
            </ul>
        </div>
    </li>
    @endif

    @if(APAuthHelp::can('payments.view'))
    <li class="side-nav-title mt-2">Payments</li>
    <li class="side-nav-item">
        <a href="{{ route('list.payment.hostory') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-bank-card-line"></i></span>
            <span class="menu-text">Companies Payment History</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.users.payment.history') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-wallet-3-line"></i></span>
            <span class="menu-text">Jobseekers Payment History</span>
        </a>
    </li>
    @endif

    @if(APAuthHelp::can('referrals.view'))
    <li class="side-nav-title mt-2">Referral</li>
    <li class="side-nav-item">
        <a href="{{ route('admin.list.referrals') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-gift-line"></i></span>
            <span class="menu-text">Company Referrals</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('admin.list.user.referrals') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-group-line"></i></span>
            <span class="menu-text">Job Seeker Referrals</span>
        </a>
    </li>
    @endif

    @if(APAuthHelp::can('custom_fields.manage'))
    <li class="side-nav-title mt-2">Custom Fields</li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#custom-fields-admin" class="side-nav-link {{ request()->routeIs('list.custom.fields', 'create.custom.field', 'edit.custom.field') ? 'active' : '' }}">
            <span class="menu-icon"><i class="ri ri-input-cursor-move"></i></span>
            <span class="menu-text">Custom Fields</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse {{ request()->routeIs('list.custom.fields', 'create.custom.field', 'edit.custom.field') ? 'show' : '' }}" id="custom-fields-admin">
            <ul class="sub-menu">
                <li class="side-nav-item"><a href="{{ route('list.custom.fields') }}" class="side-nav-link {{ request()->routeIs('list.custom.fields') ? 'active' : '' }}">List Custom Fields</a></li>
                <li class="side-nav-item"><a href="{{ route('create.custom.field') }}" class="side-nav-link {{ request()->routeIs('create.custom.field') ? 'active' : '' }}">Add Custom Field</a></li>
            </ul>
        </div>
    </li>
    @endif

    @if(APAuthHelp::can('job_attributes.manage'))
    <li class="side-nav-title mt-2">Job Attributes</li>
    <li class="side-nav-item">
        <a href="{{ route('list.language.levels') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-font-size"></i></span>
            <span class="menu-text">Language Levels</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.career.levels') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-bar-chart-line"></i></span>
            <span class="menu-text">Career Levels</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.functional.areas') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-layout-grid-line"></i></span>
            <span class="menu-text">Functional Areas</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.genders') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-user-smile-line"></i></span>
            <span class="menu-text">Genders</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.industries') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-building-4-line"></i></span>
            <span class="menu-text">Industries</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.job.experiences') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-time-line"></i></span>
            <span class="menu-text">Job Experience</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.job.skills') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-lightbulb-line"></i></span>
            <span class="menu-text">Job Skills</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.job.types') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-briefcase-4-line"></i></span>
            <span class="menu-text">Job Types</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.job.shifts') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-24-hours-line"></i></span>
            <span class="menu-text">Job Shifts</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.degree.levels') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-graduation-cap-line"></i></span>
            <span class="menu-text">Degree Levels</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.degree.types') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-book-open-line"></i></span>
            <span class="menu-text">Degree Types</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.major.subjects') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-book-2-line"></i></span>
            <span class="menu-text">Major Subjects</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.result.types') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-file-list-3-line"></i></span>
            <span class="menu-text">Result Types</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.marital.statuses') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-heart-line"></i></span>
            <span class="menu-text">Marital Status</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.ownership.types') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-home-gear-line"></i></span>
            <span class="menu-text">Ownership Types</span>
        </a>
    </li>
    <li class="side-nav-item">
        <a href="{{ route('list.salary.periods') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-money-dollar-circle-line"></i></span>
            <span class="menu-text">Salary Periods</span>
        </a>
    </li>
    @endif

    @if(APAuthHelp::can('site_settings.manage') || APAuthHelp::can('widgets.manage'))
    <li class="side-nav-title mt-2">Manage</li>
    @if(APAuthHelp::can('site_settings.manage'))
    <li class="side-nav-item">
        <a href="{{ route('edit.site.setting') }}" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-settings-3-line"></i></span>
            <span class="menu-text">Site Settings</span>
        </a>
    </li>
    @endif
    @if(APAuthHelp::can('widgets.manage'))
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#widgets" class="side-nav-link">
            <span class="menu-icon"><i class="ri ri-layout-grid-2-line"></i></span>
            <span class="menu-text">Static Content Widgets</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="widgets">
            <ul class="sub-menu">
                @php $w_pages = \App\Models\WidgetPages::where('status','active')->get(); @endphp
                @if($w_pages)
                    @foreach($w_pages as $w_p)
                    <li class="side-nav-item"><a href="{{ route('admin.widgets_data',$w_p->slug) }}" class="side-nav-link">{{ $w_p->title }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>
    </li>
    @endif
    @endif
</ul>
