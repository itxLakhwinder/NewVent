@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> Support#{{@$topic->id}}</h3>
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
        <td><img src="{{asset('/dev/uploads/support_files/')}}/{{@$topic->image}}" height="200" width="200"></td>
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
    
</section>

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    // $('#dataTable').DataTable();
  });
</script>
@endsection

@endsection
