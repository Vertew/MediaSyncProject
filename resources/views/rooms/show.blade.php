@extends('layouts.app')

@section('title', 'Room')

@section('content')

<div class = "container-md mt-3 text-center">

    <livewire:video-room />

    <div class = "container-md mt-3 text-center">
        <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="container-md mt-3">
                    <label>Add new video</label>
                    <input type="file" name="video" class="form-control"/>
                </div>
                <div class="container-md mt-3">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
        </form>
    </div>

</div>

@endsection