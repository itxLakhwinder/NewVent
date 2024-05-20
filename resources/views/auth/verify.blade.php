@extends('layouts.admin')

@section('content')

<div class="container">
    <form method="POST" class="login-form" action="{{ route('verification.resend') }}">
        @csrf
        <div class="login-wrap">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif
            <p class="login-img"><i class="icon_lock_alt"></i></p>            
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
            <!-- <button class="btn btn-info btn-lg btn-block" type="submit">Signup</button> -->
        </div>
    </form>
</div>
@endsection
