@extends('layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> PARTNERS ({{count($partners)}})</h3>
    </div>
  </div>
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

  <button type="button" data-toggle="modal" data-target="#addResource" class="btn btn-primary" data-backdrop="static"
    data-keyboard="false">Add</button>

  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Logo</th>
        <th>Banner</th>
        <th>Title</th>
        <th>Description</th>
        <th>Clicks</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td><img v-if="item.partner" v-bind:src="item.partner.logo" width="80" height="80"></td>
        <td><img v-if="item.partner" v-bind:src="item.partner.banner" width="80" height="80"></td>
        <td>@{{item.name}}</td>
        <td>
          <div v-if="item.partner">
            @{{item.partner.description}}
          </div>
        </td>
        <td>@{{item.partner.count}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
        <td>
          <div>
            <!-- <a v-if="item.status == '0' && item.partner.customer_id" href="javascript:void(0)" class="" @click="approvePartner(item)">Approve</a> -->
            <a v-if="item.status == '0'" href="javascript:void(0)" class="" @click="approvePartner(item)">Approve</a>
            <a v-if="item.status == '1'" href="javascript:void(0)" class="text-success"
              @click="disapprovePartner(item)">Disapprove</a>
            <!-- <a v-if="!item.partner.customer_id" href="javascript:void(0)" class="text-success">No Billing</a> -->
          </div>
          <a href="javascript:void(0)" @click="viewPartner(item)" class="">View</a> |
          <a href="javascript:void(0)" class="" @click="editPartner(item)">Edit</a> |
          <a href="#" v-bind:href="'/partner/delete/' + item.id" class="text-danger"
            onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>


  <!-- Modal View Analytics -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">View Analytics</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="main_section">
            <div class="right_section">
              <div class="common_section_right">

                <div class="analytics_text">
                  <h4 class="h4_tittle">Analytics</h4>
                  <div class="row mt-4">
                    <div class="col-md-6">
                      <div class="card_nalytics">

                        <div class="left_1">
                          <p class="total">Total views</p>
                          <h4 class="number">@{{visit}}</h4>
                          <!-- <p class="today"><span>0</span>today</p> -->
                        </div>

                        <div class="right_1">
                          <img src="{{asset('partner_assets/images/eye.png')}}">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card_nalytics">
                        <div class="left_1 clicks">
                          <p class="total">Total Clicks</p>
                          <h4 class="number">@{{count}}</h4>
                          <!-- <p class="today"><span>0</span>today</p> -->
                        </div>
                        <div class="right_1">
                          <img src="{{asset('partner_assets/images/clicks.png')}}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
        </div>
      </div>
    </div>
  </div>





  <!-- Modal -->
  <div class="modal fade" id="addResource" tabindex="-1" role="dialog" aria-labelledby="addResourceLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addResourceLabel">Add Partner</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('partners.add')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="logo">Logo:</label>
              <input type="file" class="form-control" id="logo" name="logo" required>
            </div>
            <div class="form-group">
              <label for="logo">Banner:</label>
              <input type="file" class="form-control" id="banner" name="banner" required>
            </div>

            {{--
            <div class="form-group">
              <label for="email">First Name:</label>
              <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
              <label for="email">Last Name:</label>
              <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            --}}
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>


            <div class="form-group">
              <label for="email">URL:</label>
              <input type="text" class="form-control" id="url" name="url" required>
            </div>
            {{--
            <div class="form-group">
              <label for="email">City:</label>
              <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group">
              <label for="email">State:</label>
              <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="form-group">
              <label for="email">Zip Code:</label>
              <input type="text" class="form-control" id="zip_code" name="zip_code" required>
            </div>
            --}}

           {{-- <div class="form-group">
              <label for="pwd">Category:</label>
				<div class="cstm-checkbox-wrap">
					<div class="cstm-checkbox-btn">
						<input type="checkbox" name="selectAllCategoryAdd"
							   v-model="selectAllCategoryAdd"
							   @change="toggleSelectAll('category', 'add')">
						<label class="remembr-btn-span" for="select-all">Select All Category </label>
					</div>
				</div>
              <select class="form-control categoryDropdownData" id="categoryDropdownData" name="category[]" required multiple>
					<option value="">Select Category</option>
					@foreach($categoryGroups as $category)
						<option value="{{$category->id}}">{{$category->name}}</option>
					@endforeach 
				  
                <option value="LGBQT+">LGBQT+</option>
                <option value="African American">African American</option>
                <option value="Teens">Teens</option>
                <option value="Christian">Christian</option>
                <option value="Muslim">Muslim</option>
                <option value="Spiritual">Spiritual</option>
				  
              </select>
            </div>--}}
			  
			  
			  <div class="form-group">
				  <label for="pwd">Category:</label>
				  <div class="cstm-checkbox-wrap">
					  <div class="cstm-checkbox-btn">
						  <input type="checkbox" name="selectAllCategoryAdd"
								 v-model="selectAllCategoryAdd"
								 @change="toggleSelectAll('category', 'add')">
						  <label class="remembr-btn-span" for="select-all">Select All Category </label>
					  </div>
				  </div>
				  <select class="form-control categoryDropdownData" id="categoryDropdownData" name="category[]" required multiple>
						<option value="">Select Category</option>
						@foreach($categoryGroups as $category)
						<option value="{{$category->name}}">{{$category->name}}</option>
						@endforeach
				  </select>
            </div>
			  
			  
			  
            <div class="form-group">
              <label for="pwd">Topic:</label>
				<div class="cstm-checkbox-wrap">
					<div class="cstm-checkbox-btn">
						<input type="checkbox" name="selectAllTopicsAdd"
							   v-model="selectAllTopicsAdd"
							   @change="toggleSelectAll('topic', 'add')">
						<label class="remembr-btn-span" for="select-all">Select All Topics </label>
					</div>
				</div>
              <select class="form-control topic" id="topicDropdownData" name="topic[]" required multiple>
                	<option value="">Select Topic</option>
					@foreach($topics as $topic)
					<option value="{{$topic->id}}">{{$topic->title}}</option>
					@endforeach
              </select>
            </div>
            <div class="form-group" style="display:none">
              <label for="pwd">Service Type:</label>
              {{--<select class="form-control service_type" id="service_type" name="service_type[]" required
                multiple>--}}
                <select class="form-control" name="service_type[]" required>
                  <option value="">Select Type</option>
                  {{--<option value="Physical location/local">Physical location/local</option>--}}
                  <option value="Online" selected>Online only</option>
                </select>
            </div>

            {{--
            <div class="form-group">
              <label for="email">Name on Card:</label>
              <input type="text" class="form-control" id="name_on_card" name="name_on_card" required>
            </div>
            <div class="form-group">
              <label for="email">Card Number</label>
              <input type="text" class="form-control" id="card_number" name="card_number" required>
            </div>
            <div class="form-group">
              <label for="email">Expiry Month:</label>
              <select class="form-control" id="exp_month" name="exp_month" required>
                <option value="">Select Month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>
            <div class="form-group">
              <label for="email">Expiry Year:</label>
              <select type="text" class="form-control" id="exp_year" name="exp_year" required>
                <option value="">Select Year</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
                <option value="2031">2031</option>
                <option value="2032">2032</option>
                <option value="2033">2033</option>

              </select>
            </div>

            --}}
            <div class="form-group">
              <label for="pwd">Short Description:</label>
              <textarea class="form-control" id="short_description" name="short_description" required></textarea>
            </div>
            <div class="form-group">
              <label for="pwd">Description:</label>
              <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
              <label for="pwd">Discount:</label>
              <input type="text" class="form-control" id="discount" name="discount" required>
            </div>
            <!-- <div class="form-group">
              <label for="email">Password:</label>
            </div> -->
            <input type="hidden" class="form-control" value="ventspace" id="password" name="password" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="updatepartner" tabindex="-1" role="dialog" aria-labelledby="updateResourceLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addResourceLabel">UPDATE PARTNER</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('partners.update')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.name" required>
              <input type="hidden" name="id" v-model="item.id">
            </div>
            <div class="form-group">
              <label for="logo">Logo:</label>
              <input type="file" class="form-control" id="logo" name="logo">
            </div>
            <div class="form-group">
              <label for="logo">Banner:</label>
              <input type="file" class="form-control" id="banner" name="banner">
            </div>


            {{--
            <div class="form-group">
              <label for="email">First Name:</label>
              <input type="text" class="form-control" id="first_name" name="first_name" :value="partner.first_name"
                required>
            </div>
            <div class="form-group">
              <label for="email">Last Name:</label>
              <input type="text" class="form-control" id="last_name" name="last_name" :value="partner.last_name"
                required>
            </div>
            --}}
            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" name="email" :value="item.email" required>
            </div>



            <div class="form-group">
              <label for="email">URL:</label>
              <input type="text" class="form-control" id="url" name="url" :value="partner.url" required>
            </div>
            {{--
            <div class="form-group">
              <label for="email">City:</label>
              <input type="text" class="form-control" id="city" name="city" :value="partner.city" required>
            </div>
            <div class="form-group">
              <label for="email">State:</label>
              <input type="text" class="form-control" id="state" name="state" :value="partner.state" required>
            </div>
            <div class="form-group">
              <label for="email">Zip Code:</label>
              <input type="text" class="form-control" id="zip_code" name="zip_code" :value="partner.zip_code" required>
            </div>

            --}}
            <div class="form-group">
              <label for="pwd">Category:</label>
				<div class="cstm-checkbox-wrap">
					<div class="cstm-checkbox-btn">
						<input type="checkbox" name="selectAllCategoryEdit"
							   v-model="selectAllCategoryEdit"
							   @change="toggleSelectAll('category', 'edit')">
						<label class="remembr-btn-span" for="select-all">Select All Category </label>
					</div>
				</div>
              <select class="form-control" id="category_2" name="category[]" required multiple>
                <option value="">Select Category</option>

                @foreach($categoryGroups as $category)
                <option value="{{$category->name}}">{{$category->name}}</option>
                @endforeach
				  
				  
				  


                {{--
                <option value="LGBQT+">LGBQT+</option>
                <option value="African American">African American</option>
                <option value="Teens">Teens</option>
                <option value="Christian">Christian</option>
                <option value="Muslim">Muslim</option>
                <option value="Spiritual">Spiritual</option>--}}
              </select>
            </div>

            <div class="form-group">
				<label for="pwd">Topic:</label>
				
				
				<div class="cstm-checkbox-wrap">
					<div class="cstm-checkbox-btn">
						<input type="checkbox" name="selectAllTopicsEdit"
							   v-model="selectAllTopicsEdit"
							   @change="toggleSelectAll('topic', 'edit')">
						<label class="remembr-btn-span" for="select-all">Select All Topics </label>
					</div>
				</div>
				<select class="form-control topic_2" id="topic" name="topic[]" required multiple>
                <option value="">Select Topic</option>
                @foreach($topics as $topic)
                <option value="{{$topic->id}}">{{$topic->title}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="display:none">
              <label for="pwd">Service Type:</label>

              {{--<select class="form-control service_type2" id="service_type2" name="service_type[]" required
                multiple>--}}
                <select class="form-control" name="service_type[]" required>
                  <option value="">Select Type</option>
                  {{-- <option value="Physical location/local">Physical location/local</option> --}}
                  <option value="Online" selected>Online only</option>
                </select>
            </div>

            {{--
            <div class="form-group">
              <label for="email">Name on Card:</label>
              <input type="text" class="form-control" id="name_on_card" name="name_on_card"
                :value="partner.name_on_card" required>
            </div>
            <div class="form-group">
              <label for="email">Card Number</label>
              <input type="text" class="form-control" id="card_number" name="card_number" :value="partner.card_number"
                required>
            </div>
            <div class="form-group">
              <label for="email">Expiry Month:</label>
              <select class="form-control" id="exp_month" name="exp_month" :value="partner.exp_month" required>
                <option value="">Select Month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>
            <div class="form-group">
              <label for="email">Expiry Year:</label>
              <select type="text" class="form-control" id="exp_year" name="exp_year" :value="partner.exp_year" required>
                <option value="">Select Year</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
                <option value="2031">2031</option>
                <option value="2032">2032</option>
                <option value="2033">2033</option>

              </select>
            </div>
            --}}
            <div class="form-group">
              <label for="pwd">Short Description:</label>
              <textarea class="form-control" id="short_description" name="short_description"
                :value="partner.short_description" required></textarea>
            </div>
            <div class="form-group">
              <label for="pwd">Description:</label>
              <textarea class="form-control" id="description" name="description" :value="partner.description"
                required></textarea>
            </div>
            <div class="form-group">
              <label for="discount">Discount:</label>
              <input type="text" class="form-control" id="discount" name="discount" :value="partner.discount" required>
            </div>
            <!-- <div class="form-group">
              <label for="email">Password:</label>
            </div> -->
            <input type="hidden" value="ventspace" class="form-control" id="password" name="password">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</section>
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/vue-multiselect@2.1.6"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.6/dist/vue-multiselect.min.css">
<script type="text/javascript">
  const vApp = new Vue({
    el: '#vueEl',
    data: {
      item: {},
      partner: {},
      count:0,
      visit: 0,
      items: <?= json_encode($partners) ?>,
	  topics: <?= json_encode($topics) ?>,
	  categoryGroups: <?= json_encode($categoryGroups) ?>,
	  selectedTopics: {},
	  selectAllTopics:{},
	  selectAllTopicsEdit: false,
	  selectAllTopicsAdd: false,
	  
	  selectedCategory:{},
	  selectAllCategoryEdit: false,
	  selectAllCategoryAdd: false,
        
      },
					   
    mounted() {
      $('#dataTable').DataTable();
      $('.categoryDropdownData').select2();
      $('.topic').select2();
      $('#category_2').select2();
      $('.topic_2').select2();
      //$('#service_type').select2();
      //$('.service_type2').select2();
      


    },
    
    methods: {

      approvePartner: function (item) {
        var confirmation = confirm('Are you sure you want to approve this partner?');
        if (confirmation) {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });

          $.ajax({
            type: 'POST',
            url: "/partners/approve",
            data: { id: item.id },
            success: function (data) {
              if (data.status) {
                alert(data.message)
                setTimeout(() => {
                  window.location.reload();
                }, 2000)
              }
            }, error: function (err) {
              alert("Please try again later.")
            }
          });

        }
      },


      disapprovePartner: function (item) {
        var confirmation = confirm('Are you sure you want to disapprove this partner?');
        if (confirmation) {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          $.ajax({
            type: 'POST',
            url: "/partners/disapprove",
            data: { id: item.id },
            success: function (data) {
              if (data.status) {
                alert(data.message)
                setTimeout(() => {
                  window.location.reload();
                }, 2000)
              }
            }, error: function (err) {
              alert("Please try again later.")
            }
          });
        }
      },


      viewPartner: function (item) {
        console.log(item);
        this.visit = item.partner.visits;
        this.count = item.partner.count;
        $('#exampleModal').modal('show');

      },
		
		toggleSelectAll:function(value, isEdit){
			
			const category = [];
          	const topic = [];
			if(value == 'category'){
				this.selectedCategory = value ? this.categoryGroups.map(categoryData => String(categoryData.name)) : [];
				
				if(isEdit === 'edit'){
					var inputName = "selectAllCategoryEdit"; // You can replace this with your base input name
				}else{
					var inputName = "selectAllCategoryAdd"; // You can replace this with your base input name
				}
				
				//console.log(inputName);
					
				var checkbox = document.getElementsByName(inputName)[0];
				var isChecked = checkbox.checked;
				if(isChecked){
					for (let selectCategoryName of this.selectedCategory) {
						
						if(inputName === 'selectAllCategoryEdit'){
							$("#category_2 option[value='" + selectCategoryName + "']").attr('selected', 'selected')
							  category.push(selectCategoryName);
						}else{
							var data = $(".categoryDropdownData option[value='" + selectCategoryName + "']").attr('selected', 				'selected')
							category.push(selectCategoryName);
							//console.log(data, "===================================");
							
						}
						
					}
				}else{
					const category = [];
				}
				
				$('.categoryDropdownData').val(category);
				$('.categoryDropdownData').trigger('change');
				$('#category_2').val(category);
				$('#category_2').trigger('change');
				
			}
			
			if(value == 'topic'){
				this.selectedTopics = value ? this.topics.map(topic => String(topic.id)) : [];
				
				if(isEdit === 'edit'){
					var inputName = "selectAllTopicsEdit"; // You can replace this with your base input name
				}else{
					var inputName = "selectAllTopicsAdd";
				}
					
				var checkbox = document.getElementsByName(inputName)[0];
				var isChecked = checkbox.checked;
				if(isChecked){
					for (let selectTopic of this.selectedTopics){
						if(inputName == 'selectAllTopicsEdit'){
							$(".topic_2 option[value='" + selectTopic + "']").attr('selected', 'selected')
							  topic.push(selectTopic);
						}else{
							$("#topicDropdownData option[value='" + selectTopic + "']").attr('selected', 'selected')
						  	topic.push(selectTopic);
						}
						
					}
				}else{
					const topic = [];
				}
				$('.topic_2').val(topic);
				$('.topic_2').trigger('change');
				$('#topicDropdownData').val(topic);
				$('#topicDropdownData').trigger('change');
			}
			
		},


      editPartner: function (item) {
        console.log('item', item)
        this.item = item;
        this.partner = item.partner;
        if (this.partner && this.partner.partnertypes) {
          const category = [];
          const topic = [];
          for (let row of this.partner.partnertypes) {
		  	//console.log(row.category, "rudra");
			  

            if (row.type == "category") {
              category.push(row.category);
              $("#category_2 option[value='" + row.category + "']").attr('selected', 'selected')
            }

            if (row.type == "topic") {
              let ssss = $(".topic_2 option[value='" + row.category + "']").attr('selected', 'selected')
              topic.push(row.category);
            }

          }
			
			//console.log(topic, "testing");

          $('#category_2').val(category);
          $('.topic_2').val(topic);
          $('#category_2').trigger('change');
          $('.topic_2').trigger('change');
        }

        if (this.partner && this.partner.servicetype) {
          const service = [];
          for (let row of this.partner.servicetype) {
            $(".topic_2 option[value='" + row.service + "']").attr('selected', 'selected')
            service.push(row.service);
          }

          $('.service_type2').val(service);
          $('.service_type2').trigger('change');
        }

        // $('#updatepartner').modal('show');    
        $('#updatepartner').modal({ backdrop: 'static', keyboard: false })
      },

      timeFormat: function (datetime) {
        return moment(datetime).format("YYYY-MM-DD HH:mm");
      },

    }
  });

</script>
@endsection
@endsection