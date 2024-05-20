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
        <form action="{{route('advertisement.create')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="email">Title:</label>
                <input type="text" class="form-control" id="title" name="title"  value="{{ old('title') }}">
                @if ($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label for="email">Description:</label>
                <textarea class="form-control" id="description" name="description" >{{ old('description') }}</textarea>
                @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <input type="hidden" id="date" name="date" >
            </div>
            <div class="form-group">
                <label for="email">Link:</label>
                <input type="text" class="form-control" id="link" name="link"  value="{{ old('link') }}">
                @if ($errors->has('link'))
                   <span class="text-danger">{{ $errors->first('link') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label for="logo">Logo:</label>
                <input type="file" class="form-control" id="logo" name="logo" value="{{ old('logo') }}">
                @if ($errors->has('logo'))
                    <span class="text-danger">{{ $errors->first('logo') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label for="logo">Image:</label>
                <input type="file" class="form-control" id="image" name="image" value="{{ old('image') }}">
                @if ($errors->has('image'))
                    <span class="text-danger">{{ $errors->first('image') }}</span>
                @endif
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
  </div>
</section>
@endsection