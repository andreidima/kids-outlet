<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

     <!-- Scripts -->
    {{-- The „version” method will automatically append a unique hash to the filenames of all compiled files, allowing for more convenient cache busting --}}
    <script src="{{ mix('/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/andrei.css') }}" rel="stylesheet">

    <!-- Font Awesome links -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

</head>
<body>
    @auth
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: darkcyan">
            <div class="container">
                <a class="navbar-brand me-5" href="/acasa">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        {{-- @if (auth()->user()->name === "Andrei Dima") --}}
                            {{-- <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('angajati.index') }}">
                                    <i class="fas fa-users me-1"></i>Angajați
                                </a>
                            </li>
                            <li class="nav-item me-3 dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-clipboard-list me-1"></i>Norme lucrate
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('norme-lucrate.afisare_lunar') }}">Vizualizare tabelară</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('norme-lucrate.index') }}">Administrare</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/norme-lucrate/muta-lucrul-pe-luna-anterioara">Mută lucrul pe luna anterioară</a></li>
                                </ul>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('pontaje.afisare_lunar') }}">
                                    <i class="fas fa-user-clock me-1"></i>Pontaje
                                </a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('produse.index') }}">
                                    <i class="fas fa-tshirt me-1"></i>Produse
                                </a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('produse-operatii.index') }}">
                                    <i class="fas fa-tasks me-1"></i>Produse operații
                                </a>
                            </li> --}}
                        {{-- @else --}}
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('angajati.index') }}">
                                    <i class="fas fa-users me-1"></i>Angajați
                                </a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('produse.index') }}">
                                    <i class="fas fa-tshirt me-1"></i>Produse
                                </a>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('produse-operatii.index') }}">
                                    <i class="fas fa-tasks me-1"></i>Produse operații
                                </a>
                            </li>
                            <li class="nav-item me-3 dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-clipboard-list me-1"></i>Norme lucrate
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('norme-lucrate.afisare_lunar') }}">Vizualizare tabelară</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('norme-lucrate.index') }}">Administrare</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/norme-lucrate/muta-lucrul-pe-luna-anterioara">Mută lucrul pe luna anterioară</a></li>
                                </ul>
                            </li>
                            <li class="nav-item me-3">
                                <a class="nav-link active" aria-current="page" href="{{ route('pontaje.afisare_lunar') }}">
                                    <i class="fas fa-user-clock me-1"></i>Pontaje
                                </a>
                            </li>
                        {{-- @endif --}}
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="navbarAuthentication" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="navbarAuthentication">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>



                                {{-- <div class="dropdown-menu" aria-labelledby="navbarAuthentication">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div> --}}
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @else
        @yield('content')
    @endauth
</body>
</html>
