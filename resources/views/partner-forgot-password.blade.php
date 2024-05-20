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
    <h3 class="h3_tittle">Forgot Password</h3>
    <form>
       <div class="row">
          <div class="col-12">
             <input type="email" placeholder="Email">
          </div>
       </div>       
       <div class="btn_div">
          <button class="btn_c">Send</button>
       </div>
    </form>
 </div>
</div>
@endsection
