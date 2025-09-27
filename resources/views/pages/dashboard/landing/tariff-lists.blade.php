@extends('layouts.personal-pages')

@section('title', 'Tariff Lists')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-lists.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/landing/tariff-lists.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">></span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists.rows.show') }}" class="text-decoration-none text-reset">Tariff List Versions</a>
@endsection

@section('content')
    <div id="application-list-page" class="container px-0 pt-0 pb-5 mb-5">
        <livewire:tariff-list.tariff-list-version-table>
        <livewire:tariff-list.tariff-list-create>
        <livewire:tariff-list.tariff-list-edit>
        <livewire:tariff-list.tariff-list-delete>
        <livewire:tariff-list.tariff-list-apply>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-tariff')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
