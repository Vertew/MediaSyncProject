@extends('layouts.app')

@section('title', 'Home')

@section('content')

<form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
       <div class="col-md-12">
          <div class="col-md-6 form-group">
             <label>Select Video:</label>
             <input type="file" name="video" class="form-control"/>
          </div>
          <div class="col-md-6 form-group">
             <button type="submit" class="btn btn-success">Save</button>
          </div>
       </div>
    </div>
 </form>

@endsection