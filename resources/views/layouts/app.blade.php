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

            @if(Auth::check())
                <form class = 'mb-1' method="POST" action="{{ route('login.logout') }}">
                    @csrf
                    <input class="btn btn-light" type = "submit" value = "Logout">
                </form>
                
                <a href="{{url()->previous()}}">
                    <button class="btn btn-light" type="button">Back</button>
                </a>
            @endif
        </div>

        @if (session('message'))
            <div class="alert alert {{ session('alert-class', 'alert-success') }} mt-3 container-md">
                <p><strong>{{session('message')}}</strong></p>
            </div>
        @endif

        @yield('content')

        <!--@livewireScripts-->

    </body>

</html>