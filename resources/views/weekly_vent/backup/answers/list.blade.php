@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Weekly Vent Answers ({{count($answers_list)}})</h3>
    </div>
  </div>
  <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Last Submitted At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items">
        <td>@{{item.name}}</td>
        <td>@{{item.email}}</td>
        <td>@{{timeFormat(item.last_submitted)}}</td>
        <td>
          <a v-bind:href="'weekly-vent/user-answers/view/' + item.id" >View</a> |
        </td>
      </tr>
    </tbody>
  </table>
</section>
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
   
  const vApp = new Vue({
        el: '#vueEl',
        data: {                      
            items: <?= json_encode($answers_list) ?>,
        },
        mounted() {
          $('#dataTable').DataTable();
        },
        methods: {
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          },
        },

    });
</script>
@endsection
@endsection