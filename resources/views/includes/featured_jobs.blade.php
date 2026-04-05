<div class="section featuredjobwrap">
    <div class="container">
        @if (isset($featuredJobs) && count($featuredJobs))
            <div class="titleTop text-center">
                <h3>{{ __('Urgent & Featured Jobs') }}</h3>
            </div>

            <ul class="featuredlist row job-search-list-single home-featured-jobs-2col">
                @foreach ($featuredJobs as $featuredJob)
                    @php
                        $company = $featuredJob->getCompany();
                    @endphp
                    @if (null !== $company)
                        @include('includes.job_search_list_card', [
                            'job' => $featuredJob,
                            'company' => $company,
                            'columnClass' => 'col-lg-6 col-md-6',
                        ])
                    @endif
                @endforeach
            </ul>

            <div class="viewallbtn"><a href="{{ route('job.list') }}">{{ __('View all jobs') }}</a></div>
        @endif
    </div>
</div>

@include('includes.job_list_search_styles')
