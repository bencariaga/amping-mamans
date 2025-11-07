@extends('layouts.home')

@section('title', 'Login')

@section('styles')
    <link href="{{ asset('css/pages/authentication/login.css') }}" rel="stylesheet">
    <style>
        .password-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            padding: 4px;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: #495057;
        }
    </style>
@endsection

@section('extra-image')
    <div class="image-section ms-3">
        <img src="{{ asset('images/main/medical-assistance-monitoring-and-notification-symbols.png') }}" alt="Medical Assistance, Monitoring and Notification Symbols" class="left-main-image">
    </div>
@endsection

@section('form-wrapper-class', '')

@section('content')
    <form class="form-container" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group" style="position: relative; z-index: 1;">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" value="{{ old('username') }}" required>
        </div>

        <div class="form-group password-wrapper">
            <input type="password" id="passwordField" name="password" class="form-control mb-3" placeholder="Password" required style="padding-right: 40px;">
            <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
        </div>

        <div class="action-buttons d-flex flex-column flex-md-row justify-content-center align-items-center w-100 mb-3 gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-action fw-bold w-75 w-md-25">HOME</a>
            <button type="submit" class="btn btn-primary btn-action fw-bold w-75 w-md-25">LOG IN</button>
        </div>
    </form>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('passwordField');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
