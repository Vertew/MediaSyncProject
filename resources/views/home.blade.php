@extends('layouts.app')

@section('title', 'Home')

@section('content')

<a href="{{route('videos.room')}}">
    <button class="btn btn-light" type="button">Get started</button>
</a>

@endsection