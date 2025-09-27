@extends('layouts.personal-pages')

@section('title', 'Applicant Profile')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/profile/applicants.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/profile/applicants.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-reset">Applicants</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.show', ['applicant' => $applicantId]) }}" class="text-decoration-none text-reset">Applicant Profile</a>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <livewire:client.client-profile :applicantId="$applicantId">
    </div>
@endsection
