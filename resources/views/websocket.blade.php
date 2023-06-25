@extends('layouts.app')

@section('title', 'Home')

@section('head')

@vite('resources/js/app.js')

@endsection

@section('content')

<script>
    // Getting the current user on the page
    const currentUser = {{ Js::from(Auth::user()->username) }};
</script>

<div id="message-container" class = "container-md mt-3" style="max-height: 300px; overflow-y: auto;">
    <ul class="list-group" id ="message-list">

    </ul>
</div>

<div class = "container-md mt-3 text-center">
    <form id='form1'>
        <div class="container-md mb-3 mt-3">
            <input id="input" type = "text" class="form-control" placeholder="Start typing..." name = "title">
        </div>
    </form>
</div>

@endsection