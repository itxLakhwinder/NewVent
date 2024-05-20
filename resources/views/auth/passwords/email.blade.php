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
            <form method="POST" class="login-form" action="{{ route('password.email') }}">
                @csrf
                <div class="login-wrap">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <p class="login-img"><i class="icon_lock_alt"></i></p>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon_profile"></i></span>
                        <input id="email" placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Send Password Reset Link</button>
                    <!-- <button class="btn btn-info btn-lg btn-block" type="submit">Signup</button> -->
                </div>
            </form>
        </div>
    </div>
</div>
@endsection