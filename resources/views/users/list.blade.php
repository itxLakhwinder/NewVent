@extends('layouts.app')
@section('styles')
<style>
  .userlisting {
    display: flex;
    justify-content: right;
    margin-bottom: 26px;
  }
</style>
@endsection
@section('content')
<section class="wrapper">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Users ({{$users->total()}})</h3>
    </div>
  </div>

  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
    <a href="/users?q=qfilled" class="btn btn-success">Questionnaire filled</a>
    <div class="row mt-3 userlisting">
      <div class="col-lg-4 ">
        <form>
          <input type="text" class="form-control" placeholder="search" id="searchElement">
        </form>
      </div>
    </div>
    <div id="UserDataListing">
        <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>#</th>
              <th onclick="sortData('name')">Name <i class="fa fa-sort" aria-hidden="true" id="name"></i></th>
              <th onclick="sortData('email')">Email <i class="fa fa-sort" aria-hidden="true" id="email"></i></th>
              <th onclick="sortData('phone_number')">Phone number <i class="fa fa-sort" aria-hidden="true" id="phone_number"></i></th>
              <th onclick="sortData('address')">Address <i class="fa fa-sort" aria-hidden="true" id="address"></i></th>
              <th>Daily average time</th>
              <th>Questionnaire</th>
              <th>View</th>		  
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = 1;
            ?>
            @foreach($users as $user)
            <tr>
              <td>{{$i}}</td>
              <td>{{$user['name']}}</td>
              <td>{{$user['email']}}</td>
              <td>{{$user['phone_number']}}</td>
              <td>{{$user['address']}}</td>
              <td>{{@$user['time']}} Mins</td>
              <td>
                @if(@$user['answers_count'] && $user['answers_count'] > 0)
                  Yes
                @else
                  No
                @endif            
              </td>
              <td><a href="{{url('user')}}/{{$user['id']}}">View</a> </td>
              <td>
                @if($user['status'] == '1')
                	<a href="{{url('user/enable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Enable</a>
				@elseif($user['status'] == '2')
				  	<a href="{{url('user/enable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Enable</a>
                @else
                	<a href="{{url('user/disable')}}/{{$user['id']}}" onclick="return confirm('Are you sure?');">Disable</a>
                @endif
                |
                <a href="{{url('user/delete')}}/{{$user['id']}}" class="text-danger" onclick="return confirm('Are you sure?');">Delete</a>
              </td>
            </tr>
            <?php 
            $i++;
            ?>
            @endforeach
          </tbody>
        </table>
        {!! $users->links() !!}
    </div>
</section>
@section('scripts')
<script type="text/javascript">
var page=1;
var column='id';
var direction='asc'

 $(document).on('click', '.pagination a', function(event){
  event.preventDefault(); 
  page = $(this).attr('href').split('page=')[1];
  fetchData();
 });

$('#searchElement').on('input', function() {
  page = 1;
  fetchData();
});

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
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const questionnaire = urlParams.get('q')
  // $("#UserDataListing").css("display","none");
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'post',
        url: "/filtered-list",
        data:{ search:searchItem,
        q:questionnaire,
        page:page,
        sort_column: column,
        sort_direction: direction
      },
        success:function(data){
          // $("#UserDataListing").css("display","block");
          $("#UserDataListing").replaceWith(data);
          $( "#"+column ).removeClass( "fa fa-sort" );
          $( "#"+column ).addClass( direction== 'asc'?"fa fa-sort-up" :'fa fa-sort-down')
        }, 
        error:function(err) {
          alert(err)
        }
    });
}
</script>
@endsection
@endsection