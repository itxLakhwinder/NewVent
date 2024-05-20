@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Notifications ({{count($notifications)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <button type="button" data-toggle="modal" data-target="#addResource" class="btn btn-primary">Add</button>
  
  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Title</th>
        <th>Message</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td>@{{item.title}}</td>
        <td>@{{item.message}}</td>
        <td>@{{timeFormat(item.created_at)}}</td>
        <td>
          <!-- <a href="javascript:void(0)" class="" @click="editResource(item)">Edit</a> | -->
          <a href="#" v-bind:href="'/notification/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addResource" tabindex="-1" role="dialog" aria-labelledby="addResourceLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addResourceLabel">Add RESOURCE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('notifications.add')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="pwd">Message:</label>
              <textarea class="form-control" id="message" name="message" required></textarea>
            </div>
            <div class="form-group">
              <label for="pwd">To:</label>
                <select name="to_user" required class="form-control">
                @foreach($users as $user)
                  <option value="{{$user->id}}">{{$user->name ?: $user->email}}</option>
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
            items: <?= json_encode($notifications) ?>
        },
        mounted() {
          $('#dataTable').DataTable();
        },
        methods: {
          editResource: function(item) {
            this.item = item;            
            $('#updateResource').modal('show');           
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          }
        }
    });
</script>
@endsection
@endsection