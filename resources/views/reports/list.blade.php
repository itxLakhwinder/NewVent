@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Reports ({{count($reports)}})</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  
    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>#</th>
          <th>Image</th>
		  <th>Title</th>
          <th>User</th>
          <th>Subject</th>
          <th style="width: 500px !important;" class="details-heading">Details</th>
		  <th style="width: 200px !important;" class="reportings-heading">Report Type</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $i = 1;
        ?>
        @foreach($reports as $report)
        <tr>
          <td>{{$i}}</td>
          <td>
			  @if($report->image)
				<a href="{{asset('uploads/reports/')}}/{{$report->image}}" target="_blank">
				  <img src="{{asset('uploads/reports/')}}/{{$report->image}}" width="100" height="100">
				</a>
			  @endif
            </td>
		  <td>
			 
            @if(isset($report->topic))
              <a href="{{url('topic')}}/{{$report->topic->id}}" target="_blank">{{ $report->topic->title ?? null }}</a>
			@elseif(isset($report->group) && $report->type == 'group')
			  <a href="{{url('peer-groups')}}" target="_blank">{{ $report->group->name ?? null}}</a>
			@else
				N/A           
            @endif
          </td>
          <td>
            @if(isset($report->user))
             <a href="{{url('user')}}/{{$report->user->id}}" target="_blank">{{ $report->user->email ?? null }}</a>
            @endif
          </td>
          <td>{{$report->subject}}</td>
          <td>{{$report->detail}}</td>        
			
			@if($report->type == 'group')
				<td>Group</td>
			@elseif($report->type == 'post')
				<td>Post</td>
			@elseif($report->type == 'story')
				<td>Story</td>
			@elseif($report->type == 'help')
				<td>Help & Support</td>
			@else
			<td>Post</td>
			@endif
        </tr>
        <?php 
        $i++;
        ?>
        @endforeach
      </tbody>
    </table>
  
</section>
@section('scripts')
<style>
th.details-heading.sorting {
    width: 500px !important;
}
th.reportings-heading{
	width:200px !important;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
  $('#dataTable').DataTable({
    "aaSorting": []
  });
});
</script>
@endsection
@endsection