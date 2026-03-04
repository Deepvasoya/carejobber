@extends('admin.layouts.admin_layout')

@section('content')

<style type="text/css">
    .table td, .table th {
        font-size: 13px;
        line-height: 2.42857 !important;
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Applied Users</span> </li>
            </ul>
        </div>
        
        @php
            if(!isset($job) && Request::segment(3)){
                $job = App\Job::findOrFail(Request::segment(3));
            }
        @endphp
        
        <div class="row">
            <div class="col-md-12">
                <div class="myads">
                    <h3>{{__('Candidates listed against')}} ({{isset($job) ? $job->title : ''}})</h3>
                    <button id="downloadCsv" class="btn btn-success">Download CSV</button>
                    <br><br>
                    <table id="appliedUsersTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Candidate Name</th>
                                <th>Location</th>
                                <th>Expected Salary</th>
                                <th>Experience</th>
                                <th>Career Level</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($job_applications) && count($job_applications))
                                @foreach($job_applications as $job_application)
                                    @php
                                        $user = $job_application->getUser();
                                        $job = $job_application->getJob();
                                        $company = $job->getCompany();             
                                        $profileCv = $job_application->getProfileCv();
                                    @endphp
                                    @if($user && $job && $company && $profileCv)
                                        <tr>
                                            <td><a href="{{ route('admin.view.public.profile', $user->id) }}">{{$user->getName()}}</a></td>
                                            <td>{{$user->getLocation()}}</td>
                                            <td>{{$job_application->expected_salary}} {{$job_application->salary_currency}}</td>
                                            <td>{{$user->getJobExperience('job_experience')}}</td>
                                            <td>{{$user->getCareerLevel('career_level')}}</td>
                                            <td>{{$user->phone}}</td>
                                            <td>
                                                @php
                                                    $status = $job_application->status ?: 'applied';
                                                    $statusLabels = [
                                                        'applied' => ['label' => 'Applied', 'class' => 'badge-info'],
                                                        'shortlist' => ['label' => 'Shortlisted', 'class' => 'badge-success'],
                                                        'hired' => ['label' => 'Hired', 'class' => 'badge-primary'],
                                                        'rejected' => ['label' => 'Rejected', 'class' => 'badge-danger']
                                                    ];
                                                    $statusInfo = isset($statusLabels[$status]) ? $statusLabels[$status] : ['label' => ucfirst($status), 'class' => 'badge-default'];
                                                @endphp
                                                <span class="badge {{$statusInfo['class']}}">{{$statusInfo['label']}}</span>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm" href="{{ route('admin.view.public.profile', [$user->id, 'company_id='.$company_id, 'job_id='.$job_id]) }}">View Profile</a>
                                                @if(isset($job) && $job->jobQuestions->count() > 0)
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewAnswersModal{{$job_application->id}}">
                                                    View Answers
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">No Candidates applied yet</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#appliedUsersTable').DataTable();

        $('#downloadCsv').click(function () {
            let csvContent = "data:text/csv;charset=utf-8," + "Name,Location,Expected Salary,Experience,Career Level,Phone,Status\n";
            
            @if(isset($job_applications) && count($job_applications))
                @foreach($job_applications as $job_application)
                    @php
                        $user = $job_application->getUser();
                        $job = $job_application->getJob();
                        $status = $job_application->status ?: 'applied';
                        $statusLabels = [
                            'applied' => 'Applied',
                            'shortlist' => 'Shortlisted',
                            'hired' => 'Hired',
                            'rejected' => 'Rejected'
                        ];
                        $statusLabel = isset($statusLabels[$status]) ? $statusLabels[$status] : ucfirst($status);
                    @endphp
                    @if($user && $job)
                        csvContent += `"{{$user->getName()}}","{{$user->getLocation()}}","{{$job_application->expected_salary}} {{$job_application->salary_currency}}","{{$user->getJobExperience('job_experience')}}","{{$user->getCareerLevel('career_level')}}","{{$user->phone}}","{{$statusLabel}}"\n`;
                    @endif
                @endforeach
            @endif

            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "applied_users_{{$job->title}}.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>

<!-- Modals for View Answers -->
@if(isset($job_applications) && count($job_applications) && isset($job) && $job->jobQuestions->count() > 0)
    @foreach($job_applications as $job_application)
        @php
            $user = $job_application->getUser();
        @endphp
        @if(null !== $job_application && null !== $user)
        <div class="modal fade" id="viewAnswersModal{{$job_application->id}}" tabindex="-1" role="dialog" aria-labelledby="viewAnswersModalLabel{{$job_application->id}}">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="viewAnswersModalLabel{{$job_application->id}}">{{__('Answers for')}} {{$user->getName()}}</h4>
                    </div>
                    <div class="modal-body">
                        @php
                            $answers = \App\JobQuestionAnswer::where('job_apply_id', $job_application->id)
                                ->with('jobQuestion')
                                ->get()
                                ->keyBy('job_question_id');
                        @endphp
                        @foreach($job->jobQuestions as $question)
                            <div class="mb-4">
                                <h5><strong>{{$question->question_title}}</strong></h5>
                                <div class="answer-box p-3" style="background-color: #f5f5f5; border-radius: 4px;">
                                    @if(isset($answers[$question->id]))
                                        <p class="mb-0">{{$answers[$question->id]->answer ?: __('No answer provided')}}</p>
                                    @else
                                        <p class="mb-0 text-muted">{{__('No answer provided')}}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif
@endsection
