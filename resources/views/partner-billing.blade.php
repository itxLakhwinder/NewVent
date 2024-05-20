@extends('layouts.partner')

@section('title', ' - Billing')

@section('content')
<?php
    function getTruncatedCCNumber($ccNum){
        return str_replace(range(0,9), "*", substr($ccNum, 0, -4)) .  substr($ccNum, -4);
    }
?>
<div class="main_section">
          @include('partner-sidebar') 
         <div class="right_section">
            <div class="common_section_right">
               @include('partner-nav')     
               <div class="">
                  <h4 class="h4_tittle">Billing</h4>
                  <!-- <p>No need to add any payment information. The next 90 days on the VentSpace App platform are on us.</p> -->
                  <p>No need to add any payment information. Your profile is free once approved by our team.</p>
               </div>
               @if(empty($partner->customer_id) && $user->status == 0) 
                  <!--   <div class="alert alert-warning">
                        Your account is'nt approved so please add your card details.
                        <a href="/partner-billing">Add Billing</a>
                    </div> -->
               @endif
               <div class="billing_text mt-4">
                @if (session('success'))
                  <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                  </div>
                @endif
                @if (session('error'))
                  <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
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
                  <!--  <form method="POST" class="login-form" action="{{ url('/partner-billing') }}">
                    @csrf
                     <div class="row">
                        <div class="col-12">
                           <input type="text" placeholder="Name on credit card" class=" @error('name_on_card') is-invalid @enderror" name="name_on_card" value="{{ $partner ? $partner->name_on_card : old('name_on_card') }}" required>
                           @error('name_on_card')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                           <input type="tel" inputmode="numeric" placeholder="Credit card number" class=" @error('card_number') is-invalid @enderror" name="card_number" value="{{ $partner ? getTruncatedCCNumber($partner->card_number) : old('card_number') }}" required pattern="[0-9\s]{13,19}" maxlength="19" title="Enter valid card number">

                           @error('card_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                           <select class=" @error('exp_month') is-invalid @enderror" name="exp_month" id="exp_month" value="{{ $partner ? $partner->exp_month : old('exp_month') }}" required>
                                <option value="">Month</option>
                                <?php
                                  for ($i=1; $i < 13 ; $i++) { 
                                    print '<option value="'.$i.'">'.$i.'</option>';
                                  }                        
                                ?>
                            </select> 
                           @error('exp_month')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <select class=" @error('exp_year') is-invalid @enderror" name="exp_year" id="exp_year" value="{{ $partner ? $partner->exp_year : old('exp_year') }}" required>
                                <option value="">Year</option>
                                <?php
                                  $already_selected_value = date('Y') + 6;
                                  $earliest_year = date('Y');
                                  for ($i=$earliest_year; $i < $already_selected_value ; $i++) { 
                                    print '<option value="'.$i.'">'.$i.'</option>';
                                  } 
                                
                                                             
                                ?>
                            </select>
                            @error('exp_year')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12">
                           <input type="password" inputmode="numeric" placeholder="CVV" class="@error('cvv') is-invalid @enderror" name="cvv" required style="margin-bottom: 2px;">
                           @error('cvv')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                     </div>
                     <div>
                        <button class="btn_c">Save</button>
                     </div>
                  </form> -->
               </div>
            </div>
         </div>
      </div>
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        const exp_year = $('#exp_year').attr('value');
        $('#exp_year').val(exp_year)
        
        const exp_month = $('#exp_month').attr('value');
        $('#exp_month').val(exp_month)

    });
</script>
@endsection
@endsection
