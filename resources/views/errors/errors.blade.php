<!DOCTYPE html>
<html lang="en">
    <head>
        <script>
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        </script>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>AMPING MAMANS - @yield('title')</title>

        <link rel="preload" href="{{ asset('images/main/amping-office.png') }}" as="image" importance="high">
        <link rel="icon" href="{{ asset('images/main/amping-logo.png') }}" type="image/x-icon">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/layouts/errors.css') }}">
    </head>

    <body class="bg-transition" style="display: grid; place-items: center center;">
        <div class="background-cover" style="background-image: url('{{ asset('images/main/amping-office.png') }}')"></div>

        <div class="error-container">
            <div class="error-content">
                <div class="error-icon mb-5">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>

                <h1 class="error-code">@yield('code') Error</h1>
                <h2 class="error-title">Reason: @yield('title')</h2>
                <p class="error-description">@yield('message')</p>

                <div class="error-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">BACK TO DASHBOARD PAGE</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">BACK TO LOGIN PAGE</a>
                    @endauth
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/layouts/errors.js') }}"></script>
    </body>
</html>
