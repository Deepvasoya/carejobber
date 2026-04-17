@extends('layouts.app')

@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->

<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title' => __('Referral Program')])
<!-- Inner Page Title end -->

<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.user_dashboard_menu')
            <div class="col-md-9 col-sm-8">
                @include('flash::message')

                <!-- Referral Program Header -->
                <div class="referral-header">
                    <div class="referral-header-content">
                        <h2><i class="fas fa-gift"></i> {{__('Referral Program')}}</h2>
                        <p>{{__('Invite friends and get your profile featured!')}}</p>
                    </div>
                </div>

                <!-- Featured Status (from referrals only) -->
                @if($remainingFeaturedDays > 0)
                <div class="featured-status-card active">
                    <div class="featured-icon"><i class="fas fa-star"></i></div>
                    <div class="featured-info">
                        <h4>{{__('Your Profile is Featured!')}}</h4>
                        <p>{{__(':days days remaining', ['days' => $remainingFeaturedDays])}}</p>
                    </div>
                </div>
                @elseif($featuredDays > 0)
                <div class="featured-status-card available">
                    <div class="featured-icon"><i class="fas fa-star"></i></div>
                    <div class="featured-info">
                        <h4>{{__('You have :days featured days available!', ['days' => $featuredDays])}}</h4>
                        <button class="btn btn-activate" onclick="activateFeatured()">
                            <i class="fas fa-bolt"></i> {{__('Activate Now')}}
                        </button>
                    </div>
                </div>
                @endif

                <!-- How It Works -->
                <div class="referral-how-it-works">
                    <h4><i class="fas fa-info-circle"></i> {{__('How It Works')}}</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="step-box">
                                <div class="step-number">1</div>
                                <h5>{{__('Share Your Link')}}</h5>
                                <p>{{__('Share your unique referral link with friends')}}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step-box">
                                <div class="step-number">2</div>
                                <h5>{{__('They Register')}}</h5>
                                <p>{{__('When they register using your link, you get credit')}}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="step-box">
                                <div class="step-number">3</div>
                                <h5>{{__('Get Featured')}}</h5>
                                <p>{{__('Get :days days featured profile for every :count successful referrals', ['days' => $rewardDays, 'count' => $requiredCount])}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row referral-stats">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card stat-total">
                            <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
                            <div class="stat-value">{{ $totalReferrals }}</div>
                            <div class="stat-label">{{__('Total Invites')}}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card stat-success">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-value">{{ $successfulReferrals }}</div>
                            <div class="stat-label">{{__('Successful')}}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card stat-pending">
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                            <div class="stat-value">{{ $pendingReferrals }}</div>
                            <div class="stat-label">{{__('Pending')}}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card stat-bonus">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-value">{{ $featuredDays }}</div>
                            <div class="stat-label">{{__('Featured Days')}}</div>
                        </div>
                    </div>
                </div>

                <!-- Progress to Next Reward -->
                <div class="referral-progress-card">
                    <h4><i class="fas fa-trophy"></i> {{__('Progress to Next Reward')}}</h4>
                    <div class="progress-info">
                        <span>{{ $progressToReward }} / {{ $requiredCount }} {{__('referrals')}}</span>
                        <span>{{ $referralsNeeded }} {{__('more needed')}}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($progressToReward / $requiredCount) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Your Referral Link -->
                <div class="referral-link-card">
                    <h4><i class="fas fa-link"></i> {{__('Your Referral Link')}}</h4>
                    <div class="referral-link-box">
                        <input type="text" id="referralLink" class="form-control" value="{{ $user->getReferralLink() }}" readonly>
                        <button type="button" class="btn btn-primary" onclick="copyReferralLink()">
                            <i class="fas fa-copy"></i> {{__('Copy')}}
                        </button>
                    </div>
                    <p class="referral-code-display">{{__('Your Code')}}: <strong>{{ $user->referral_code }}</strong></p>
                </div>

                <!-- Invite by Email -->
                <div class="referral-invite-card">
                    <h4><i class="fas fa-envelope"></i> {{__('Invite by Email')}}</h4>
                    <form id="inviteForm" class="invite-form">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" id="inviteEmail" class="form-control" placeholder="{{__('Enter email address')}}" required>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> {{__('Send Invite')}}
                            </button>
                        </div>
                    </form>
                    <div id="inviteMessage" class="invite-message"></div>
                </div>

                <!-- Referral History -->
                <div class="referral-history-card">
                    <h4><i class="fas fa-history"></i> {{__('Referral History')}}</h4>
                    <div class="table-responsive">
                        <table class="table table-striped" id="referralHistoryTable">
                            <thead>
                                <tr>
                                    <th>{{__('Invited')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection

@push('styles')
<style>
    .referral-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        color: #fff;
    }

    .referral-header h2 {
        margin: 0 0 10px 0;
        font-size: 28px;
    }

    .referral-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .featured-status-card {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .featured-status-card.active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: #fff;
    }

    .featured-status-card.available {
        border: 2px dashed #38ef7d;
    }

    .featured-icon {
        font-size: 40px;
        margin-right: 20px;
    }

    .featured-status-card.active .featured-icon {
        color: #fff;
    }

    .featured-status-card.available .featured-icon {
        color: #38ef7d;
    }

    .featured-info h4 {
        margin: 0 0 5px 0;
    }

    .featured-info p {
        margin: 0;
        opacity: 0.9;
    }

    .btn-activate {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        margin-top: 10px;
        cursor: pointer;
    }

    .btn-activate:hover {
        opacity: 0.9;
        color: #fff;
    }

    .referral-how-it-works {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .referral-how-it-works h4 {
        margin-bottom: 20px;
        color: #333;
    }

    .step-box {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        height: 100%;
        margin-bottom: 15px;
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        margin: 0 auto 15px;
    }

    .step-box h5 {
        color: #333;
        margin-bottom: 10px;
    }

    .step-box p {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .referral-stats {
        margin-bottom: 25px;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 15px;
    }

    .stat-icon {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .stat-total .stat-icon {
        color: #11998e;
    }

    .stat-success .stat-icon {
        color: #28a745;
    }

    .stat-pending .stat-icon {
        color: #ffc107;
    }

    .stat-bonus .stat-icon {
        color: #f5576c;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #333;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
    }

    .referral-progress-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .referral-progress-card h4 {
        margin-bottom: 15px;
        color: #333;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
        color: #666;
    }

    .progress {
        height: 12px;
        border-radius: 6px;
        background: #e9ecef;
    }

    .progress-bar {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 6px;
    }

    .referral-link-card,
    .referral-invite-card,
    .referral-history-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .referral-link-card h4,
    .referral-invite-card h4,
    .referral-history-card h4 {
        margin-bottom: 20px;
        color: #333;
    }

    .referral-link-box {
        display: flex;
        gap: 10px;
    }

    .referral-link-box input {
        flex: 1;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    .referral-code-display {
        margin-top: 15px;
        color: #666;
        font-size: 14px;
    }

    .referral-code-display strong {
        color: #11998e;
        font-size: 16px;
    }

    .invite-form .input-group {
        display: flex;
        gap: 10px;
    }

    .invite-form input {
        flex: 1;
    }

    .invite-message {
        margin-top: 15px;
        padding: 10px 15px;
        border-radius: 6px;
        display: none;
    }

    .invite-message.success {
        display: block;
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .invite-message.error {
        display: block;
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* DataTable Styling */
    #referralHistoryTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    #referralHistoryTable thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 15px;
        font-weight: 600;
        color: #333;
        text-align: left;
    }

    #referralHistoryTable tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    #referralHistoryTable tbody tr:hover {
        background: #f8f9fa;
    }

    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 12px;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
        color: #666;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 3px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        color: #333 !important;
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #11998e !important;
        color: #fff !important;
        border-color: #11998e;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #11998e !important;
        color: #fff !important;
        border-color: #11998e;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .dataTables_empty {
        text-align: center;
        padding: 30px !important;
        color: #666;
        font-style: italic;
    }

    /* Badge Styling */
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #17a2b8;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }

    .badge-secondary {
        background: #e9ecef;
        color: #495057;
        border: 1px solid #6c757d;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    function copyReferralLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        var btn = event.target.closest('button');
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> {{__("Copied!")}}';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-primary');

        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    }

    function activateFeatured() {
        if (confirm("{{__('Activate your featured profile now?')}}")) {
            $.ajax({
                url: "{{ route('user.activate.featured') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert("{{__('An error occurred. Please try again.')}}");
                }
            });
        }
    }

    $(document).ready(function() {
        $('#referralHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.fetch.referral.history') }}",
            columns: [{
                    data: 'referred_info',
                    name: 'referred_info'
                },
                {
                    data: 'status_badge',
                    name: 'status'
                },
                {
                    data: 'date',
                    name: 'created_at'
                }
            ],
            order: [
                [2, 'desc']
            ],
            language: {
                emptyTable: "{{__('No referrals yet. Start inviting!')}}"
            }
        });

        $('#inviteForm').on('submit', function(e) {
            e.preventDefault();

            var email = $('#inviteEmail').val();
            var btn = $(this).find('button[type="submit"]');
            var originalHtml = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin"></i> {{__("Sending...")}}').prop('disabled', true);

            $.ajax({
                url: "{{ route('user.send.referral.invite') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email: email
                },
                success: function(response) {
                    var msgDiv = $('#inviteMessage');
                    if (response.success) {
                        msgDiv.removeClass('error').addClass('success').text(response.message).show();
                        $('#inviteEmail').val('');
                        $('#referralHistoryTable').DataTable().ajax.reload();
                    } else {
                        msgDiv.removeClass('success').addClass('error').text(response.message).show();
                    }

                    setTimeout(function() {
                        msgDiv.fadeOut();
                    }, 5000);
                },
                error: function() {
                    $('#inviteMessage').removeClass('success').addClass('error')
                        .text("{{__('An error occurred. Please try again.')}}").show();
                },
                complete: function() {
                    btn.html(originalHtml).prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush