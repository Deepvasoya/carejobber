@extends('layouts.app')

@section('content') 

<!-- Header start --> 

@include('includes.header') 

<!-- Header end --> 

<!-- Inner Page Title start --> 

@include('includes.inner_page_title', ['page_title'=>__('Unlocked Seekers')]) 

<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container">

       

                <div class="userskanban">

                    <div class="kanban-board mt-3">
                        <div class="column" id="unlocked">
                            <h2>Unlocked Users</h2>
                            @if(isset($unlocked_user_statuses) && count($unlocked_user_statuses))
                                @foreach($unlocked_user_statuses as $status)
                                    @if($status->status == 'unlocked' && $status->user)
                                        @php
                                            $user = $status->user;
                                        @endphp
                                        <div class="task" draggable="true" id="task{{$user->id}}">                   
                                            <div class="jobinfo">
                                                <h3>{{$user->getName()}}</h3>
                                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                                            </div>                                                  
                                            <div class="minsalary">
                                                <i class="fas fa-briefcase"></i> {{$user->getFunctionalArea('functional_area') ?? 'N/A'}}
                                            </div>
                                            <div class="minsalary">
                                                <i class="fas fa-chart-line"></i> {{$user->getCareerLevel('career_level') ?? 'N/A'}}
                                            </div>
                                            
                                            <div class="d-flex justify-content-end jobskrbtnact">
                                                <a class="me-auto profbtn" href="{{route('user.profile', $user->id)}}" target="_blank">{{__('View Profile')}}</a>
                                                <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                                                <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                                            </div>                  
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="nodatabox">
                                    <h4>{{__('No Unlocked Seekers Found')}}</h4>
                                    <div class="viewallbtn mt-2"><a href="{{url('/job-seekers')}}">{{__('Search Candidates')}}</a></div>
                                </div>
                            @endif
                        </div>

                        <div class="column" id="rejected">
                            <h2>Rejected</h2>
                            @if(isset($unlocked_user_statuses) && count($unlocked_user_statuses))
                                @foreach($unlocked_user_statuses as $status)
                                    @if($status->status == 'rejected' && $status->user)
                                        @php
                                            $user = $status->user;
                                        @endphp
                                        <div class="task" draggable="true" id="task{{$user->id}}">                   
                                            <div class="jobinfo">
                                                <h3>{{$user->getName()}}</h3>
                                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                                            </div>                                                  
                                            <div class="minsalary">
                                                <i class="fas fa-briefcase"></i> {{$user->getFunctionalArea('functional_area') ?? 'N/A'}}
                                            </div>
                                            <div class="minsalary">
                                                <i class="fas fa-chart-line"></i> {{$user->getCareerLevel('career_level') ?? 'N/A'}}
                                            </div>
                                            
                                            <div class="d-flex justify-content-end jobskrbtnact">
                                                <a class="me-auto profbtn" href="{{route('user.profile', $user->id)}}" target="_blank">{{__('View Profile')}}</a>
                                                <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                                                <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                                            </div>                  
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="column" id="shortlist">
                            <h2>Shortlisted</h2>
                            @if(isset($unlocked_user_statuses) && count($unlocked_user_statuses))
                                @foreach($unlocked_user_statuses as $status)
                                    @if($status->status == 'shortlist' && $status->user)
                                        @php
                                            $user = $status->user;
                                        @endphp
                                        <div class="task" draggable="true" id="task{{$user->id}}">                   
                                            <div class="jobinfo">
                                                <h3>{{$user->getName()}}</h3>
                                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                                            </div>                                                  
                                            <div class="minsalary">
                                                <i class="fas fa-briefcase"></i> {{$user->getFunctionalArea('functional_area') ?? 'N/A'}}
                                            </div>
                                            <div class="minsalary">
                                                <i class="fas fa-chart-line"></i> {{$user->getCareerLevel('career_level') ?? 'N/A'}}
                                            </div>
                                            
                                            <div class="d-flex justify-content-end jobskrbtnact">
                                                <a class="me-auto profbtn" href="{{route('user.profile', $user->id)}}" target="_blank">{{__('View Profile')}}</a>
                                                <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                                                <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                                            </div>                  
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="column" id="hired">
                            <h2>Hired</h2>
                            @if(isset($unlocked_user_statuses) && count($unlocked_user_statuses))
                                @foreach($unlocked_user_statuses as $status)
                                    @if($status->status == 'hired' && $status->user)
                                        @php
                                            $user = $status->user;
                                        @endphp
                                        <div class="task" draggable="true" id="task{{$user->id}}">                   
                                            <div class="jobinfo">
                                                <h3>{{$user->getName()}}</h3>
                                                <div class="location d-flex mb-2"><i class="fas fa-map-marker-alt me-1"></i> {{$user->getLocation()}}</div>
                                            </div>                                                  
                                            <div class="minsalary">
                                                <i class="fas fa-briefcase"></i> {{$user->getFunctionalArea('functional_area') ?? 'N/A'}}
                                            </div>
                                            <div class="minsalary">
                                                <i class="fas fa-chart-line"></i> {{$user->getCareerLevel('career_level') ?? 'N/A'}}
                                            </div>
                                            
                                            <div class="d-flex justify-content-end jobskrbtnact">
                                                <a class="me-auto profbtn" href="{{route('user.profile', $user->id)}}" target="_blank">{{__('View Profile')}}</a>
                                                <button class="move-btn backward" onclick="moveTask(this, 'backward')"><i class="fas fa-reply"></i></button>
                                                <button class="move-btn forward ms-1" onclick="moveTask(this, 'forward')"><i class="fas fa-share"></i></button>
                                            </div>                  
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

          

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
	const unlocked = getNumericIds('unlocked');
	const shortlist = getNumericIds('shortlist');
	const hired = getNumericIds('hired');
	const rejected = getNumericIds('rejected');
  
	let columnData;
	switch (columnType) {
	  case 'unlocked':
		columnData = unlocked;
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
	  url: '{{route("unlocked.users.setStatus")}}',
	  type: 'GET',
	  data: {
		unlocked: JSON.stringify(unlocked),
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
@endpush

@endsection
