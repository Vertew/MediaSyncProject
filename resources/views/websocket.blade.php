@extends('layouts.app')

@section('title', 'Home')

@section('head')

@vite('resources/js/app.js')

@endsection

@section('content')

<ul id ="message-list">

</ul>

<div class = "container-md mt-3 text-center">
    <form id='form1'>
        <div class="container-md mb-3 mt-3">
            <label for="input" class="form-label">Input:</label>
            <input id="input" type = "text" class="form-control" name = "title">
        </div>
    </form>
</div>

@endsection