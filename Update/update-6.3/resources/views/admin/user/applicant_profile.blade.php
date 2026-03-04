@extends('admin.layouts.admin_layout')
@push('css')
<style>
    .profile-detail-logo { max-width: 100px; overflow: hidden; border-radius: 8px; }
    .profile-detail-logo img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; display: block; }
    .profile-detail-list { list-style: none; padding: 0; margin: 0; }
    .profile-detail-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .profile-detail-list li:last-child { border-bottom: 0; }
</style>
@endpush
@section('content')
<?php
$profileCv = $user->getDefaultCv();
$profile_skills = $user->profileSkills;
$skills_arr = array();
if (null !== $profile_skills) {
    foreach ($profile_skills as $value) {
        $skills_arr[] = $value->job_skill_id;
    }
}
$job_ids = \App\JobApply::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
?>
<div class="page-bar mb-3">
    <ul class="page-breadcrumb mb-0">
        <li><a href="{{ route('admin.home') }}">Home</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><a href="{{ route('list.users') }}">Users</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><span>Applicant Profile</span></li>
    </ul>
</div>

{{-- Candidate overview card --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-start">
            <div class="col-md-8">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 profile-detail-logo">{!! $user->printUserImage(100, 100) !!}</div>
                    <div>
                        <h4 class="mb-1">{{ $user->getName() }}</h4>
                        @if($user->address)
                        <p class="text-muted mb-1 small">{{ $user->address }}</p>
                        @endif
                        <p class="mb-2 small text-muted">
                            <i class="ri ri-calendar-line me-1"></i> {{ __('Member Since') }}, {{ $user->created_at->format('M d, Y') }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @if(Auth::guard('admin')->user())
                                @if(count($user->profileCvs) <= 0)
                                <span class="btn btn-sm btn-secondary disabled">{{ __('No CV Available') }}</span>
                                @else
                                <a href="{{ asset('cvs/'.$profileCv->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ __('View Candidates CV') }}</a>
                                @endif
                            @endif
                            @if(null !== $profileCv && !empty($profileCv->cv_file) && file_exists(public_path('cvs/'.$profileCv->cv_file)))
                            <a href="{{ asset('cvs/'.$profileCv->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="ri ri-download-line me-1"></i>{{ __('Download CV') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- About Me (Summary) --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-information-line me-1"></i> About Me (Summary)</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $user->getProfileSummary('summary') ?: '–' }}</p>
            </div>
        </div>

        {{-- Education (AJAX) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-graduation-cap-line me-1"></i> {{ __('Education') }}</h5>
            </div>
            <div class="card-body">
                <div id="education_div"></div>
            </div>
        </div>

        {{-- Experience (AJAX) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-briefcase-4-line me-1"></i> {{ __('Experience') }}</h5>
            </div>
            <div class="card-body">
                <div id="experience_div"></div>
            </div>
        </div>

        {{-- Portfolio (AJAX) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-folder-open-line me-1"></i> {{ __('Portfolio') }}</h5>
            </div>
            <div class="card-body">
                <div id="projects_div"></div>
            </div>
        </div>

        {{-- Applied On Jobs --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-file-list-3-line me-1"></i> {{ __('Applied On Jobs') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-align-middle mb-0" id="user_datatable_ajax">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Company Name</th>
                                <th>Applied Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($job_ids->count())
                            @foreach($job_ids as $jo)
                            <?php
                                $jobb = \App\Job::find($jo->job_id);
                                $userr = \App\User::find($jo->user_id);
                                $companyy = $jobb ? $jobb->getCompany() : null;
                            ?>
                            @if($companyy && $jobb)
                            <tr id="user_dt_row_{{ $jo->id }}">
                                <td>{{ $userr ? $userr->name : '–' }}</td>
                                <td><a href="{{ route('public.job', ['id' => $jo->job_id]) }}" target="_blank">{{ $jobb->title }}</a></td>
                                <td>{{ $companyy->name }}</td>
                                <td>{{ date('d/m/Y', strtotime($jo->created_at)) }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @else
                            <tr><td colspan="4" class="text-muted text-center py-4">{{ __('No applications yet.') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Candidate Contact --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-contacts-line me-1"></i> {{ __('Candidate Contact Details') }}</h5>
            </div>
            <div class="card-body">
                @if(!empty($user->phone))
                <p class="mb-1 small"><i class="ri ri-phone-line me-1 text-muted"></i> <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a></p>
                @endif
                @if(!empty($user->mobile_num))
                <p class="mb-1 small"><i class="ri ri-smartphone-line me-1 text-muted"></i> <a href="tel:{{ $user->mobile_num }}">{{ $user->mobile_num }}</a></p>
                @endif
                @if(!empty($user->email))
                <p class="mb-1 small"><i class="ri ri-mail-line me-1 text-muted"></i> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
                @endif
                <p class="mb-0 small"><i class="ri ri-map-pin-line me-1 text-muted"></i> {{ $user->getLocation() }}</p>
            </div>
        </div>

        {{-- Candidate Details --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-user-settings-line me-1"></i> {{ __('Candidate Details') }}</h5>
            </div>
            <div class="card-body">
                <ul class="profile-detail-list">
                    <li><span class="text-muted">{{ __('Available Immediately') }}</span><span>{{ (bool)$user->is_immediate_available ? __('Yes') : __('No') }}</span></li>
                    @if(!(bool)$user->is_immediate_available)
                    <li><span class="text-muted">Available from</span><span>{{ $user->immediate_date_from ? date('d/m/Y', strtotime($user->immediate_date_from)) : date('d/m/Y') }}</span></li>
                    @endif
                    <li><span class="text-muted">{{ __('Eligible to work in the UK:') }}</span><span>{{ (bool)$user->eligible_uk ? __('Yes') : __('No') }}</span></li>
                    <li><span class="text-muted">{{ __('Eligible to work in the EU:') }}</span><span>{{ (bool)$user->eligible_eu ? __('Yes') : __('No') }}</span></li>
                    <li><span class="text-muted">{{ __('Age') }}</span><span>{{ $user->getAge() }} Years</span></li>
                    @if(null !== $user->getGender('gender'))
                    <li><span class="text-muted">{{ __('Gender') }}</span><span>{{ $user->getGender('gender') }}</span></li>
                    @endif
                    @if(null !== $user->getMaritalStatus('marital_status'))
                    <li><span class="text-muted">{{ __('Marital Status') }}</span><span>{{ $user->getMaritalStatus('marital_status') }}</span></li>
                    @endif
                    @if(isset($user->education_id) && $user->education_id && optional($user->education)->degree_level)
                    <li><span class="text-muted">{{ __('Education') }}</span><span>{{ $user->education->degree_level }}</span></li>
                    @endif
                    <li><span class="text-muted">{{ __('Experience') }}</span><span>{{ $user->getJobExperience('job_experience') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Career Level') }}</span><span>{{ $user->getCareerLevel('career_level') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Current Salary') }}</span><span>{{ $user->salary_currency ?? '' }}{{ $user->current_salary ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Expected Salary') }}</span><span>{{ $user->salary_currency ?? '' }}{{ $user->expected_salary ?? '–' }}</span></li>
                </ul>
            </div>
        </div>

        {{-- CV Added --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-file-text-line me-1"></i> {{ __('CV Added') }}</h5>
            </div>
            <div class="card-body">
                @if(count($user->profileCvs) <= 0)
                <span class="text-danger fw-semibold">{{ __('No') }}</span>
                @else
                <span class="text-success fw-semibold">{{ __('Yes') }}</span>
                @endif
            </div>
        </div>

        {{-- Skills (AJAX) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-lightbulb-line me-1"></i> {{ __('Skills') }}</h5>
            </div>
            <div class="card-body">
                <div id="skill_div"></div>
            </div>
        </div>

        {{-- Languages (AJAX) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-translate-2 me-1"></i> {{ __('Languages') }}</h5>
            </div>
            <div class="card-body">
                <div id="language_div"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style type="text/css">.formrow iframe { height: 78px; }</style>
@endpush

@push('scripts')
<script>
$(function() {
    showEducation();
    showProjects();
    showExperience();
    showSkills();
    showLanguages();

    function showProjects() {
        $.post("{{ route('show.applicant.profile.projects', $user->id) }}", { user_id: {{ $user->id }}, _method: 'POST', _token: '{{ csrf_token() }}' })
            .done(function(response) { $('#projects_div').html(response); });
    }
    function showExperience() {
        $.post("{{ route('show.applicant.profile.experience', $user->id) }}", { user_id: {{ $user->id }}, _method: 'POST', _token: '{{ csrf_token() }}' })
            .done(function(response) { $('#experience_div').html(response); });
    }
    function showEducation() {
        $.post("{{ route('show.applicant.profile.education', $user->id) }}", { user_id: {{ $user->id }}, _method: 'POST', _token: '{{ csrf_token() }}' })
            .done(function(response) { $('#education_div').html(response); });
    }
    function showLanguages() {
        $.post("{{ route('show.applicant.profile.languages', $user->id) }}", { user_id: {{ $user->id }}, _method: 'POST', _token: '{{ csrf_token() }}' })
            .done(function(response) { $('#language_div').html(response); });
    }
    function showSkills() {
        $.post("{{ route('show.applicant.profile.skills', $user->id) }}", { user_id: {{ $user->id }}, _method: 'POST', _token: '{{ csrf_token() }}' })
            .done(function(response) { $('#skill_div').html(response); });
    }

    if ($.fn.DataTable && $('#user_datatable_ajax tbody tr').length > 0 && $('#user_datatable_ajax tbody tr td').first().attr('colspan') !== '4') {
        $('#user_datatable_ajax').DataTable({ orderable: false, paging: true, pageLength: 10 });
    }
});
</script>
@endpush
