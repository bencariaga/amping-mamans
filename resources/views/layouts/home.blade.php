<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AMPING-MAMANS | @yield('title')</title>

        <link rel="preload" href="{{ asset('images/main/amping-office.png') }}" as="image" importance="high">
        <link rel="icon" href="{{ asset('images/main/amping-logo.png') }}" type="image/x-icon">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
        <link href="{{ asset('css/layouts/home.css') }}" rel="stylesheet">
        @yield('styles')
    </head>

    <body class="bg-transition" style="display: grid; place-items: center center;">
        <div class="background-cover" style="background-image: url('{{ asset('images/main/amping-office.png') }}')"></div>

        @include('components.buttons.theme-toggler.theme-toggler-home')

        <div class="amping-container">
            <div class="content-wrapper">
                @yield('extra-image')

                <div class="form-section @yield('form-wrapper-class')">
                    @if (!View::hasSection('form-wrapper-class') || View::getSection('form-wrapper-class') !== 'about-page-form-section')
                        @include('components.layouts.header')
                    @endif

                    @yield('content')

                    @if (!View::hasSection('form-wrapper-class') || View::getSection('form-wrapper-class') !== 'about-page-form-section')
                        @if ($errors->any())
                            @include('components.feedback.alert', ['type' => 'error', 'messages' => $errors->all()])
                        @endif

                        @if (session('success'))
                            @include('components.feedback.alert', ['type' => 'success', 'messages' => [session('success')]])
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <script src="{{ asset('js/layouts/theme-toggle.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        @yield('scripts')
    </body>
</html>
