@extends('layouts.app')

@section('styles')
@endsection

@section('content')
<section class="wrapper">
  @foreach ($answers as $answer)
    <table class="table table-bordered panel" id="dataTable" width="50%" cellspacing="0">    
      <tbody>
        <tr>
          <th>Title</th>
          <td>{{$answer->title}}</td>
        </tr>
        <tr>
          <th>Category</th>
          <td>{{$answer->category_name}}</td>
        </tr>
        <tr>
          <th>Group</th>
          <td>{{$answer->group_name}}</td>
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
          <th>Submitted At</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $i = 1;
        ?>
        @foreach($answer->questions as $questions)
        <tr>
          <td>{{$i}}</td>
          <td>{{@$questions->question}}</td>
          <td>
            <?php
              $arr = [];
            ?>
            @if(count($questions->answers))
              @foreach($questions->answers as $answer)
                  <?php
                    array_push($arr, $answer->answer);
                  ?>
              @endforeach
              {{implode(', ', $arr)}}
            @endif
          </td>
          <td>{{ date('m-d-Y H:i', strtotime(@$questions->answers[0]->created_at)) }}</td>
        </tr>
        <?php 
        $i++;
        ?>
        @endforeach
      </tbody>
    </table>
    @endforeach
</section>

@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $('#dataTable').DataTable();
  });
</script>
@endsection

@endsection
