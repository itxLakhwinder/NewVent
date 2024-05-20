@extends('layouts.partner')

@section('title', ' - Therapist Sign In')

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
    <h3 class="h3_tittle">Login</h3>
    @if (session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
      </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
    @endif
    <form method="POST" class="login-form" action="{{ url('/partner-login') }}">
        @csrf
       <div class="row">
          <div class="col-12">
            <input id="email" type="email" placeholder="Email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="col-12">
            <input id="password" type="password" placeholder="Password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
       </div>
       <div class="frget_password">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot Password?') }}
                </a>
            @endif
       </div>
       <div class="btn_div">
          <button class="btn_c">Login</button>
       </div>
       <div class="aleardy_account">
		   {{--<a href="{{url('/partner-register')}}">Become a Partner</a></p>--}}
          <p>Don't have an account?<a href="#">Become a Partner</a></p>
       </div>
    </form>
 </div>
</div>

@endsection
