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
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <a href="{{ route('list.companies') }}">Companies</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Unlocked Candidates</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR -->
        
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Unlocked Candidates <small>for {{ $company->name }}</small> </h3>
        <!-- END PAGE TITLE-->
        
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"> 
                            <i class="icon-users font-dark"></i> 
                            <span class="caption-subject font-dark sbold uppercase">Unlocked Candidates</span> 
                        </div>
                        <div class="actions">
                            <a href="{{ route('list.companies') }}" class="btn btn-xs btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Companies
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            @if(isset($users) && count($users) > 0)
                                <table id="unlockedCandidatesTable" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Location</th>
                                            <th>Functional Area</th>
                                            <th>Career Level</th>
                                            <th>Experience</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.view.public.profile', $user->id) }}" target="_blank">
                                                        {{ $user->getName() }}
                                                    </a>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                                <td>{{ $user->getCity('city') ?? 'N/A' }}</td>
                                                <td>{{ $user->getFunctionalArea('functional_area') ?? 'N/A' }}</td>
                                                <td>{{ $user->getCareerLevel('career_level') ?? 'N/A' }}</td>
                                                <td>{{ $user->getJobExperience('job_experience') ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.view.public.profile', $user->id) }}" target="_blank" class="btn btn-xs btn-primary">
                                                        <i class="fa fa-eye"></i> View Profile
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    <strong>No Unlocked Candidates Found!</strong>
                                    <p>This company has not unlocked any candidates yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        @if(isset($users) && count($users) > 0)
            $('#unlockedCandidatesTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
            });
        @endif
    });
</script>
@endpush

@endsection

