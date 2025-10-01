@extends('layouts.home')

@section('title', 'About')

@section('styles')
    <link href="{{ asset('css/layouts/about.css') }}" rel="stylesheet">
@endsection

@section('form-wrapper-class', 'about-page-form-section')

@section('content')
    <div class="about-page-layout">
        <div class="info-section">
            <p class="info-text fw-bold">The AMPING Medical Assistance Monitoring and Notification System
                (AMPING-MAMANS) is developed by Aimee Laurence Cando, Benhur L. Cariaga,
                and Angel Kate P. Hinoctan. It is a specialized web-based system designed for the
                Auxiliaries and Medical Program for Individuals and Needy Generals (AMPING),
                a medical and funeral assistance program under the City Mayor's Office of General Santos.

                The system's primary goal is to modernize the program's operations
                by automating its medical assistance application process and digitizing
                records for both employees and clients. The system incorporates key features
                such as centralized data management, automated system reporting,
                role-based access control (RBAC) for secure staff operations, and
                short message service (SMS) notifications for communicating with applicants.

                The system is designed to significantly reduce the time staff spend on
                searching for and retrieving information. It also helps prevent budget
                overspending through improved monitoring of fund usage and guarantee
                letter releases. Furthermore, the SMS feature ensures applicants
                receive timely updates, including their reapplication dates, minimizing
                unnecessary visits to the office. The AMPING-MAMANS seeks to improve
                the transparency and efficiency of the program's services.<br><br>

                © 2025 AMPING-MAMANS Developers. All rights reserved.</p>
            <div id="back-button-container" class="d-flex flex-column flex-md-row justify-content-center align-items-center mt-3 mb-3 gap-3">
                <a href="{{ route('home') }}" id="back-button" class="btn btn-primary btn-action fw-bold">BACK TO HOME</a>
            </div>
        </div>

        <div class="tools-section">
            <p class="tools-header">Here are the list of tools we have used.&nbsp;&nbsp;–&nbsp;&nbsp;AMPING-MAMANS Developers</p>

            <div class="tools-grid">
                @php
                    $tools = [
                        ['link' => 'https://code.visualstudio.com/', 'imageSrc' => 'vscode.png', 'imageAlt' => 'VS Code', 'title' => 'VS Code', 'imageClass' => ''],
                        ['link' => 'https://html.spec.whatwg.org/', 'imageSrc' => 'html.png', 'imageAlt' => 'HTML', 'title' => 'HTML', 'imageClass' => ''],
                        ['link' => 'https://www.w3.org/TR/css/', 'imageSrc' => 'css.png', 'imageAlt' => 'CSS', 'title' => 'CSS', 'imageClass' => ''],
                        ['link' => 'https://ecma-international.org/publications-and-standards/standards/ecma-262/', 'imageSrc' => 'javascript.png', 'imageAlt' => 'JavaScript', 'title' => 'JavaScript', 'imageClass' => ''],
                        ['link' => 'https://nodejs.org/', 'imageSrc' => 'node-js.png', 'imageAlt' => 'Node.js', 'title' => 'Node.js', 'imageClass' => 'node-js'],
                        ['link' => 'https://getbootstrap.com/', 'imageSrc' => 'bootstrap.png', 'imageAlt' => 'Bootstrap', 'title' => 'Bootstrap', 'imageClass' => 'bootstrap'],
                        ['link' => 'https://fontawesome.com/', 'imageSrc' => 'font-awesome.png', 'imageAlt' => 'Font Awesome', 'title' => 'Font Awesome', 'cardClass' => 'font-awesome-box'],
                        ['link' => 'https://www.php.net/', 'imageSrc' => 'php.png', 'imageAlt' => 'PHP', 'title' => 'PHP', 'imageClass' => ''],
                        ['link' => 'https://laravel.com/', 'imageSrc' => 'laravel.png', 'imageAlt' => 'Laravel', 'title' => 'Laravel', 'imageClass' => 'laravel'],
                        ['link' => 'https://getcomposer.org/', 'imageSrc' => 'composer.png', 'imageAlt' => 'Composer', 'title' => 'Composer', 'imageClass' => 'composer'],
                        ['link' => 'https://httpd.apache.org/', 'imageSrc' => 'apache.png', 'imageAlt' => 'Apache', 'title' => 'Apache', 'imageClass' => ''],
                        ['link' => 'https://www.mysql.com/', 'imageSrc' => 'mysql.png', 'imageAlt' => 'MySQL', 'title' => 'MySQL', 'imageClass' => 'mysql'],
                        ['link' => 'https://www.phpmyadmin.net/', 'imageSrc' => 'phpmyadmin.png', 'imageAlt' => 'phpMyAdmin', 'title' => 'phpMyAdmin', 'imageClass' => 'phpmyadmin'],
                        ['link' => 'https://semaphore.co/', 'imageSrc' => 'semaphore.png', 'imageAlt' => 'Semaphore', 'title' => 'Semaphore', 'imageClass' => ''],
                        ['link' => 'https://github.com/bencariaga/amping-mamans', 'imageSrc' => 'github.png', 'imageAlt' => 'GitHub', 'title' => 'GitHub', 'imageClass' => 'github'],
                    ];
                @endphp

                @foreach($tools as $tool)
                    @include('components.data-display.card', ['tool' => $tool])
                @endforeach
            </div>
        </div>
    </div>
@endsection
