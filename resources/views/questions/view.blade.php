@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa fa-bars"></i> User#{{@$user->id}}</h3>
        </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif
    <table class="table table-bordered panel" id="dataTable" width="50%" cellspacing="0">
    
    <tbody>
      <tr>
        <th>Name</th>
        <td>{{@$user->name}}</td>
      </tr>
      <tr>
        <th>Email</th>
        <td>{{@$user->email}}</td>
      </tr>
      <tr>
        <th>Phone number</th>
        <td>{{@$user->phone_number}}</td>
      </tr>     
    </tbody>
</section>

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    // $('#dataTable').DataTable();
  });
</script>
@endsection

@endsection
