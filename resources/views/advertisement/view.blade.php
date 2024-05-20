@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> Advertisement</h3>
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
        <td>
          @if($ad->image)
            <img src="https://dev-vent.s3.amazonaws.com/{{$ad->image}}" width="240" height="120">
          @endif
        </td>
      </tr>
      <tr>
        <th>Logo</th>
        <td>
          @if($ad->logo)
            <img src="https://dev-vent.s3.amazonaws.com/{{$ad->logo}}" width="240" height="120">
          @endif
        </td>
      </tr>
      <tr>
        <th>Title</th>
        <td>{{@$ad->title}}</td>
      </tr>
      <tr>
        <th>Description</th>
        <td>{{@$ad->description}}</td>
      </tr>  
      <tr>
        <th>Link</th>
         <td>{{ @$ad->link}}</td>          
      </tr>  
      <tr>
        <th>created_at</th>
         <td>{{ date('m-d-Y', strtotime(@$ad->created_at)) }}</td>          
      </tr>     
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
