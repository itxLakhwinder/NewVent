@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> Topic#{{@$topic->id}}</h3>
        </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif
    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
    
    <tbody>
      <tr>
        <th>File</th>
        <td>
        @if($topic->content_type=='video')       
        <video width="240" height="120" controls>
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="video/mpa">
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="video/mp4">
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="video/mov">
          Your browser does not support the video tag.
        </video>
        @endif
        @if($topic->content_type=='audio')  
        <audio v-if="item.content_type=='audio'" controls>
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="audio/mpa">
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="audio/mp4">
          <source src="https://dev-vent.s3.amazonaws.com/{{$topic->image}}" type="audio/mp3">
          Your browser does not support the audio element.
        </audio>
        @endif
      </td>
      </tr>
      <tr>
        <th>title</th>
        <td>{{@$topic->title}}</td>
      </tr>
      <tr>
        <th>details</th>
        <td>{{@$topic->details}}</td>
      </tr> 
      <tr>
        <th>feedback_type</th>
        <td>{{@$topic->feedback_type}}</td>
      </tr> <tr>
        <th>created_at</th>
        <td>{{@$topic->created_at}}</td>
      </tr>     
    </tbody>
    </table>
    <h3>
      <b>Comments</b>
    </h3>
    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>Comment</th>
          <th>Comment By</th>
          <th>Created_at</th>
		  <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if(count($topic->comments))
          @foreach($topic->comments as $comments)
          <tr>
            <td>{{@$comments->comment}}</td>
            <td>{{@$comments->user->name}}</td>
            <td>{{@$comments->created_at}}</td>   
			<td><a href="/comment/delete/{{$comments->id}}" onclick="return confirm('Are you sure?');" class="text-danger">Delete</a></td>
			  
          </tr>
          @endforeach
        @endif
      </tbody>
    </table>
</section>

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    // $('#dataTable').DataTable();
  });
</script>
@endsection

@endsection
