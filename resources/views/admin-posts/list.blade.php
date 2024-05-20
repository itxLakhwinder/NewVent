@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Admin Posts ({{count($topics)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <button type="button" data-toggle="modal" data-target="#addTopic" class="btn btn-primary">Add</button>
  
  <form class="form-inline" method="GET">
    <div class="form-group">
      <label for="group">Filter By Group:</label>
      <input type="text" name="group" class="form-control" id="group" value="<?php echo @$_GET['group']; ?>" placeholder="Category Group">
    </div>
    <div class="form-group">
      <label for="pwd">Filter By Category:</label>
      <input type="text" name="mental_health" class="form-control" id="mental_health" value="<?php echo @$_GET['mental_health']; ?>" placeholder="Mental Health">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>

  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        {{-- <th>Image</th> --}}
        <th>Title</th>
        <th>Details</th>
        <th>Category</th>
        <th>Group</th>
		<th>Total Comments</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        {{-- <td> --}}
          {{-- <img v-bind:src="'/uploads/topic_files/' + item.image" width="80" height="80"></td> --}}
        <td>@{{item.title}}</td>
        <td>@{{item.details.substr(0, 20)}}</td>
        <td>@{{item.available_topic}}</td>
        <td>@{{item.category_group}}</td>
		<td>@{{item.comments.length}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
        <td>
          <a v-if="item.status == '1'" v-bind:href="'/admin-posts/enable/' + item.id" onclick="return confirm('Are you sure?');">Enable</a>
          <a v-if="item.status != '1'" v-bind:href="'/admin-posts/disable/' + item.id" onclick="return confirm('Are you sure?');">Disable</a>
        </td>
        <td>
          <a href="#" v-bind:href="'/admin-posts/' + item.id">View</a> &nbsp; | &nbsp;
          <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
          <a href="#" v-bind:href="'/admin-posts/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD POST</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('admin-posts.add')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required value="{{ old('title') }}">
            </div>
            <div class="form-group">
              <label for="email">Upload Type:</label>
              <select class="form-control" id="upload_type" name="content_type" v-model="content_type">
                <option value="">Select</option>
                <option value="audio">Audio</option>
                <option value="video">Video</option>
              </select>
              <input v-if="content_type =='audio' ||content_type =='video'" type="file" class="form-control mt-3" id="image" name="file" @change.prevent="handleFileUpload">
              <span class="text-danger" v-if="fileError">@{{ fileError }}</span>
            </div>
            {{-- <div class="form-group">
              <label for="email">Audio or Video:</label>
              <input type="file" class="form-control" id="image" name="file">
              @error('file')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div> --}}
            <div class="form-group">
              <label for="email">Details:</label>
              <textarea class="form-control" id="details" name="details" required>{{ old('details') }}</textarea>
              <input type="hidden" id="date" name="date" >
            </div>
            <div class="form-group">
                <label for="email">Type:</label>
                <select class="form-control" id="type" name="type" required >
                    <option value="adults">Adults</option>
                    <option value="teens">Teens</option>
                </select>
            </div> 
            <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control" id="available_topic" name="available_topic[]"  required multiple>
                <option value="">Select</option>
                @foreach($availabletopics as $availabletopic)
                  @if($availabletopic->title)
                    <option value="{{$availabletopic->title}}">{{$availabletopic->title}}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
                <label for="group">Group:</label>
                <select class="form-control" id="group" name="category_group[]"  required multiple>
                  <option value="">Select</option>
                  @foreach($groups as $group)
                    @if($group->name)
                      <option value="{{$group->name}}">{{$group->name}}</option>
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
          <h5 class="modal-title" id="addResourceLabel">UPDATE POST</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('admin-posts.update')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.title" required>
              <input type="hidden" name="id" v-model="item.id">
            </div> 
            <div class="form-group">
              <label for="email">Upload Type:</label>
              <select class="form-control" id="upload_type_update" name="content_type" v-model="item.content_type" :value="item.content_type" @change.prevent="changeContentType">
                <option value="">Select</option>
                    <option value="audio">Audio</option>
                    <option value="video">Video</option>
              </select>
              <td v-if="item.image != ''"> 
                <video v-if="item.content_type=='video' && item.image != null" width="240" height="120" controls>
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="video/mp4">
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="video/mpa">
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="video/mov">
                  Your browser does not support the video tag.
                </video>
                <audio v-if="item.content_type=='audio' && item.image != null" controls>
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="audio/mp4">
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="audio/mpa">
                  <source v-bind:src="'https://dev-vent.s3.amazonaws.com/' + item.image" type="audio/mp3">
                Your browser does not support the audio element.
                </audio>
            </td>
              <input v-if="item.content_type =='audio' ||item.content_type =='video'" type="file" class="form-control mt-3" id="Updateimage" name="file" @change.prevent="handleFileUpload">
              <span class="text-danger" v-if="fileError">@{{ fileError }}</span>
            </div>
            {{-- <div class="form-group">
              <td v-if="item.image"> <img v-bind:src="'/uploads/topic_files/' + item.image" width="80" height="80"></td>
              <label for="email">Audio or Video:</label>
              @error('file')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
              @enderror
              <input type="file" class="form-control" id="image" name="file">
            </div> --}}
            <div class="form-group">
              <label for="email">Details:</label>
              <textarea class="form-control" id="details2" name="details2" :value="item.details" required></textarea>
            </div> 
            <div class="form-group">
                <label for="email">Type:</label>
                <select class="form-control" id="type" name="type" required :value="item.type">
                    <option value="adults">Adults</option>
                    <option value="teens">Teens</option>
                </select>
            </div>     
             <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control upsel" id="available_topic" name="available_topic[]" required multiple>
                <option value="">Select</option>
                @foreach($availabletopics as $availabletopic)
                  @if($availabletopic->title)
                    <option value="{{$availabletopic->title}}">{{$availabletopic->title}}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="form-group">
                <label for="email">Group:</label>
                <select class="form-control groupSelected" id="groups" name="category_group[]" required multiple>
                  <option value="">Select</option>
                  @foreach($groups as $group)
                    @if($group->name)
                      <option value="{{$group->name}}">{{$group->name}}</option>
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
            content_type:'',
            fileError:'',
        },
        mounted() {
          $("#date").val(moment().format("YYYY-MM-DD HH:mm:ss"))
          $('#dataTable').DataTable({
             "order": []
          });
        },
        methods: {
          editQues: function(item) {
            this.fileError='',
            this.item = item;           
            // for(let ky of ) {
            // console.log(ky)

            // } 
   //          $.each(this.item.available_topic.split(","), function(i,e){
			//     $(".upsel option[value=" +$.trim(e.toString()) + "]").prop("selected", true);
			// });
            $('#updateTopics').modal('show');           
            $(".upsel").val($.trim(this.item.available_topic).split(","));
            $(".groupSelected").val($.trim(this.item.category_group).split(","));
          },
          handleFileUpload(event) {
            const file = event.target.files[0];
            const fileSizeLimit = 5 * 1024 * 1024; // 5MB

            if (file.size > fileSizeLimit) {
              this.fileError = "Sorry you can't upload file greter than 5MB.";
              event.target.value = null; // Clear the input field
            }
         },
          changeContentType(){
            this.item.image=null;
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("MM-DD-YYYY HH:mm");
          },
        }
    });
</script>
@endsection
@endsection