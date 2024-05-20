@extends('layouts.app')
@section('styles')
<style>
  .topiclisting {
    display: flex;
    justify-content: right;
    margin-bottom: 26px;
  }
</style>
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Posts ({{$topics->total()}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
  <button type="button" data-toggle="modal" data-target="#addTopic" class="btn btn-primary">Add</button>
  <div class="row mt-3 topiclisting">
    <form class="form-inline col-lg-8" method="GET">
      <div class="form-group">
        <label for="group">Filter By Group:</label>
        <input type="text" name="group" class="form-control" id="group" value="<?php echo @$_GET['group']; ?>" placeholder="Category Group">
      </div>
      <div class="form-group">
        <label for="pwd">Filter By Mental Health Type:</label>
        <input type="text" name="mental_health" class="form-control" id="mental_health" placeholder="Mental Health">
      </div>
      <button type="button" class="btn btn-default" onclick="filters()">Submit</button>
    </form>
    <form class=" col-lg-4">
      <input type="text" class="form-control" placeholder="search" id="searchElement">
    </form>
  </div>
  <div id="topicsDataListing">
    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          {{-- <th>Image</th> --}}
          <th onclick="sortData('title')">Title <i class="fa fa-sort" aria-hidden="true" id="title"></i></th>
          <th>Details </th>
          <th onclick="sortData('feedback_type')">Feedback Type <i class="fa fa-sort" aria-hidden="true" id="feedback_type"></i></th>
          <th onclick="sortData('category_group')">Group <i class="fa fa-sort" aria-hidden="true" id="category_group"></i></th>
          <th onclick="sortData('available_topic')">Type <i class="fa fa-sort" aria-hidden="true" id="available_topic"></i></th>
          <th>Total Comments </th>
          <th onclick="sortData('created_at')">Created At <i class="fa fa-sort" aria-hidden="true" id="created_at"></i></th>
          <th onclick="sortData('status')">Status <i class="fa fa-sort" aria-hidden="true" id="status"></i></th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items.data">
          {{-- <td>
            <img v-bind:src="'/uploads/topic_files/' + item.image" width="80" height="80"></td> --}}
          <td>@{{item.title}}</td>
          <td>@{{item.details.substr(0, 20)}}</td>
          <td>@{{item.feedback_type}}</td>
          <td>@{{item.category_group}}</td>
          <td>@{{item.available_topic}}</td>
          <td>@{{item.comments.length}}</td>
          <td>@{{timeFormat(item.created_at)}}</td>
          <td>
            <a v-if="item.status == '1'" v-bind:href="'/topic/enable/' + item.id" onclick="return confirm('Are you sure?');">Enable</a>
            <a v-if="item.status != '1'" v-bind:href="'/topic/disable/' + item.id" onclick="return confirm('Are you sure?');">Disable</a>
          </td>
          <td>
            <a href="#" v-bind:href="'/topic/' + item.id">View</a> &nbsp; | &nbsp;
            <a href="javascript:void(0)" class="" @click="editQues(item)">Edit</a> |
            <a href="#" v-bind:href="'/topic/delete/' + item.id" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
          </td>
        </tr>
      </tbody>
    </table>
    {!! $topics->links() !!}
  </div>
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
        <form action="{{route('topics.add')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
              <label for="email">Image:</label>
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
                @foreach($availabletopics as $availabletopic)
                  @if($availabletopic->title)
                    <option value="{{$availabletopic->title}}">{{$availabletopic->title}}</option>
                  @endif
                @endforeach
              </select>
            </div>
			  
			<div class="form-group">
              <label for="email">Peer Group:</label>
              <select class="form-control" id="available_topic" name="peer_groups[]"  required multiple>
                <option value="">Select</option>
                @foreach($preeGroups as $peerGroup)
                  @if($peerGroup)
                    <option value="{{$peerGroup->id}}">{{$peerGroup->name}}</option>
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
        <form action="{{route('topics.update')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" :value="item.title" required>
              <input type="hidden" name="id" v-model="item.id">
            </div> 
            <div class="form-group">
              <label for="email">Image:</label>
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
                @foreach($availabletopics as $availabletopic)
                  @if($availabletopic->title)
                    <option value="{{$availabletopic->title}}">{{$availabletopic->title}}</option>
                  @endif
                @endforeach
              </select>
            </div>
			  
			<div class="form-group">
              <label for="email">Peer Group:</label>
              <select class="form-control peerGroup" id="peer_Group" name="peer_groups[]"  required multiple>
                <option value="">Select</option>
                @foreach($preeGroups as $peerGroup)
                  @if($peerGroup)
                    <option value="{{$peerGroup->id}}">{{$peerGroup->name}}</option>
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
          // $("#date").val(moment().format("YYYY-MM-DD HH:mm:ss"))
          // $('#dataTable').DataTable({
          //    "order": []
          // });
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
			 $(".upsel").val($.trim(this.item.peer_groups).split(","));
          },
          timeFormat: function(datetime) {
            return moment(datetime).format("YYYY-MM-DD HH:mm");
          }
        }
    });
    var page=1;
    var column='id';
    var direction='desc'

    $(document).on('click', '.pagination a', function(event){
      event.preventDefault(); 
      page = $(this).attr('href').split('page=')[1];
      fetchData();
    });

    $('#searchElement').on('input', function() {
      page = 1;
      fetchData();
    });

    function filters(){
      page=1;
      fetchData();
    }
    function sortData(column_name){
      page=1;
      if(column==column_name){
        direction=(direction== 'asc') ? 'desc' : 'asc';

      }else{
        direction='asc';
      }
      column=column_name
      fetchData();
    }

    function fetchData(){
      searchItem = $('#searchElement').val();
      mentalHealth = $('#mental_health').val();
      group = $('#group').val();
      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);
      const questionnaire = urlParams.get('q')
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: "/filtered-topic-list",
            data:{ search:searchItem,
            page:page,
            sort_column: column,
            sort_direction: direction,
            group:group,
            mental_health:mentalHealth
          },
            success:function(response){
              vApp.items = response.topics;
              $( "#title" ).removeClass().addClass("fa fa-sort");
              $( "#feedback_type" ).removeClass().addClass("fa fa-sort");
              $( "#category_group").removeClass().addClass("fa fa-sort");
              $( "#status" ).removeClass().addClass("fa fa-sort");
              $( "#created_at" ).removeClass().addClass("fa fa-sort");
              $( "#"+column ).addClass( direction== 'asc'?"fa fa-sort-up" :'fa fa-sort-down')
              $(".pagination").html(response.pagination);
              // $( "#"+column ).removeClass( "fa fa-sort" );
              // $( "#"+column ).addClass( direction== 'asc'?"fa fa-sort-up" :'fa fa-sort-down')
            }, 
            error:function(err) {
              alert(err)
            }
        });
    }
</script>
@endsection
@endsection