@extends('layouts.app')
@section('styles')
@endsection
@section('content')
<section class="wrapper" id="vueEl">
  <div class="row">
    <div class="col-lg-12">
      <h3 class="page-header"><i class="fa fa fa-bars"></i>Create Advertisement</h3>
    </div>
    <div class="col-lg-12">
        <form action="{{route('advertisement.update')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="email">Title:</label>
              <input type="text" class="form-control" id="title" name="title" value="{{ $ad->title }}">
                @if ($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
              <input type="hidden" name="id" value="{{ $ad->id }}">
            </div> 
            <div class="form-group">
              <label for="email">Description:</label>
              <textarea class="form-control" id="description" name="description" >{{ $ad->description }}</textarea>
                @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div> 
            <div class="form-group">
              <label for="email">Link:</label>
              <input type="text" class="form-control" id="link" name="link" value="{{ $ad->link }}" >
                @if ($errors->has('link'))
                    <span class="text-danger">{{ $errors->first('link') }}</span>
                @endif
            </div>
            <div class="form-group">
              <label for="logo">Logo:</label>
              @if($ad->logo)
              <div class="col-lg-12" style="margin-bottom:13px;" id="logoFile">
                    <img src="https://dev-vent.s3.amazonaws.com/{{$ad->logo}}" width="240" height="120">
                </div>
              @endif
              <input type="file" class="form-control" name="logo" onchange="handleLogoUpload()" value="{{ old('logo') }}">
                @if ($errors->has('logo'))
                    <span class="text-danger">{{ $errors->first('logo') }}</span>
                @endif
            </div>
            <div class="form-group">
              <label for="logo">Image:</label>
                @if($ad->image)
                    <div class="col-lg-12" style="margin-bottom:13px;" id="imagefile">
                        <img src="https://dev-vent.s3.amazonaws.com/{{$ad->image}}" width="240" height="120">
                    </div>
                @endif
              <input type="file" class="form-control" name="image" onchange="handleImageUpload()" value="{{ old('image') }}">
                @if ($errors->has('image'))
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                @endif
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
  </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
    function handleImageUpload(){
       $("#imagefile").css("display","none");
    }
    function handleLogoUpload(){
       $("#logoFile").css("display","none");
    }
</script>
@endsection