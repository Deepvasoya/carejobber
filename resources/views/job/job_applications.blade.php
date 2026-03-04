@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end --> 
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Kanban Board to manage applied jobseekers')])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
    @php
                $jobTitle = ''; // Initialize job title variable

                // Check if the job ID is present in the URL
                $jobIdFromUrl = request()->segment(2); // Assuming the job ID is the second segment in the URL

                if ($jobIdFromUrl) {
                    // Fetch the job title based on the job ID
                    $job = \App\Job::find($jobIdFromUrl);

                    if ($job) {
                        $jobTitle = $job->title;
                    }
                }
            @endphp


        

            <h3>{{__('Applications Showing for the job')}}: {{ $jobTitle }}</h3>




            <div class="kanban-board mt-3">
            <div class="column" id="applied">
                <h2>Applied Users</h2>
                @if(isset($job_applications) && count($job_applications))
                @foreach($job_applications as $job_application)
                @if($job_application->status=='applied')
                @php
                $user = $job_application->getUser();
                $job = $job_application->getJob();
                $company = $job->getCompany();             
                $profileCv = $job_application->getProfileCv();
                @endphp
                @if(null !== $job_application && null !== $user && null !== $job && null !== $company && null !== $profileCv)
                <div class="task" draggable="true" id="task{{$job_application->id}}">                   
                        
                            <div class="jobinfo">
                                <h3>{{$user->getName()}}</h3>
                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                            </div>                                                  
                            <div class="minsalary"><i class="fas fa-money-bill"></i>
                                {{$job_application->expected_salary}} {{$job_application->salary_currency}} 
                                <span>/ {{$job->getSalaryPeriod('salary_period')}}</span>
                            </div>
                        
                            
                            <div class="d-flex justify-content-end jobskrbtnact">
                            <a class="me-auto profbtn" href="{{route('applicant.profile', $job_application->id)}}" target="_blank">{{__('View Profile')}}</a>
                            <button class="btn profbtn me-1 start-chat-btn" data-user-id="{{$user->id}}" data-user-name="{{$user->getName()}}" title="{{__('Start Chat')}}">
                                <i class="fas fa-comments"></i>
                            </button>
                            @if($job->jobQuestions->count() > 0)
                            <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewAnswersModal{{$job_application->id}}">
                                {{__('View Answers')}}
                            </button>
                            @endif
                            <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                            <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                            </div>                  
                </div>
                <!-- job end --> 
                @endif
                @endif
                @endforeach
                @else
                <div class="nodatabox">
                        <h4>{{__('No Record Found')}}</h4>
                        <div class="viewallbtn mt-2"><a href="{{url('/jobs')}}">{{__('Search Jobs')}}</a></div>
                    </div>
                @endif



            </div>

            <div class="column" id="rejected">
                <h2>Rejected</h2>
                @if(isset($job_applications) && count($job_applications))
                @foreach($job_applications as $job_application)
                @if($job_application->status=='rejected')
                @php
                $user = $job_application->getUser();
                $job = $job_application->getJob();
                $company = $job->getCompany();             
                $profileCv = $job_application->getProfileCv();
                @endphp
                @if(null !== $job_application && null !== $user && null !== $job && null !== $company && null !== $profileCv)
                <div class="task" draggable="true" id="task{{$job_application->id}}">                   
                        
                            <div class="jobinfo">
                                <h3>{{$user->getName()}}</h3>
                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                            </div>                                                  
                            <div class="minsalary"><i class="fas fa-money-bill"></i>
                                {{$job_application->expected_salary}} {{$job_application->salary_currency}} 
                                <span>/ {{$job->getSalaryPeriod('salary_period')}}</span>
                            </div>
                        
                            
                            <div class="d-flex justify-content-end jobskrbtnact">
                            <a class="me-auto profbtn" href="{{route('applicant.profile', $job_application->id)}}" target="_blank">{{__('View Profile')}}</a>
                            <button class="btn profbtn me-1 start-chat-btn" data-user-id="{{$user->id}}" data-user-name="{{$user->getName()}}" title="{{__('Start Chat')}}">
                                <i class="fas fa-comments"></i>
                            </button>


                            @if($job->jobQuestions->count() > 0)
                            <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewAnswersModal{{$job_application->id}}">
                                {{__('View Answers')}}
                            </button>
                            @endif
                            <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                            <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                            </div>                  
                </div>
                <!-- job end --> 
                @endif
                @endif
                @endforeach
                @else
                <div class="nodatabox">
                        <h4>{{__('No Record Found')}}</h4>
                        <div class="viewallbtn mt-2"><a href="{{url('/jobs')}}">{{__('Search Jobs')}}</a></div>
                    </div>
                @endif
            </div>

            <div class="column" id="shortlist">
                <h2>Shortlisted</h2>
                @if(isset($job_applications) && count($job_applications))
                @foreach($job_applications as $job_application)
                @if($job_application->status=='shortlist')
                @php
                $user = $job_application->getUser();
                $job = $job_application->getJob();
                $company = $job->getCompany();             
                $profileCv = $job_application->getProfileCv();
                @endphp
                @if(null !== $job_application && null !== $user && null !== $job && null !== $company && null !== $profileCv)
                <div class="task" draggable="true" id="task{{$job_application->id}}">                   
                        
                            <div class="jobinfo">
                                <h3>{{$user->getName()}}</h3>
                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                            </div>                                                  
                            <div class="minsalary"><i class="fas fa-money-bill"></i>
                                {{$job_application->expected_salary}} {{$job_application->salary_currency}} 
                                <span>/ {{$job->getSalaryPeriod('salary_period')}}</span>
                            </div>
                        
                            
                            <div class="d-flex justify-content-end jobskrbtnact">
                            <a class="me-auto profbtn" href="{{route('applicant.profile', $job_application->id)}}" target="_blank">{{__('View Profile')}}</a>
                            <button class="btn profbtn me-1 start-chat-btn" data-user-id="{{$user->id}}" data-user-name="{{$user->getName()}}" title="{{__('Start Chat')}}">
                                <i class="fas fa-comments"></i>
                            </button>
                            @if($job->jobQuestions->count() > 0)
                            <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewAnswersModal{{$job_application->id}}">
                                {{__('View Answers')}}
                            </button>
                            @endif
                            <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                            <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                            </div>                  
                </div>
                <!-- job end --> 
                @endif
                @endif
                @endforeach
                @else
                <div class="nodatabox">
                        <h4>{{__('No Record Found')}}</h4>
                        <div class="viewallbtn mt-2"><a href="{{url('/jobs')}}">{{__('Search Jobs')}}</a></div>
                    </div>
                @endif
            </div>

            <div class="column" id="hired">
                <h2>Hired</h2>
                @if(isset($job_applications) && count($job_applications))
                @foreach($job_applications as $job_application)
                @if($job_application->status=='hired')
                @php
                $user = $job_application->getUser();
                $job = $job_application->getJob();
                $company = $job->getCompany();             
                $profileCv = $job_application->getProfileCv();
                @endphp
                @if(null !== $job_application && null !== $user && null !== $job && null !== $company && null !== $profileCv)
                <div class="task" draggable="true" id="task{{$job_application->id}}">                   
                        
                            <div class="jobinfo">
                                <h3>{{$user->getName()}}</h3>
                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                            </div>                                                  
                            <div class="minsalary"><i class="fas fa-money-bill"></i>
                                {{$job_application->expected_salary}} {{$job_application->salary_currency}} 
                                <span>/ {{$job->getSalaryPeriod('salary_period')}}</span>
                            </div>
                        
                            
                            <div class="d-flex justify-content-end jobskrbtnact">
                            <a class="me-auto profbtn" href="{{route('applicant.profile', $job_application->id)}}" target="_blank">{{__('View Profile')}}</a>
                            <button class="btn profbtn me-1 start-chat-btn" data-user-id="{{$user->id}}" data-user-name="{{$user->getName()}}" title="{{__('Start Chat')}}">
                                <i class="fas fa-comments"></i>
                            </button>
                            @if($job->jobQuestions->count() > 0)
                            <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#viewAnswersModal{{$job_application->id}}">
                                {{__('View Answers')}}
                            </button>
                            @endif
                            <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                            <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                            </div>                  
                </div>
                <!-- job end --> 
                @endif
                @endif
                @endforeach
                @else
                <div class="nodatabox">
                        <h4>{{__('No Record Found')}}</h4>
                        <div class="viewallbtn mt-2"><a href="{{url('/jobs')}}">{{__('Search Jobs')}}</a></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modals for View Answers -->
        @if(isset($job_applications) && count($job_applications))
            @foreach($job_applications as $job_application)
                @php
                    $user = $job_application->getUser();
                    $job = $job_application->getJob();
                @endphp
                @if(null !== $job_application && null !== $user && null !== $job && $job->jobQuestions->count() > 0)
                <div class="modal fade" id="viewAnswersModal{{$job_application->id}}" tabindex="-1" aria-labelledby="viewAnswersModalLabel{{$job_application->id}}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewAnswersModalLabel{{$job_application->id}}">{{__('Answers from')}} {{$user->getName()}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <h6 class="fw-bold">{{$question->question_title}}</h6>
                                        <div class="answer-box p-3 bg-light rounded">
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        @endif

           
    </div>
</div>
@include('includes.footer')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	const drake = dragula({
	  isContainer: el => el.classList.contains('column'),
	});
  
	drake.on('drop', (el, target, source) => {
	  handleDrop(el, target);
	});
  
	drake.on('drag', (el, source) => {
	  el.classList.add('dragging');
	});
  });
  
  function handleDrop(el, target) {
	el.classList.remove('dragging');
  
	const dataId = el.id.replace("task", "");
	const targetId = target.id;
  
	updateServer(targetId, dataId);
  }
  
  function updateServer(columnType, dataId) {
	const applied = getNumericIds('applied');
	const shortlist = getNumericIds('shortlist');
	const hired = getNumericIds('hired');
	const rejected = getNumericIds('rejected');
  
	let columnData;
	switch (columnType) {
	  case 'applied':
		columnData = applied;
		break;
	  case 'shortlist':
		columnData = shortlist;
		break;
	  case 'hired':
		columnData = hired;
		break;
	  case 'rejected':
		columnData = rejected;
		break;
	  default:
		columnData = [];
	}

  
	$.ajax({
	  url: '{{route("applicants.setStatus")}}',
	  type: 'GET',
	  data: {
		applied: JSON.stringify(applied),
		shortlist: JSON.stringify(shortlist),
		hired: JSON.stringify(hired),
		rejected: JSON.stringify(rejected),
		columnType: columnType,
		dataId: dataId
	  },
	  dataType: 'json',
	  success: function (data) {
	  },
	  error: function (xhr, status, error) {
	  }
	});


  }
  
  function getNumericIds(divid) {
	var idArray = [];
  
	$("#" + divid + " .task").each(function () {
	  var id = $(this).attr("id");
	  if (typeof id !== 'undefined') {
		var numericId = parseInt(id.replace("task", ""));
		idArray.push(numericId);
	  }
	});
  
	return idArray;
  }
  
  function moveTask(button, direction) {
	const taskContainer = $(button).closest('.task');
	const currentColumn = taskContainer.closest('.column');
	let targetColumn;
  
	if (direction === 'forward') {
	  targetColumn = currentColumn.next();
	} else if (direction === 'backward') {
	  targetColumn = currentColumn.prev();
	}
  
	if (targetColumn.length > 0) {
	  targetColumn.append(taskContainer);
	  const targetColumnType = targetColumn.attr('id');
	  const dataId = taskContainer.attr('id').replace("task", "");
	  updateServer(targetColumnType, dataId);
	}
  
	taskContainer.removeClass('dragging');
  }
</script>



<script>
    $('#downloadCsv').click(function () {
    let jobId = $(this).data('job-id');
    
    $.ajax({
        url: '/download-csv/' + jobId,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            window.location.href = response.file_url; // Redirect to download
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseText);
        }
    });
});

</script>

  @endpush
<script>
  // Handle Start Chat button clicks - Navigate to full-page chat
  document.addEventListener('click', function(e) {
    if (e.target.closest('.start-chat-btn')) {
      e.preventDefault();
      const btn = e.target.closest('.start-chat-btn');
      const userId = btn.getAttribute('data-user-id');
      const baseUrl = window.CHAT_BASE_URL || '';
      
      // Start conversation
      fetch(baseUrl + '/chat/start/' + userId, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const conversationId = data.data.conversation_id;
          // Navigate to full-page chat with conversation ID
          window.location.href = baseUrl + '/chat?conversation=' + conversationId;
        } else {
          alert(data.message || 'Failed to start conversation');
        }
      })
      .catch(error => {
        console.error('Error starting chat:', error);
        alert('Failed to start conversation. Please try again.');
      });
    }
  });
</script>


@endsection