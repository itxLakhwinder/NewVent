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
      <tr>
        <th>Birth Year</th>
        <td>{{@$user->birth_year}}</td>
      </tr>  
      <tr>
        <th>State</th>
        <td>{{@$user->state}}</td>
      </tr>     
    </tbody>
    </table>
    <h3>
      <b>Q/A</b>
    </h3>
    <table class="table table-bordered panel" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>#</th>
          <th>Question</th>
          <th>Answer</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $i = 1;
        ?>
        @foreach($questions as $question)
        <tr>
          <td>{{$i}}</td>
          <td>{{@$question->question}}</td>
          <td>
            <?php
              $arr = [];
            ?>
            @if(count($question->answer))
              @foreach($question->answer as $answer)
                  <?php
                    array_push($arr, $answer->answer);
                  ?>
              @endforeach
              {{implode(', ', $arr)}}
            @endif
          </td>
        </tr>
        <?php 
        $i++;
        ?>
        @endforeach
      </tbody>
    </table>
</section>

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $('#dataTable').DataTable();
  });
</script>
@endsection

@endsection
