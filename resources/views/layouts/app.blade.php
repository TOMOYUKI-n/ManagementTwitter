<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="twitterを利用し、自動で集客を行えるツール">
    <meta name="keywords" content="twitter 集客 ツール">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kamitter') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha256-UzFD2WYH2U1dQpKDjjZK72VtPeWP50NoJjd26rnAdUI=" crossorigin="anonymous" />

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    
</head>
<body>

    <div id="app"  class="wrapper--main">
        <main class="l-body--base">
            @include('header')
            
                <!-- フラッシュメッセージ -->
                @if (session('flash_message'))
                <div class="flash_message flash_message__fade">
                    {{ session('flash_message') }}
                </div>
                @endif
                
                @if (session('error_message'))
                <div class="error_message">
                    {{ session('error_message') }}
                </div>
                @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
