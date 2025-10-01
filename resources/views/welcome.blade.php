@extends('layouts.home')

@section('title', 'Home')

@section('styles')
    <link href="{{ asset('css/layouts/welcome.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="image-row d-flex justify-content-center align-items-center mt-2 mb-4 gap-4">
        <img src="{{ asset('images/main/general-santos-seal.png') }}" alt="General Santos Seal" class="seal-img">
        <img src="{{ asset('images/main/magandang-gensan.png') }}" alt="Magandang Gensan" class="gensan-img">
    </div>

    <div class="action-buttons d-flex flex-column flex-md-row justify-content-center align-items-center my-1 gap-3">
        <a href="{{ route('about') }}" class="btn btn-primary btn-action fw-bold">
            ABOUT
        </a>
        <a href="{{ route('login') }}" class="btn btn-primary btn-action fw-bold">
            LOG IN
        </a>
    </div>
@endsection
