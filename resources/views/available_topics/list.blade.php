@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Categories ({{count($topics)}})</h3>
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
        <th>Type</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody class="sort_menu">
      <tr v-for="item in items"  :data-id="item.id">
        <td class="test1">@{{item.title}}</td>
        <td >@{{item.title}}</td>
        <td>@{{item.type}}</td>       
        <td>
          <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
          <a href="#" v-bind:href="'/available_topics/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- Modal -->
  <div class="modal fade" id="addTopic" tabindex="-1" role="dialog" aria-labelledby="addTopicLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTopicLabel">ADD CATEGORIES</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('available_topics.add')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="email">Type:</label>
              <select type="text" class="form-control" id="type" name="type" required>
                <option value="teens"> Teens </option>
                <option value="adults"> Adults </option>
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
          <h5 class="modal-title" id="addResourceLabel">UPDATE CATEGORIES</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('available_topics.update')}}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.title" required>
              <input type="hidden" name="id" v-model="item.id">
            </div> 
            <div class="form-group">
              <label for="email">Type:</label>              
              <select type="text" class="form-control" id="type" name="type" required value="item.type">
                <option value="teens"> Teens </option>
                <option value="adults"> Adults </option>
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
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script>
  $(document).ready(function(){

    function updateToDatabase(idString){
       $.ajaxSetup({ headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}});
      
       $.ajax({
            url:'{{url("/indexing_topics")}}',
            method:'POST',
            data:{ids:idString},
            success:function(){
             
            }
         })
    }

      var target = $('.sort_menu');
      target.sortable({
          handle: '.test1',
          placeholder: 'highlight',
          axis: "y",
          update: function (e, ui){
             var sortData = target.sortable('toArray',{ attribute: 'data-id'})
             updateToDatabase(sortData.join(','))
          }
      })
      
  })
</script>
<script type="text/javascript">
  const vApp = new Vue({
        el: '#vueEl',
        data: {            
            item: {},           
            items: <?= json_encode($topics) ?>
        },
        mounted() {
          $('#dataTable').DataTable({
              "ordering": false
          });
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