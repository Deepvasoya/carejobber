@extends('admin.layouts.admin_layout')

@section('content')
<style type="text/css">
    .table td, .table th {
        font-size: 12px;
        line-height: 2.42857 !important;
    }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Company Claim Requests</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
                    <li class="breadcrumb-item active">Company Claim Requests</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Pending Requests -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pending Claim Requests</h3>
                    </div>
                    <div class="card-body">
                        @if($pendingRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Company Name</th>
                                        <th>Claimant Name</th>
                                        <th>Work Email</th>
                                        <th>Job Title</th>
                                        <th>Message</th>
                                        <th>Requested Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                    <tr id="claim-request-row-{{$request->id}}">
                                        <td>{{$request->id}}</td>
                                        <td>
                                            <a href="{{route('edit.company', $request->company_id)}}" target="_blank">
                                                {{$request->company->name ?? 'N/A'}}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{$request->claimant_name ?? $request->user->name ?? 'N/A'}}</strong>
                                            @if($request->user && $request->user->name && !$request->claimant_name)
                                                <br><small class="text-muted">(from account: {{$request->user->name}})</small>
                                            @endif
                                        </td>
                                        <td>{{$request->claimant_email ?? $request->user->email ?? 'N/A'}}</td>
                                        <td>{{$request->claimant_job_title ?? 'N/A'}}</td>
                                        <td>{{$request->message ?? 'No message'}}</td>
                                        <td>{{$request->requested_at ? $request->requested_at->format('M d, Y h:i A') : 'N/A'}}</td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="approveClaimRequest({{$request->id}})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="rejectClaimRequest({{$request->id}})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                            <button class="btn btn-info btn-sm" onclick="editClaimNotes({{$request->id}})">
                                                <i class="fas fa-sticky-note"></i> Notes
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-muted">No pending claim requests.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recently Reviewed -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recently Reviewed Requests</h3>
                    </div>
                    <div class="card-body">
                        @if($recentReviewed->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Company Name</th>
                                        <th>Claimant</th>
                                        <th>Work Email</th>
                                        <th>Job Title</th>
                                        <th>Status</th>
                                        <th>Admin Notes</th>
                                        <th>Reviewed Date</th>
                                        <th>Reviewed By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReviewed as $request)
                                    <tr>
                                        <td>{{$request->id}}</td>
                                        <td>
                                            <a href="{{route('edit.company', $request->company_id)}}" target="_blank">
                                                {{$request->company->name ?? 'N/A'}}
                                            </a>
                                        </td>
                                        <td>{{$request->claimant_name ?? $request->user->name ?? 'N/A'}}</td>
                                        <td>{{$request->claimant_email ?? $request->user->email ?? 'N/A'}}</td>
                                        <td>{{$request->claimant_job_title ?? 'N/A'}}</td>
                                        <td>
                                            @if($request->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                            @else
                                            <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{$request->admin_notes ?? 'N/A'}}
                                            <button class="btn btn-info btn-xs float-right" onclick="editClaimNotes({{$request->id}})" title="Edit notes">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                        <td>{{$request->reviewed_at ? $request->reviewed_at->format('M d, Y h:i A') : 'N/A'}}</td>
                                        <td>{{$request->reviewer->name ?? 'N/A'}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-muted">No reviewed requests yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="notes-text" rows="5" placeholder="Add your notes here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmNotesBtn">Save Notes</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Claim Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Admin Notes (Optional)</label>
                    <textarea class="form-control" id="approve-admin-notes" rows="3" placeholder="Any notes about this approval..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApproveBtn">Approve Request</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Claim Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Admin Notes (Optional)</label>
                    <textarea class="form-control" id="reject-admin-notes" rows="3" placeholder="Reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">Reject Request</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentApproveId = null;
    let currentRejectId = null;
    let currentNotesId = null;

    function approveClaimRequest(id) {
        currentApproveId = id;
        $('#approve-admin-notes').val('');
        $('#approveModal').modal('show');
    }

    $('#confirmApproveBtn').click(function() {
        if (currentApproveId === null) return;

        const adminNotes = $('#approve-admin-notes').val();

        $.ajax({
            url: '{{route("admin.approve.claim.request", ":id")}}'.replace(':id', currentApproveId),
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                admin_notes: adminNotes
            },
            success: function(response) {
                if (response.success) {
                    $('#approveModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });

    function rejectClaimRequest(id) {
        currentRejectId = id;
        $('#reject-admin-notes').val('');
        $('#rejectModal').modal('show');
    }

    function editClaimNotes(id) {
        currentNotesId = id;
        $('#notes-text').val('');

        $.ajax({
            url: '{{route("admin.company.claim.requests")}}',
            type: 'GET',
            dataType: 'json',
            data: { get_notes: id },
            success: function(response) {
                if (response.notes) {
                    $('#notes-text').val(response.notes);
                }
                $('#notesModal').modal('show');
            },
            error: function() {
                $('#notesModal').modal('show');
            }
        });
    }

    $('#confirmNotesBtn').click(function() {
        if (currentNotesId === null) return;

        const notes = $('#notes-text').val();

        $.ajax({
            url: '{{route("admin.save.claim.notes", ":id")}}'.replace(':id', currentNotesId),
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                admin_notes: notes
            },
            success: function(response) {
                if (response.success) {
                    $('#notesModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });

    $('#confirmRejectBtn').click(function() {
        if (currentRejectId === null) return;

        const adminNotes = $('#reject-admin-notes').val();

        $.ajax({
            url: '{{route("admin.reject.claim.request", ":id")}}'.replace(':id', currentRejectId),
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                admin_notes: adminNotes
            },
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });
</script>
@endpush
