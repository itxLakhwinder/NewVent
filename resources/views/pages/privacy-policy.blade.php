@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i> Privacy Policy</h3>
    </div>
  </div>
  @if (session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
  </div>
  @endif
  <form action="{{url('privacy-policy-save')}}" method="post">
    @csrf
    <table class="table table-bordered panel" id="dataTable" width="50%" cellspacing="0">
      <tbody>
        <tr>
          <th>Content</th>
          <td>
            <textarea name="body" id="editor1">{{@$text->body}}</textarea>
            <br>
            <input type="hidden" name="page" value="privacy-policy">
            <input type="hidden" name="title" value="Privacy Policy">
            <input type="hidden" name="id" value="{{@$text->id}}">
            <button type="submit" class="btn btn-primary">Save</button>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</section>
@section('scripts')
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  CKEDITOR.replace( 'body' );
});
</script>
@endsection
@endsection