@extends('layouts.home')

@section('title', 'About')

@section('styles')
    <link href="{{ asset('css/layouts/about.css') }}" rel="stylesheet">
@endsection

@section('form-wrapper-class', 'about-page-form-section')

@section('content')
    <div class="about-page-layout">
        <div class="info-section">
            <p class="info-text fw-bold">Developed by us, Angel Kate P. Hinoctan, Benhur L. Cariaga,
                and Aimee Laurence Cando, the AMPING Medical Assistance Monitoring and Notification System
                (AMPING-MAMANS) is a web-based computer system for the program of the City Mayor’s Office
                of General Santos, called the “Auxiliaries and Medical Program for Individuals and Needy Generals”
                (AMPING), within its office. The system intends to automate the program’s medical assistance
                application process and digitize the data of the program’s employees and clients by
                implementing into the system the following features: data management, system reporting,
                role-based access control (RBAC), and short message service (SMS) notifications.
                It is designed to help reduce the dependence on searching and retrieving physical documents
                on the side of the employees, the administrative program head and non-administrative
                staff members, and to help deliver information by means of text messages to the clients.
                Working under the principles of e-government and and e-governance, the system covers
                AMPING-related matters, such as medical-assistance-type application processes and
                program funding status updates.<br><br>© 2025 AMPING-MAMANS Developers. All rights reserved.</p>
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
