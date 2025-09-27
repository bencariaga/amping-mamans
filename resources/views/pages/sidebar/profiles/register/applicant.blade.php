@extends('layouts.personal-pages')

@section('title', 'Add Applicant')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/register/applicant.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/register/applicant.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-reset">Applicants</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.create') }}" class="text-decoration-none text-reset">Add Applicant</a>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <livewire:client.client-registration>
    </div>
@endsection
