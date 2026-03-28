<div class="section featuredjobwrap">
    <div class="container"> 
    @if(isset($featuredJobs) && count($featuredJobs))
        <!-- title start -->
        <div class="titleTop text-center">
            <h3>{{__('Featured Vacancies')}}</h3>
        </div>
        <!-- title end --> 

        <!--Featured Job start-->
        <ul class="featuredlist row">
            
            @foreach($featuredJobs as $featuredJob)
            <?php $company = $featuredJob->getCompany(); ?>
            @if(null !== $company)
            <!--Job start-->
            <li class="col-lg-3 col-md-6 @if($featuredJob->is_featured == 1) featured @endif">
                <div class="jobint @if(!empty($featuredJob->is_highlighted)) job-card-highlighted @endif">
                    @if($featuredJob->is_urgent == 1)
                        <span class="promotepof-badge-left" title="{{__('Urgent')}}"><i class="fas fa-fire"></i></span>
                    @endif
                    @if($featuredJob->is_featured == 1)
                        <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('Featured')}}"></i></span>
                    @endif
                    <div class="d-flex">
                        <div class="fticon"><i class="fas fa-briefcase"></i> {{$featuredJob->getJobType('job_type')}}</div>                        
                    </div>

                    <h4><a href="{{route('job.detail', [$featuredJob->slug])}}" title="{{$featuredJob->title}}">{!! \Illuminate\Support\Str::limit($featuredJob->title, $limit = 20, $end = '...') !!}</a></h4>
                    <strong><i class="fas fa-map-marker-alt"></i> {{$featuredJob->getCity('city')}}</strong> 
                    
                    <div class="jobcompany">
                     <div class="ftjobcomp">
                        <span>{{$featuredJob->created_at->format('M d, Y')}}</span>
                        <a href="{{route('company.detail', $company->slug)}}" title="{{$company->name}}">{{$company->name}}</a>
                     </div>
                    <a href="{{route('company.detail', $company->slug)}}" class="company-logo" title="{{$company->name}}">{{$company->printCompanyImage()}} </a>
                    </div>
                </div>
            </li>
            <!--Job end--> 
            @endif
            @endforeach
           

        </ul>
        <!--Featured Job end--> 

        <!--button start-->
        <div class="viewallbtn"><a href="{{route('job.list')}}">{{__('View all jobs')}}</a></div>
        <!--button end--> 
    
     @endif
    </div>

    
    
</div>
@push('styles')
<style>
    .job-card-highlighted {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
        border: 1px solid #f59e0b !important;
        box-shadow: 0 2px 12px rgba(245, 158, 11, 0.12);
    }
    .promotepof-badge.job-urgent-badge {
        background: #dc2626 !important;
        left: 10px;
        right: auto;
    }
</style>
@endpush
