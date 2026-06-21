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
                                        <td>{{$request->admin_notes ?? 'N/A'}}</td>
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
    let currentRejectId = null;

    function approveClaimRequest(id) {
        if (!confirm('Are you sure you want to approve this claim request?')) {
            return;
        }

        $.ajax({
            url: '{{route("admin.approve.claim.request", ":id")}}'.replace(':id', id),
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function(response) {
                if (response.success) {
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
    }

    function rejectClaimRequest(id) {
        currentRejectId = id;
        $('#reject-admin-notes').val('');
        $('#rejectModal').modal('show');
    }

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
