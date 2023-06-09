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

       <!-- @livewireStyles-->
    </head>


    <body>

        <div class= "container-fluid p-5 bg-primary text-white">
            <h1 class = "text-center display-1">MediaSync @yield('title')</h1>

            @auth
                <form class = 'mb-1' method="POST" action="{{ route('login.logout') }}">
                    @csrf
                    <input class="btn btn-light" type = "submit" value = "Logout">
                </form>
                <a href="{{route('users.show', ['id'=> Auth::id()])}}">
                    <button class="btn btn-light mr-2" type="button">My Account</button>
                </a>
            @endauth

            
            @if(request()->route()->uri != 'home' and request()->route()->uri != 'login')
                    <a href="{{route('home')}}">
                        <button class="btn btn-light" type="button">Home</button>
                    </a>
            @endif
            <a href="{{url()->previous()}}">
                <button class="btn btn-light" type="button">Back</button>
            </a>

            @guest
                @if(request()->route()->uri != 'login')
                    <a href="{{route('login')}}">
                        <button class="btn btn-light" type="button">Login</button>
                    </a>
                @endif
                @if(request()->route()->uri != 'users/create')
                    <a href="{{route('users.create')}}">
                        <button class="btn btn-light" type="button">Create account</button>
                    </a>
                @endif
            @endguest

            
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

        <!-- Might need to get livewire implemented for this to work-->
        @if (session('message'))
            <div class="alert alert {{ session('alert-class', 'alert-success') }} mt-3 container-md">
                <p><strong>{{session('message')}}</strong></p>
            </div>
        @endif

        @yield('content')

        <!--@livewireScripts-->

    </body>

</html>