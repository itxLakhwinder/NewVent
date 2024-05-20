@extends('layouts.app')
@section('styles')
@endsection
@section('content')
  <section class="wrapper" id="vueEl">
    <div class="row">
      <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa fa-bars"></i>Advertisements ({{count($ads)}})</h3>
      </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success" role="alert">
      {{ session('success') }}
    </div>
    @endif
    
    <a type="button" href="{{ route('advertisement.create') }}" class="btn btn-primary">Create</a>

    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>#</th>
          <th>Logo</th>
          <th>Image</th>
          <th>Title</th>
          <th>Description</th>
          <th>Link</th>
          <th>Created At</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item,index) in items">
          <td>@{{index+1}}</td>
          <td><img v-if="item.logo"  :src="`https://dev-vent.s3.amazonaws.com/${item.logo}`" width="80" height="80"></td>
          <td><img v-if="item.image" :src="`https://dev-vent.s3.amazonaws.com/${item.image}`" width="80" height="80"></td>
          <td>@{{item.title}}</td>
          <td>@{{item.description.substr(0, 20)}}</td>
          <td>@{{item.link}}</td>
          <td>@{{timeFormat(item.created_at)}}</td>
          <td>
            <a v-if="item.status == '1'" v-bind:href="'/advertisement/enable/' + item.id" onclick="return confirm('Are you sure you want to disable this ad?');">Enable</a>
            <a v-if="item.status == '0'" v-bind:href="'/advertisement/disable/' + item.id" onclick="return confirm('Are you sure you want to enable this ad?');">Disable</a>
          </td>
          <td>
            <a href="#" v-bind:href="'/advertisement/' + item.id">View</a> &nbsp; | &nbsp;
            <a v-bind:href="'/advertisement/edit/' + item.id">Edit</a> |
            <a href="#" v-bind:href="'/advertisement/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure you want to delete this ad?');">Delete</a>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
@endsection
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
  const vApp = new Vue({
        el: '#vueEl',
        data: {            
            item: {},           
            items: <?= json_encode($ads) ?>,
        },
        mounted() {
          $("#date").val(moment().format("YYYY-MM-DD HH:mm:ss"))
          $('#dataTable').DataTable({
            "order": []
          });
        },
        methods: {
          timeFormat: function(datetime) {
            return moment(datetime).format("MM-DD-YYYY");
          },
        }
    });
</script>
@endsection