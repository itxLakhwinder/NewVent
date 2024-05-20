@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Supports ({{count($topics)}})</h3>
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
        <th>Image/Video</th>
        <th>Title</th>
        <th>Details</th>
        <th>Feedback Type</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td>
          <img v-bind:src="'/dev/uploads/support_files/' + item.image" width="80" height="80"></td>
        <td>@{{item.title}}</td>
        <td>@{{item.details}}</td>
        <td>@{{item.feedback_type}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
        <td>
          <a v-if="item.status == '1'" v-bind:href="'/dev/public/index.php/support/enable/' + item.id" onclick="return confirm('Are you sure?');">Enable</a>
          <a v-if="item.status != '1'" v-bind:href="'/dev/public/index.php/support/disable/' + item.id" onclick="return confirm('Are you sure?');">Disable</a>
        </td>
        <td>
          <a href="#" v-bind:href="'/dev/public/index.php/support/' + item.id">View</a> &nbsp; | &nbsp;
          <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
          <a href="#" v-bind:href="'/dev/public/index.php/support/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD SUPPORT</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('supports.add')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="email">Image/Video:</label>
              <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="form-group">
              <label for="email">Details:</label>
              <input type="text" class="form-control" id="details" name="details" required>
              <input type="hidden" id="date" name="date">
            </div>
            <div class="form-group">
              <label for="email">Feedback Type:</label>
              <select class="form-control" id="feedback_type" name="feedback_type" required>
                <option value="Advice">Advice</option>
                <option value="Encouragement">Encouragement</option>
                <option value="Can you relate?">Can you relate?</option>
              </select>
            </div>
            <div class="form-group">
              <label for="email">User:</label>
              <select class="form-control" id="user_id" name="user_id" :value="item.user_id" required>
                <option value="">Select</option>
                @foreach($users as $user)
                  @if($user->name)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control" id="available_topic" name="available_topic[]"  required multiple>
                <option value="">Select</option>
                @foreach($categories as $category)
                  @if($category->name)
                    <option value="{{$category->name}}">{{$category->name}}</option>
                  @endif
                @endforeach
              </select>
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
          <h5 class="modal-title" id="addResourceLabel">UPDATE SUPPORT</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('supports.update')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.title" required>
              <input type="hidden" name="id" v-model="item.id">
            </div> 
            <div class="form-group">
              <label for="email">Image/Video:</label>
              <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="form-group">
              <label for="email">Details:</label>
              <input type="text" class="form-control" id="details2" name="details2" :value="item.details" required>
            </div> 
            <div class="form-group">
              <label for="email">Feedback Type:</label>
              <select class="form-control" id="feedback_type" name="feedback_type" :value="item.feedback_type" required>
                <option value="Advice">Advice</option>
                <option value="Encouragement">Encouragement</option>
                <option value="Can you relate?">Can you relate?</option>
              </select>
              <!-- <input type="text" class="form-control" id="feedback_type" name="feedback_type" required> -->
            </div>
            <div class="form-group">
              <label for="email">User:</label>
              <select class="form-control" id="user_id" name="user_id" :value="item.user_id" required>
                <option value="">Select</option>
                @foreach($users as $user)
                  @if($user->name)
                    <option value="{{$user->id}}">{{$user->name}}</option>
                  @endif
                @endforeach
              </select>
            </div>
             <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control upsel" id="available_topic" name="available_topic[]" required multiple>
                <option value="">Select</option>
                @foreach($categories as $category)
                  @if($category->name)
                    <option value="{{$category->name}}">{{$category->name}}</option>
                  @endif
                @endforeach
              </select>
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
</section>
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
  const vApp = new Vue({
        el: '#vueEl',
        data: {            
            item: {},           
            items: <?= json_encode($topics) ?>,
            users: <?= json_encode($topics) ?>
        },
        mounted() {
          $("#date").val(moment().format("YYYY-MM-DD HH:mm:ss"))
          $('#dataTable').DataTable({
             "order": []
          });
        },
        methods: {
          editQues: function(item) {
            this.item = item;           
            // for(let ky of ) {
            // console.log(ky)

            // } 
   //          $.each(this.item.available_topic.split(","), function(i,e){
			//     $(".upsel option[value=" +$.trim(e.toString()) + "]").prop("selected", true);
			// });
            $('#updateTopics').modal('show');           
            $(".upsel").val($.trim(this.item.available_topic).split(","));
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          }
        }
    });
</script>
@endsection
@endsection