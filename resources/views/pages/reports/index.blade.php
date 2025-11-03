@extends('layouts.personal-pages')

@section('title', 'Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/system/reports.css') }}">
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.index') }}" class="text-decoration-none text-white">Reports</a>
@endsection

@section('content')
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Applicants</div>
                    <div class="card-body">
                        <p class="fw-semibold fs-6 mb-4">For new applicants within a certain period.</p>
                        <a href="{{ route('reports.show', ['type' => 'applicants']) }}" class="btn btn-primary">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Patients</div>
                    <div class="card-body">
                        <p class="fw-semibold fs-6 mb-4">For patients added within a certain period.</p>
                        <a href="{{ route('reports.show', ['type' => 'patients']) }}" class="btn btn-primary">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Applications (Assistance Requests)</div>
                    <div class="card-body">
                        <p class="fw-semibold fs-6 mb-4">For totals and amounts within a certain period.</p>
                        <a href="{{ route('reports.show', ['type' => 'applications']) }}" class="btn btn-primary">View Report</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Tariff Versions</div>
                    <div class="card-body">
                        <p class="fw-semibold fs-6 mb-4">For tariff list versions within a certain effectivity date and status.</p>
                        <a href="{{ route('reports.show', ['type' => 'tariffs']) }}" class="btn btn-primary">View Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
