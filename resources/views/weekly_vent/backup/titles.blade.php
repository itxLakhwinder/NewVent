@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Weekly Vent Titles ({{count($weekly_vent_titles)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <button type="button" data-toggle="modal" data-target="#addTopic" class="btn btn-primary">Add</button>
  
  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Title</th>
        <th>Group</th>
        <th>Type</th> 
        <th>Category</th>
        <th>Valid From</th>
        <th>Valid Till</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody >
      <tr v-for="data in dataItems">
        <td class="test1">@{{data.title}}</td>
        <td class="test1">@{{data.group_id}}</td>
        <td class="test1">@{{data.type}}</td>
        <td class="test1">@{{data.category_id}}</td>
        <td class="test1">@{{data.valid_from}}</td>
        <td class="test1">@{{data.valid_to}}</td>
        <td>
          <a href="javascript:void(0)" class="" @click="editQues(data)">Edit</a> |
          <a href="#" v-bind:href="'/weekly-vent/titles/delete/' + data.id" class="text-danger" onclick="return confirm('Are you sure you want to delete this title? By deleting this title all the questions related to this title will be removed too');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD WEEKLY VENT TITLE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('weekly_vent.title.add')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="group">Group:</label>
                <select class="form-control" id="group" name="group_id[]"  required multiple>
                    <option value="">Select</option>
                  <option v-for="group in groups" :value="group.name">@{{group.name}}</option>
                </select>
            </div>  
            <div class="form-group">
              <label for="category">Type:</label>
              <select class="form-control" id="type" name="type" v-model="type" required>
                  <option value="">Select</option>
                <option value="adults">Adults</option>
                <option value="teens">Teens</option>
              </select>
          </div>
          <div class="form-group">
              <label for="category">Category:</label>
              <select class="form-control" id="category" name="category_id[]"  required multiple>
                <option value="">Select</option>
              <option v-for="category in categories" :value="category.title">@{{category.title}}</option>
              </select>
          </div>
          <div class="form-group">
            <label for="valid_from">Valid From Date:</label>
            <input type="date" class="form-control" id="valid_from" name="valid_from" :min="CurrentDate" required v-model="validFromDate" @change.prevent="ResetValidToDate">
          </div>
          <div class="form-group">
            <label for="valid_to">Valid Till Date:</label>
            <input type="date" class="form-control" id="valid_to" name="valid_to" :min="validFromDate" required>
          </div>
            <div class="form-group">
              <label for="pwd">Description:</label>
              <textarea class="form-control" id="description" name="description" required></textarea>
            </div> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="updateTopics" tabindex="-1" role="dialog" aria-labelledby="updateQuesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addResourceLabel">UPDATE WEEKLY VENT TITLE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('weekly-vent.title.update')}}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="id" v-model="item.id">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title_update" name="title" v-model="item.title" required>
            </div>         
          <div class="form-group">
              <label for="group">Group:</label>
              <select class="form-control groupSelected" id="group" name="group_id[]"   required multiple>
                  <option value="">Select</option>
                <option v-for="group in groups" :value="group.name">@{{group.name}}</option>
                </select>
          </div> 
          <div class="form-group">
            <label for="type">Type:</label>
            <select class="form-control" id="type" name="type" v-model="item.type" required >
                <option value="">Select</option>
              <option value="adults">Adults</option>
              <option value="teens">Teens</option>
            </select>
        </div>
        <div class="form-group">
          <label for="category">Category:</label>
          <select class="form-control categorySelected" id="category" name="category_id[]" required multiple>
            <option value="">Select</option>
          <option v-for="category in categories" :value="category.title">@{{category.title}}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="valid_from">Valid From Date:</label>
          <input type="date" class="form-control" id="valid_from" name="valid_from" v-model="item.valid_from" :min="CurrentDate" required @change.prevent="ResetValidToDateInUpdate">
        </div>
        <div class="form-group">
          <label for="update_valid_to">Valid Till Date:</label>
          <input type="date" class="form-control" id="update_valid_to" name="valid_to" v-model="item.valid_to" :min="item.valid_from" required>
        </div> 
          <div class="form-group">
            <label for="pwd">Description:</label>
            <textarea class="form-control" id="description" v-model="item.description" name="description" required></textarea>
          </div> 
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

{{-- <script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script> --}}
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
  const vApp = new Vue({
        el: '#vueEl',
        data: {  
            dataItems:  <?= json_encode($weekly_vent_titles) ?>,          
            item: {}, 
            type:'',
            categories:<?= json_encode($categories) ?>,
            groups: <?= json_encode($groups) ?>,
            CurrentDate:new Date().toISOString().split('T')[0],
            validFromDate:''
        },
        mounted() {
          $('#dataTable').DataTable({
              "ordering": false
          });
        },
        methods: {
          editQues: function(item) {
            this.item = {
              id: item.id,
              title: item.title,
              type: item.type,
              description: item.description,
              group_id:item.group_id,
              category:item.category,
              category_id:item.category_id,
              created_at:item.created_at,
              valid_from:item.valid_from,
              valid_to:item.valid_to,
              group:item.group,
              updated_at:item.updated_at
            }
            // this.item = item;      
            $(".categorySelected").val($.trim(this.item.category_id).split(","));    
            $('#updateTopics').modal('show'); 
            $(".groupSelected").val($.trim(this.item.group_id).split(","));          
          },
          ResetValidToDate(){
           $("#valid_to").val("");
          },
          ResetValidToDateInUpdate(){
           $("#update_valid_to").val("");
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          },
        }
    });
</script>


@endsection
@endsection