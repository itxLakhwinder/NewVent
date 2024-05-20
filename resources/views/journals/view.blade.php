@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> Journal#{{@$journal->id}}</h3>
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
        <th>Image</th>
        <td><img src="{{asset('/uploads/journal_files/')}}/{{@$journal->image}}" height="200" width="200"></td>
      </tr>
      <tr>
        <th>is_public</th>
        <td>{{@$journal->is_public}}</td>
      </tr>
      <tr>
        <th>topic</th>
        <td>{{@$journal->topic}}</td>
      </tr>
      <tr>
        <th>date</th>
        <td>{{@$journal->date}}</td>
      </tr>
      <tr>
        <th>title</th>
        <td>{{@$journal->title}}</td>
      </tr>
      <tr>
        <th>details</th>
        <td>{{@$journal->details}}</td>
      </tr> 
      <tr>
        <th>type</th>
        <td>{{@$journal->type}}</td>
      </tr> <tr>
        <th>created_at</th>
        <td>{{@$journal->created_at}}</td>
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
        </tr>
      </thead>
      <tbody>
        @if(count($journal->comments))
          @foreach($journal->comments as $comments)
          <tr>
            <td>{{@$comments->comment}}</td>
            <td>{{@$comments->user->name}}</td>
            <td>{{@$comments->created_at}}</td>          
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
