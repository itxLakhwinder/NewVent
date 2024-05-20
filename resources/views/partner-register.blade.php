@extends('layouts.partner')

@section('title', ' - Therapist Sign Up')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
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
            <h3 class="h3_tittle">Become a Partner</h3>
            <p>VentSpace app has thousands of active, anonymous users that are searching for mental health services that fit their needs. Join today and become part of our in-app marketplace to access thousands of real people needing your mental health services.</p>
            
            @if(session()->has('error'))
                <div class="alert alert-warning">
                    {{ session()->get('error') }}
                </div>
            @endif
            <!-- @if (session('success'))
              <div class="alert alert-success" role="alert">
                {{ session('success') }}
              </div>
            @endif -->
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
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
            <form method="POST" class="login-form" action="{{ url('/partner-register') }}">
                @csrf
               <div class="row">
                  <div class="col-6 col-12">
                    <input type="text" placeholder="First Name" class="@error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>                    
                  </div>
                     <div class="col-6 col-12">
                    <input type="text" placeholder="Last Name" class="@error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>                    
                  </div>
                    <div class="col-6 col-12">
                    <input type="text" placeholder="Company Name" class="@error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" required autocomplete="name" autofocus>                    
                  </div>
                     <div class="col-6 col-12">
                    <input type="text" placeholder="Company Website" class="@error('company_name') is-invalid @enderror" name="company_website" value="{{ old('company_website') }}" required autocomplete="name" autofocus>                    
                  </div>
                  <div class="col-md-4 col-12">
                    <input type="text" placeholder="City" class="@error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required>                   
                  </div>
                  <div class="col-md-4 col-12">
                     <select class="@error('state') is-invalid @enderror" name="state" id="state" data-value="{{ old('state') }}" required>
                        <option value="">State</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District Of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                     </select>                   
                  </div>
                  <div class="col-md-4 col-12">
                    <input type="text" placeholder="Zip Code" class="@error('zip_code') is-invalid @enderror" name="zip_code" value="{{ old('zip_code') }}" required>                   
                  </div>
                  <div class="col-6 col-12">
                     <select multiple class="@error('service_type') is-invalid @enderror" id="service_type" name="service_type[]" size="30" style="height: 100%;" required>
                        <option <?php if( old('service_type') && in_array('Athlete', old('service_type'))) echo "selected"; ?> value="Athlete">Athlete</option>
                        <option <?php if( old('service_type') && in_array('Black/African American', old('service_type'))) echo "selected"; ?> value="Black/African American">Black/African American</option>
                        <option <?php if( old('service_type') && in_array('Career', old('service_type'))) echo "selected"; ?> value="Career">Career</option>
                        <option <?php if( old('service_type') && in_array('General', old('service_type'))) echo "selected"; ?> value="General">General</option>
                        <option <?php if( old('service_type') && in_array('Hispanic', old('service_type'))) echo "selected"; ?> value="Hispanic">Hispanic</option>
                        <option <?php if( old('service_type') && in_array('Illness', old('service_type'))) echo "selected"; ?> value="Illness">Illness</option>
                        <option <?php if( old('service_type') && in_array('LGBQT+', old('service_type'))) echo "selected"; ?> value="LGBQT+">LGBQT+</option>
                        <option <?php if( old('service_type') && in_array('Military', old('service_type'))) echo "selected"; ?> value="Military">Military</option>
                        <option <?php if( old('service_type') && in_array('Nurses & Caregivers', old('service_type'))) echo "selected"; ?> value="Nurses & Caregivers">Nurses & Caregivers</option>
                        <option <?php if( old('service_type') && in_array('Relationships', old('service_type'))) echo "selected"; ?> value="Relationships">Relationships</option>
                        <option <?php if( old('service_type') && in_array('Religion', old('service_type'))) echo "selected"; ?> value="Religion">Religion</option>
                        <option <?php if( old('service_type') && in_array('Student', old('service_type'))) echo "selected"; ?> value="Student">Student</option>
                        <option <?php if( old('service_type') && in_array('Teacher', old('service_type'))) echo "selected"; ?> value="Teacher">Teacher</option>
                        <!-- <option value="Online">Online only</option>
                        <option value="Physical location/local">Physical location/local</option>  -->                      
                     </select>
                    
                  </div>

                   <div class="col-6 col-12">
                     <select multiple class="@error('specialize') is-invalid @enderror" id="specialize" name="specialize[]" required>
                        <option <?php if( old('specialize') && in_array('Achievement', old('specialize'))) echo "selected"; ?> value="Achievement">Achievement</option>
                        <option <?php if( old('specialize') && in_array('Addiction', old('specialize'))) echo "selected"; ?> value="Addiction">Addiction</option>
                        <option <?php if( old('specialize') && in_array('Anxiety', old('specialize'))) echo "selected"; ?> value="Anxiety">Anxiety</option>
                        <option <?php if( old('specialize') && in_array('Career', old('specialize'))) echo "selected"; ?> value="Career">Career</option>
                        <option <?php if( old('specialize') && in_array('Depression', old('specialize'))) echo "selected"; ?> value="Depression">Depression</option>
                        <option <?php if( old('specialize') && in_array('Family', old('specialize'))) echo "selected"; ?> value="Family">Family</option>
                        <option <?php if( old('specialize') && in_array('Finance', old('specialize'))) echo "selected"; ?> value="Finance">Finance</option>
                        <option <?php if( old('specialize') && in_array('Health', old('specialize'))) echo "selected"; ?> value="Health">Health</option>
                        <option <?php if( old('specialize') && in_array('Help', old('specialize'))) echo "selected"; ?> value="Help">Help</option>
                        <option <?php if( old('specialize') && in_array('Loneliness', old('specialize'))) echo "selected"; ?> value="Loneliness">Loneliness</option>
                        <option <?php if( old('specialize') && in_array('Mental Health (general)', old('specialize'))) echo "selected"; ?> value="Mental Health (general)">Mental Health (general)</option>
                        <option <?php if( old('specialize') && in_array('Parenting', old('specialize'))) echo "selected"; ?> value="Parenting">Parenting</option>
                        <option <?php if( old('specialize') && in_array('Relationships/Sex', old('specialize'))) echo "selected"; ?> value="Relationships/Sex">Relationships/Sex</option>
                        <option <?php if( old('specialize') && in_array('Social life', old('specialize'))) echo "selected"; ?> value="Social life">Social life</option>
                        <option <?php if( old('specialize') && in_array('Spirituality/Religion', old('specialize'))) echo "selected"; ?> value="Spirituality/Religion">Spirituality/Religion</option>
                        <option <?php if( old('specialize') && in_array('Trauma/PTSD', old('specialize'))) echo "selected"; ?> value="Trauma/PTSD">Trauma/PTSD</option>
                        <option <?php if( old('specialize') && in_array('Politics', old('specialize'))) echo "selected"; ?> value="Politics">Politics</option>
                       <!--  <option value="African American">African American</option>
                        <option value="Anxiety">Anxiety</option>
                        <option value="Caregivers">Caregivers</option>
                        <option value="Children, Youth, Families">Children, Youth, Families</option>
                        <option value="Common Concerns">Common Concerns</option>
                        <option value="Common Prescriptions">Common Prescriptions</option>
                        <option value="COVID-19">COVID-19</option>
                        <option value="Depression">Depression</option>
                        <option value="Equity in Mental Health">Equity in Mental Health</option>
                        <option value="Evidence Based Treatment">Evidence Based Treatment</option>
                        <option value="General Mental Health">General Mental Health</option>
                        <option value="Hospital Staff & Faculty">Hospital Staff & Faculty</option>
                        <option value="LGBQT+">LGBQT+</option>
                        <option value="Mental Health Diagnoses">Mental Health Diagnoses</option>
                        <option value="Mental Health Symptoms">Mental Health Symptoms</option>
                        <option value="Opioid Analgesics">Opioid Analgesics</option>
                        <option value="Opioids">Opioids</option>
                        <option value="Patient Communication Strategies">Patient Communication Strategies</option>
                        <option value="Presentation and Treatment">Presentation and Treatment</option>
                        <option value="Relationships/Sex">Relationships/Sex</option>
                        <option value="Schizophrenia">Schizophrenia</option>
                        <option value="Suicide & Crisis Support">Suicide & Crisis Support</option>
                        <option value="Teens">Teens</option> -->
                      
                     </select>
                    
                  </div>

                  <div class="col-6 col-12">
                    <select name="InPerson" placeholder="In-Person or Virtual" required>
                        <option value="" <?php if(old('InPerson') == '') echo "selected"; ?>>In-Person or Virtual</option>    
                        <option value="Both" <?php if(old('InPerson') == 'Both') echo "selected"; ?>>Both</option>
                        <option value="In-person only" <?php if(old('InPerson') == 'In-person only') echo "selected"; ?>>In-person only</option>
                        <option value="Virtual only" <?php if(old('InPerson') == 'Virtual only') echo "selected"; ?>>Virtual only</option>
                    </select>
                  </div>
                  <div class="col-6 col-12">
                    <input type="email" placeholder="Email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">                   
                  </div>
                  <div class="col-6 col-12">
                    <input type="password" placeholder="Password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">                    
                  </div>
                  <div class="col-6 col-12">
                    <input type="password" placeholder="Confirm Password" class="@error('cpassword') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">                    
                  </div>
                  <div class="row col-12">
                    <!-- <input type="hidden" name="recaptcha" id="recaptcha"> -->
                  </div>
                   <!--  <label>
                        Select Plan (You will be charged after approval by administrator)
                    </label>
                  <div class="form-check" style="margin-left: 20px;">
                      <input class="form-check-input" name="plan" type="radio" value="monthly" id="monthly">
                      <label class="form-check-label" for="monthly" style="margin: 14px 5px;">
                        <b>$50/month</b>
                      </label>
                    </div>
                    <div class="form-check" style="margin-left: 20px;">
                      <input class="form-check-input" name="plan" type="radio" value="anually" id="anually" checked>
                      <label class="form-check-label" for="anually" style="margin: 14px 5px;">
                        <b>$500/anually</b>
                      </label>
                    </div> -->
               </div>
               <div class="btn_div">
                  <button class="btn_c">Submit</button>
               </div>
               <div class="aleardy_account">
                  <p>Already have an account?<a href="{{url('/partner-login')}}">Login</a></p>
               </div>
            </form>
         </div>
      </div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render={{env('RECAPTCHA_SITEKEY')}}"></script>
<script>
    // grecaptcha.ready(function() {
    //     grecaptcha.execute("{{env('RECAPTCHA_SITEKEY')}}", { action: 'submit' }).then(function(token) {
    //         if (token) {
    //         document.getElementById('recaptcha').value = token;
    //         }
    //     });
    // });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#service_type').select2({
            placeholder: 'Who do you service?'
        });

    }); 
    $(document).ready(function() {
        $('#specialize').select2({
            placeholder: 'What do you specialize in?'
        });
        
        $("#state").val($("#state").attr("data-value"));
    });
</script>
@endsection
@endsection
