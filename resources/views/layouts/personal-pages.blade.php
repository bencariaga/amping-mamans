<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script>
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme ? storedTheme : (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);

            document.addEventListener('DOMContentLoaded', function () {
                const themeToggle = document.getElementById('themeToggle');
                if (!themeToggle) return;

                const themeText = themeToggle.querySelector('.nav-text');
                if (!themeText) return;

                const themeIcon = themeToggle.querySelector('i');
                if (!themeIcon) return;

                let currentTheme = document.documentElement.getAttribute('data-theme') || 'light';

                if (currentTheme === 'dark') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    themeToggle.title = 'Switch to light mode.';
                    themeText.textContent = 'Light Mode';
                }

                themeToggle.addEventListener('click', () => {
                    currentTheme = currentTheme === 'light' ? 'dark' : 'light';

                    document.documentElement.classList.add('theme-transition');
                    document.documentElement.setAttribute('data-theme', currentTheme);
                    localStorage.setItem('theme', currentTheme);

                    setTimeout(() => {
                        document.documentElement.classList.remove('theme-transition');
                    }, 300);

                    if (currentTheme === 'dark') {
                        themeIcon.classList.remove('fa-moon');
                        themeIcon.classList.add('fa-sun');
                        themeToggle.title = 'Switch to light mode.';
                        themeText.textContent = 'Light Mode';
                    } else {
                        themeIcon.classList.remove('fa-sun');
                        themeIcon.classList.add('fa-moon');
                        themeToggle.title = 'Switch to dark mode.';
                        themeText.textContent = 'Dark Mode';
                    }
                });
            });
        </script>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AMPING-MAMANS | @yield('title', config('app.name'))</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/main/amping-logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/main/amping-logo.png') }}">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/components/overlays/modals.css') }}" rel="stylesheet">
        <link href="{{ asset('css/layouts/personal-pages.css') }}" rel="stylesheet">
        @stack('styles')
        @livewireStyles
    </head>

    <body class="d-flex flex-column min-vh-100 bg-transition">
        @php
            $authUser = Auth::user();
            $authFileRecord = $authUser->files->firstWhere('file_type', 'Image');
            $authProfileImage = optional($authFileRecord)->filename;
            $role = optional(optional($authUser->staff)->role)->role;
        @endphp

        <div class="d-flex flex-grow-1">
            <aside id="sidebar" class="d-flex flex-column sidebar-v1">
                <div class="d-flex flex-column align-items-center py-2 border-bottom sidebar-header">
                    <div class="d-flex align-items-center gap-2">
                        <img alt="AMPING Logo" class="amping-logo" src="{{ asset('images/main/amping-logo-white.png') }}" loading="eager">

                        <div class="website-title">
                            <p class="text-center lh-sm mt-2 sidebar-brand-text">
                                <span class="line">Auxiliaries and Medical</span>
                                <span class="line">Program for Individuals</span>
                                <span class="line">and Needy Generals</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="sidebar-content">
                    <nav class="d-flex flex-column mt-3 px-3 sidebar-nav">
                        <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                            <div class="nav-icon"><i class="fas fa-th-large"></i></div>
                            <div class="nav-text">Dashboard</div>
                        </a>

                        <a class="nav-link dropdown-toggle" id="systemMenuToggle" href="#systemMenu" data-bs-toggle="collapse" aria-expanded="false">
                            <div class="nav-icon"><i class="fas fa-cogs"></i></div>
                            <div class="nav-text">System</div>
                        </a>
                        <div class="collapse" id="systemMenu">
                            <a class="nav-link sub-nav-link" href="">
                                <div class="nav-icon"><i class="fas fa-archive"></i></div>
                                <div class="nav-text">Archives</div>
                            </a>
                            <a class="nav-link sub-nav-link" href="">
                                <div class="nav-icon"><i class="fas fa-user-times"></i></div>
                                <div class="nav-text">Deactivated Accounts</div>
                            </a>
                            <a class="nav-link sub-nav-link" href="">
                                <div class="nav-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="nav-text">Logs</div>
                            </a>
                            <a class="nav-link sub-nav-link" href="">
                                <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
                                <div class="nav-text">Reports</div>
                            </a>
                        </div>

                        <a class="nav-link" href="{{ route('profiles.users.list') }}">
                            <div class="nav-icon"><i class="fas fa-user-friends"></i></div>
                            <div class="nav-text">Users</div>
                        </a>

                        <a class="nav-link" href="{{ route('profiles.applicants.list') }}">
                            <div class="nav-icon"><i class="fas fa-users"></i></div>
                            <div class="nav-text">Applicants</div>
                        </a>

                        <a class="nav-link" href="{{ route('applications.assistance-request') }}">
                            <div class="nav-icon"><i class="fas fa-clipboard-list"></i></div>
                            <div class="nav-text">Assistance Request</div>
                        </a>

                        <a class="nav-link" href="{{ route('guarantee-letter') }}">
                            <div class="nav-icon"><i class="fas fa-list-alt"></i></div>
                            <div class="nav-text">Guarantee Letter</div>
                        </a>

                        <a class="nav-link" href="">
                            <div class="nav-icon"><i class="fas fa-paper-plane"></i></div>
                            <div class="nav-text">Send Text Message</div>
                        </a>

                        <a class="nav-link" href="">
                            <div class="nav-icon"><i class="fas fa-comment-alt"></i></div>
                            <div class="nav-text">SMS Templates</div>
                        </a>

                        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                            <div class="nav-text">Log Out</div>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </nav>
                </div>

                <div class="sidebar-footer border-top">
                    <div id="profile-container">
                        <div class="profile-left">
                            @if($authProfileImage)
                                <a href="{{ route('user.profile.show') }}">
                                    <img alt="User Avatar" class="profile-picture px-0 py-0 border border-white border-2" src="{{ asset('storage/' . $authProfileImage) }}" loading="eager" decoding="sync">
                                </a>
                            @else
                                <a href="{{ route('user.profile.show') }}" class="profile-picture-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center text-decoration-none">
                                    {{ substr($authUser->first_name, 0, 1) }}{{ substr($authUser->last_name, 0, 1) }}
                                </a>
                            @endif
                        </div>

                        <div class="profile-right">
                            <div class="surface-level-profile">
                                <div class="name">{{ $authUser->last_name }}, {{ $authUser->first_name }} {{ substr($authUser->middle_name, 0, 1) }}. {{ $authUser->suffix }}</div>
                                <div class="member-role">{{ $role }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="d-flex flex-column flex-grow-1 main-content-v2" id="main-content">
                <header class="d-flex justify-content-between align-items-center px-4 py-3 navbar-top" id="navbar-top" style="position: sticky; top: 0;">
                    <h2 class="dashboard-title">@yield('breadcrumbs')</h2>

                    @include('components.feedback.alert')
                </header>

                <div class="main-content-body overflow-y-auto">
                    @yield('content')
                </div>

                <footer class="d-flex justify-content-evenly align-items-center px-4 py-3 navbar-bottom" id="navbar-bottom" style="position: sticky; bottom: 0;">
                    @yield('footer')

                    @include('components.buttons.theme-toggler.theme-toggler-personal-pages')
                </footer>
            </main>
        </div>

        @include('pages.dashboard.landing.allocate-budget')
        @include('pages.dashboard.landing.supplementary-budget')
        @include('components.overlays.modals.affiliate-partners')
        @include('components.overlays.modals.occupations')
        @include('components.overlays.modals.roles')
        @include('components.overlays.modals.services')
        @include('components.overlays.modals.sponsors')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/components/overlays/modals.js') }}"></script>
        <script src="{{ asset('js/layouts/personal-pages.js') }}"></script>
        @stack('scripts')
        @livewireScripts
    </body>
</html>
