@include('emails.header')
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;"><h4>Hi, {{@$user->first_name}}</h4></p>
<p style="color:#3d4852;font-size: 16px;line-height:1.5em;">Your OTP for reset password: <b>{{@$otp}}</b> </p>
@include('emails.footer')