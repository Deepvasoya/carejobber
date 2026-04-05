@extends('layouts.app')

@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->

<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Privacy & Data Settings')])
<!-- Inner Page Title end -->

<div class="listpgWraper">
    <div class="container">
        @include('flash::message')
        <div class="row">
            @include('includes.user_dashboard_menu')
            
            <div class="col-lg-9">
                <div class="userccount">
                    <h3 class="page-title">{{__('Privacy & Data Settings')}}</h3>
                    
                    <div class="privacy-settings-container">

                        <!-- Resume search visibility -->
                        <div class="privacy-card">
                            <div class="privacy-card-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="privacy-card-content">
                                <h4 class="privacy-card-title">{{ __('Employer resume search') }}</h4>
                                <p class="privacy-card-description">{{ __('Choose whether your profile appears when employers search the resume database.') }}</p>
                                <form method="POST" action="{{ route('update.resume.search.visibility') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="visible_in_employer_resume_search" value="0" />
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="visible_in_employer_resume_search" value="1" id="privacy_visible_resume_search"
                                            {{ old('visible_in_employer_resume_search', $user->visible_in_employer_resume_search ?? true) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="privacy_visible_resume_search">{{ __('Show my profile in employer resume search') }}</label>
                                    </div>
                                    <button type="submit" class="btn-privacy-action btn-export">
                                        {{ __('Save preference') }}
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Privacy Consent Card -->
                        <div class="privacy-card">
                            <div class="privacy-card-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="privacy-card-content">
                                <h4 class="privacy-card-title">{{__('Privacy Consent')}}</h4>
                                <p class="privacy-card-description">{{__('Your consent status for data processing and privacy policy.')}}</p>
                                
                                @if($user->gdpr_consent_date)
                                    <div class="privacy-status-badge status-given">
                                        <i class="fas fa-check-circle"></i>
                                        <span>{{__('Consent given on')}} {{Carbon\Carbon::parse($user->gdpr_consent_date)->format('M d, Y H:i')}}</span>
                                    </div>
                                @else
                                    <div class="privacy-status-badge status-pending">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>{{__('Consent not recorded')}}</span>
                                    </div>
                                    <button type="button" class="btn-privacy-action btn-consent" onclick="giveConsent()" style="margin-top: 15px;">
                                        <i class="fas fa-check"></i> {{__('Accept Privacy Policy')}}
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Export Data Card -->
                        <div class="privacy-card">
                            <div class="privacy-card-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="privacy-card-content">
                                <h4 class="privacy-card-title">{{__('Export Your Data')}}</h4>
                                <p class="privacy-card-description">{{__('You have the right to receive a copy of your personal data. Request an export and we will prepare a downloadable file containing all your information.')}}</p>
                                <form method="POST" action="{{route('request.data.export')}}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-privacy-action btn-export">
                                        {{__('Request Data Export')}}
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Delete Account Card -->
                        <div class="privacy-card">
                            <div class="privacy-card-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <div class="privacy-card-content">
                                <h4 class="privacy-card-title">{{__('Delete Your Account')}}</h4>
                                <p class="privacy-card-description">{{__('You have the right to request deletion of your account and all associated data. This action is irreversible.')}}</p>
                                <button type="button" class="btn-privacy-action btn-delete" data-toggle="modal" data-target="#deleteAccountModal">
                                    {{__('Request Account Deletion')}}
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">{{__('Confirm Account Deletion')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('request.account.deletion')}}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>{{__('Warning!')}}</strong> {{__('This action is irreversible. All your data will be permanently deleted.')}}
                    </div>
                    <p>{{__('To confirm, please enter your password:')}}</p>
                    <div class="form-group">
                        <label for="password">{{__('Password')}}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                    <button type="submit" class="btn btn-danger">{{__('Confirm Deletion')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.privacy-settings-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.privacy-card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e8e8e8;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.privacy-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.privacy-card-icon {
    font-size: 48px;
    margin-bottom: 20px;
    color: #667eea;
}

.privacy-card-icon i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.privacy-card-content {
    flex: 1;
    width: 100%;
}

.privacy-card-title {
    font-size: 22px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 10px;
}

.privacy-card-description {
    color: #718096;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.privacy-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.privacy-status-badge.status-given {
    background: #d4edda;
    color: #155724;
}

.privacy-status-badge.status-pending {
    background: #fff3cd;
    color: #856404;
}

.btn-privacy-action {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-export {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    color: #fff;
    text-decoration: none;
}

.btn-delete {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: #fff;
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    color: #fff;
}

@media (max-width: 768px) {
    .privacy-settings-container {
        grid-template-columns: 1fr;
    }
    
    .privacy-card {
        padding: 20px;
    }
}

.btn-consent {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.btn-consent:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    color: #fff;
}
</style>

<script>
function giveConsent() {
    $.ajax({
        url: "{{ route('give.privacy.consent') }}",
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
        error: function(xhr) {
            alert('An error occurred. Please try again.');
        }
    });
}
</script>

@endsection
