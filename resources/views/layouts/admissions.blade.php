<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student admissions') — JBI University</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/admissions.css') }}?v=20261021">
    <script src="{{ asset('js/admissions.js') }}?v=20261021" defer></script>
    <script src="{{ asset('js/form-guard.js') }}?v=20261022" defer></script>
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="admissions-header">
        <a href="{{ url('/') }}" class="brand" aria-label="JBI University home"><img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University" width="112" height="48"><span>@yield('brand-label', 'Student admissions')</span></a>
        @guest <a class="header-link" href="{{ route('login') }}">Sign in <span aria-hidden="true">↗</span></a>
        @else <form method="POST" action="{{ route('logout') }}">@csrf<button class="header-link" type="submit">Sign out</button></form> @endguest
    </header>
    <main id="main" class="admissions-main">
        @yield('content')
    </main>
    <footer class="admissions-footer"><span>© {{ date('Y') }} JBI University</span><a href="mailto:admission@jbiuniversity.com">Contact admissions</a></footer>
</body>
</html>
