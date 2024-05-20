@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Just Mental Podcasts ({{count($topics)}})</h3>
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
        <th>Video Title</th>
		<th>Clicks</th>
		<th>Visits</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">

        <td>@{{item.video_title}}</td>
		<td>@{{item.count}}</td>
		<td>@{{item.visits}}</td>
        <td>@{{item.status}}</td>
        <td>
          <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
          <a href="#" v-bind:href="'justmental_podcast/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('justmental_podcast.add')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Video title:</label>
              <input type="text" class="form-control" id="video_title" name="video_title" required>
            </div>
            <div class="form-group">
              <label for="email">Status:</label>
              <select class="form-control" id="status" name="status" :value="item.status" required>
                <option value="Live">Live</option>                
                <option value="Disable">Disable</option>                
              </select>
            </div>
            <div class="form-group">
              <label for="email">Hyperlink:</label>
              <input type="text" class="form-control" id="hyperlink" name="hyperlink" required>
            </div>
            <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control" id="category_id" name="category_id" :value="item.category_id" required>
                <option value="">Select</option>
                @foreach($categories as $cat)
                  @if($cat->name)
                    <option value="{{$cat->id}}">{{$cat->name}}</option>
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
          <h5 class="modal-title" id="addResourceLabel">UPDATE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('justmental_podcast.update')}}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="id" v-model="item.id">
            <div class="form-group">
              <label for="email">Video title:</label>
              <input type="text" class="form-control" id="video_title" name="video_title" :value="item.video_title" required>
            </div>
            <div class="form-group">
              <label for="email">Status:</label>
              <select class="form-control" id="status" name="status" :value="item.status" :value="item.status" required>
                <option value="Live">Live</option>                
                <option value="Disable">Disable</option>                
              </select>
            </div>
            <div class="form-group">
              <label for="email">Hyperlink:</label>
              <input type="text" class="form-control" id="hyperlink" name="hyperlink" :value="item.hyperlink" required>
            </div>
            <div class="form-group">
              <label for="email">Category:</label>
              <select class="form-control" id="category_id" name="category_id" :value="item.category_id" required>
                <option value="">Select</option>
                @foreach($categories as $cat)
                  @if($cat->name)
                    <option value="{{$cat->id}}">{{$cat->name}}</option>
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
            items: <?= json_encode($topics) ?>
        },
        mounted() {
          $('#dataTable').DataTable();
        },
        methods: {
          editQues: function(item) {
            this.item = item;            
            $('#updateTopics').modal('show');           
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          }
        }
    });
</script>
@endsection
@endsection