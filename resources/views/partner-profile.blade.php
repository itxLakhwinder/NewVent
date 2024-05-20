@extends('layouts.partner')

@section('title', ' - Profile')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="main_section">
          @include('partner-sidebar') 
         <div class="right_section">
            <div class="common_section_right">
               @include('partner-nav')     
               <div class="">
                  <h4 class="h4_tittle">Profile</h4>
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

                   <form method="POST" class="login-form" action="{{ url('/partner-profile') }}" enctype="multipart/form-data">
                    @csrf
                     <div class="row">
                        <div class="col-12">
                           <input type="text" placeholder="Company Name" name="company_name" value="{{ $user ? $user->name : '' }}" required>
                            @error('company_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-12 upload_file">
                           <label>Upload Logo (400px x 200px)</label>
                           @if(@$partner->logo)
                                <img src="{{$partner->logo}}" style="border-radius: 50%;height: 60px;width: 60px;">
                           @endif
                           <input type="file" name="logo" accept="image/png, image/gif, image/jpeg" >
                        </div>
                        <div class="col-12 upload_file">
                           <label>Upload Banner  (600px x 403px)</label>
                            @if(@$partner->banner)
                                <img src="{{$partner->banner}}" height="80">
                            @endif
                           <input type="file" name="banner" accept="image/png, image/gif, image/jpeg" >
                        </div>
                        <div class="col-md-4">
                           <input type="text" placeholder="City" name="City" value="{{ $partner ? $partner->city : '' }}" required>
                           @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                           <select name="state" value="{{ $partner ? $partner->state : '' }}" required id="state">
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
                        <div class="col-md-4">
                           <input type="text" placeholder="Zip Code" name="zip_code" value="{{ $partner ? $partner->zip_code : '' }}" required>
                        </div>

                        <div class="col-12">
                    <select name="InPerson" placeholder="In-Person or Virtual">
                    <option value="">In-Person or Virtual</option>    
                    <option value="Both" {{$partner->in_person == 'Both' ? 'selected' : ''}}>Both</option>
                    <option value="In-person only" {{$partner->in_person == 'In-person only' ? 'selected' : ''}}>In-person only</option>
                    <option value="Virtual only" {{$partner->in_person == 'Virtual only' ? 'selected' : ''}}>Virtual only</option>                  
                    </select>
                  </div>
                       <!--  <div class="col-12">
                            <select class="service_type" id="service_type" name="service_type[]" data-val="{{$partner ? $service : ''}}" required multiple>
                                <option value="Physical location/local">Physical location/local</option>
                                <option value="Online">Online only</option>             
                            </select>                          
                        </div> -->
                        <!-- <div class="col-6">
                           <select class=""category id="category" name="category[]" data-val="{{$partner ? $category : ''}}" required multiple>
                            <option value="LGBQT+">LGBQT+</option>
                            <option value="African American">African American</option>
                            <option value="Teens">Teens</option>
                            <option value="Christian">Christian</option>
                            <option value="Muslim">Muslim</option>
                            <option value="Spiritual">Spiritual</option>
                          </select>         

                        </div> -->
                        <div class="col-6 col-md-6 col-sm-12 col-12">
                     <select multiple id="service_type" name="service_type[]" data-val="{{$partner ? $service : ''}}">
                        <option value="Athlete">Athlete</option>
                        <option value="Black/African American">Black/African American</option>
                        <option value="Career">Career</option>
                        <option value="General">General</option>
                        <option value="Hispanic">Hispanic</option>
                        <option value="Illness">Illness</option>
                        <option value="LGBQT+">LGBQT+</option>
                        <option value="Military">Military</option>
                        <option value="Nurses & Caregivers">Nurses & Caregivers</option>
                        <option value="Relationships">Relationships</option>
                        <option value="Religion">Religion</option>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                     </select>
                    
                  </div>

                  <div class="col-6  col-md-6  col-sm-12 col-12">
                     <select multiple class="spec_cls" id="specialize" name="specialize[]" data-val="{{$partner->specialize}}">
                        <option value="African American">African American</option>
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
                        <option value="Teens">Teens</option>       
                     </select>

                    
                  </div>
                        <!-- <div class="col-12">
                            <select class="topic" id="topic" name="topic[]"  data-val="{{$partner ? $topic : ''}}" required multiple>
                                @foreach($topics as $topic)
                                  <option value="{{$topic->id}}">{{$topic->title}}</option>
                                @endforeach 
                            </select>
                        </div> -->
                        <!-- <div class="col-12">
                           <input type="text" placeholder="Short Description" name="short_description" value="{{ $partner ? $partner->short_description : '' }}" required>
                        </div> -->
                        <div class="col-12">
                           <textarea placeholder="Short Description" name="short_description" class="partnershort" required>{{ $partner ? $partner->short_description : '' }}</textarea>
                        </div>
                        <div class="col-12">
                           <textarea placeholder="Long Description" name="description" class="partnerdis" required>{{ $partner ? $partner->description : '' }}</textarea>
                        </div>
                        <div class="col-12">
                           <input type="text" placeholder="Special/Discount" name="discount" value="{{ $partner ? $partner->discount : '' }}" required>
                        </div>
                        <div class="col-12">
                           <input type="text" placeholder="Website" name="url" value="{{ $partner ? $partner->company_website : '' }}" required>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#service_type').select2({
            placeholder: 'Who do you service?'
        });
        $('#category').select2({
            placeholder: 'Select category'
        });
        $('#specialize').select2({
            placeholder: 'What do you specialize in?'
        });
        $('#topic').select2({
            placeholder: 'Select topic'
        });

        const state = $('#state').attr('value');
        $('#state').val(state)

        setTimeout(() => {
            const service_type = $('#service_type').attr('data-val');
            if(service_type) {
                // let htt = "";
                // for(let ser of service_type.split(',')) {
                //     htt += '<option value="'+ser+'">'+ser+'</option>'
                // }
                // $('#service_type').html(htt);
                $('#service_type').val(service_type.split(','));
                $('#service_type').trigger('change');
            }

            const category = $('#category').attr('data-val');
            if(category) {
                $('#category').val(category.split(','));
                $('#category').trigger('change');
            }
            const specialize = $('#specialize').attr('data-val');
            // console.log(specialize)
            if(specialize) {
                // let httm = "";
                // for(let spec of specialize.split(',')) {
                //     httm += '<option value="'+spec+'">'+spec+'</option>'
                // }
                // $('#specialize').html(httm);

                $('.spec_cls').val(specialize.split(','));
                $('.spec_cls').trigger('change');
            }
            
            const topic = $('#topic').attr('data-val');
            if(topic) {
                $('#topic').val(topic.split(','));
                $('#topic').trigger('change');
            }
            //console.log(service_type, category, topic)


        }, 1000)

    });
</script>
@endsection
@endsection
