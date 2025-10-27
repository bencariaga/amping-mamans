@extends('layouts.personal-pages')

@section('title', 'Tariff Lists')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-lists.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/landing/tariff-lists.js') }}" defer></script>
    <script src="{{ asset('js/pages/tariff-list/tariff-list-create.js') }}" defer></script>
    <script src="{{ asset('js/pages/tariff-list/tariff-list-delete.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists') }}" class="text-decoration-none text-white">Tariff List Versions</a>
@endsection

@section('content')
    <div class="container">
        @include('pages.tariff-list.tariff-list-filter-controls')
        @include('pages.tariff-list.tariff-list-version-table')
        @include('pages.tariff-list.tariff-list-create')
        @include('pages.tariff-list.tariff-list-delete')
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-tariff')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
