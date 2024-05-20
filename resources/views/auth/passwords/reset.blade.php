@extends('layouts.partner')
@section('content')
<div class="vent_space_header">
    <div class="container">
        <div class="brand_logo">
            <a href="#"><img src="{{asset('partner_assets/images/logo.png')}}"></a>
        </div>
    </div>
</div>
<div class="form_section">
    <div class="sign_up login_inputs">
        <div class="container">
            <form method="POST" class="login-form" action="{{ route('password.update') }}">
                @csrf
                <div class="login-wrap">
                    <p class="login-img"><i class="icon_lock_alt"></i></p>
                    <div class="input-group">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <span class="input-group-addon"><i class="icon_profile"></i></span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="E-Mail Address" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon_key_alt"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon_key_alt"></i></span>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                    </div>
                    
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Reset Password</button>
                    <!-- <button class="btn btn-info btn-lg btn-block" type="submit">Signup</button> -->
                </div>
            </form>
        </div>
    </div>
</div>
@endsection