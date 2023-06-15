@extends('layouts.app')

@section('title', 'Home')

@section('content')

<form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
       <div class="col-md-12">
          <div class="col-md-6 form-group">
             <label>Select File:</label>
             <input type="file" name="file" class="form-control"/>
          </div>
          <div class="col-md-6 form-group">
             <button type="submit" class="btn btn-success">Save</button>
          </div>
       </div>
    </div>
 </form>

@endsection