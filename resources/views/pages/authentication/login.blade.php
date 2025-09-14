@extends('layouts.home')

@section('title', 'Login')

@section('styles')
    <link href="{{ asset('css/pages/authentication/login.css') }}" rel="stylesheet">
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

        <div class="form-group">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username (Format: [First Name] [Last Name])" value="{{ old('username') }}" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" class="form-control mb-3" placeholder="Password (If you want to change, contact the admin.)" required>
        </div>

        <div class="action-buttons d-flex flex-column flex-md-row justify-content-center align-items-center w-100 mb-3 gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-action fw-bold w-75 w-md-25">HOME</a>
            <button type="submit" class="btn btn-primary btn-action fw-bold w-75 w-md-25">LOG IN</button>
        </div>
    </form>
@endsection
