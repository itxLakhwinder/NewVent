@extends('layouts.partner')

@section('title', ' - Account')

@section('content')
<div class="main_section">
          @include('partner-sidebar') 
         <div class="right_section">
            <div class="common_section_right">
               @include('partner-nav')     
               <div class="">
                  <h4 class="h4_tittle">Account</h4>
               </div>
               <div class="billing_text mt-4">
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
                   <form method="POST" class="login-form" action="{{ url('/partner-account') }}">
                    @csrf
                     <div class="row">
                        <div class="col-12">
                           <input type="text" placeholder="First Name" class="@error('first_name') is-invalid @enderror" name="first_name" value="{{ $partner ? $partner->first_name : old('first_name') }}" required>
                           @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                           <input type="text" placeholder="Last Name" class="@error('last_name') is-invalid @enderror" name="last_name" value="{{ $partner ? $partner->last_name : old('last_name') }}" required>
                           @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                           <input type="email" placeholder="Email" class="@error('email') is-invalid @enderror" readonly name="email" value="{{ $user ? $user->email : old('email') }}" required>
                           @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- <small>If you want to update Password then use Current Password and New Password</small> -->
                        <div class="col-md-6">
                           <input type="password" placeholder="Current Password" class="@error('password') is-invalid @enderror" name="password">
                           @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                           <input type="password" minlength="8" placeholder="New Password" class="@error('new_password') is-invalid @enderror" name="new_password">
                           @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>                       
                        
                     </div>
                     <div>
                        <button class="btn_c">Save</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
<!-- <div class="container">
    <form method="POST" class="login-form" action="{{ route('register') }}">
        @csrf
        <div class="login-wrap">
            <div class="form-group row">
                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                <div class="col-md-6">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div> -->
@endsection
