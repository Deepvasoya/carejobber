@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end --> 
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>'Reset Password'])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
    <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="useraccountwrap">
                <div class="userccount whitebg">
                    <div class="panel-body mt-5">
                        <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="mb-2 control-label">{{__('Email Address')}}</label>                                
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif                                
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}" style="position: relative;">
                                <label for="password" class="mb-2 control-label">{{__('Password')}}</label>
                                
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    <i class="fas fa-eye" onclick="togglePasswordReset('password', this)" style="position: absolute; right: 15px; top: 42px; cursor: pointer; color: #999; z-index: 10;"></i>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                
                            </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}" style="position: relative;">
                                <label for="password-confirm" class="mb-2 control-label">{{__('Confirm Password')}}</label>                                
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    <i class="fas fa-eye" onclick="togglePasswordReset('password-confirm', this)" style="position: absolute; right: 15px; top: 42px; cursor: pointer; color: #999; z-index: 10;"></i>
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif                                
                            </div>
                            <div class="text-center mt-3">
                                
                                    <button type="submit" class="btn btn-primary">
                                        {{__('Reset Password')}}
                                    </button>
                               
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')

@push('scripts')
<script>
function togglePasswordReset(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush

@endsection