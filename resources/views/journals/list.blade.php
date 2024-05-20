@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Journals ({{count($journals)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <!-- <button type="button" data-toggle="modal" data-target="#addTopic" class="btn btn-primary">Add</button> -->
  
  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>User</th>
        <th>Topic</th>
        <th>Public</th>
        <th>Date</th>
        <th>Title</th>
        <th>Details</th>
        <th>Created At</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td>@{{item.user?.name}}</td>
        <td>@{{item.topic}}</td>
        <td>@{{item.is_public}}</td>
        <td>@{{item.date}}</td>
        <td>@{{item.title}}</td>
        <td>@{{item.details}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
        <td>
          <a v-if="item.status == '1'" v-bind:href="'/journal/enable/' + item.id" onclick="return confirm('Are you sure?');">Enable</a>
          <a v-if="item.status != '1'" v-bind:href="'/journal/disable/' + item.id" onclick="return confirm('Are you sure?');">Disable</a>
        </td>
        <td>
          <a href="#" v-bind:href="'/journal/' + item.id">View</a> &nbsp; | &nbsp;
          <a href="#" v-bind:href="'/journal/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD TOPIC</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('topics.add')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="email">Details:</label>
              <input type="text" class="form-control" id="details" name="details" required>
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
          <h5 class="modal-title" id="addResourceLabel">UPDATE TOPIC</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('topics.update')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.title" required>
              <input type="hidden" name="id" v-model="item.id">
            </div>
            <div class="form-group">
              <label for="email">Details:</label>
              <input type="text" class="form-control" id="details" name="details" :value="item.details" required>
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
            items: <?= json_encode($journals) ?>
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