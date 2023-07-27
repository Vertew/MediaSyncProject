<!doctype html>

<html lang="en">

    <head>
        <meta charset="UTF-8">

        <title>MediaSync @yield('title')</title>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Styles-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Scripts-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

       @livewireStyles

       @yield('head')
    </head>


    <body>

        <livewire:top-bar />

        <div class= "container-fluid p-5 bg-primary text-white">
            <h1 class = "text-center display-1">MediaSync @yield('title')</h1>

            @auth
                <form class = 'mb-1' method="POST" action="{{ route('login.logout') }}">
                    @csrf
                    <input class="btn btn-light" type = "submit" value = "Logout">
                </form>
                @if(Auth::user()->guest!=true)
                    <a href="{{route('users.show', ['id'=> Auth::id()])}}">
                        <button class="btn btn-light mr-2" type="button">My Account</button>
                    </a>
                @else
                    <a href="{{route('users.create')}}">
                        <button class="btn btn-light" type="button">Create account</button>
                    </a>
                @endif
                @if(request()->route()->uri != 'home')
                    <a href="{{route('home')}}">
                        <button class="btn btn-light" type="button">Home</button>
                    </a>
                @endif
            @endauth

            <a href="{{url()->previous()}}">
                <button class="btn btn-light" type="button">Back</button>
            </a>

        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3 container-md">
                Submit failed due to the following:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><strong>{{$error}}</strong></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('message'))
            <div class="alert alert {{ session('alert-class', 'alert-success') }} mt-3 container-md">
                <p><strong>{{session('message')}}</strong></p>
            </div>
        @endif

        @yield('content')

        @livewireScripts

    </body>

</html>