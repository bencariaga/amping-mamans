@extends('layouts.personal-pages')

@section('title', 'System Reports')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/system/reports.css') }}">
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.index') }}" class="text-decoration-none text-white">System Reports</a>
@endsection

@section('content')
    <div class="container">
        <div class="mb-4">
            <h2 class="mb-2">System Reports</h2>
            <p class="text-muted">Generate and download comprehensive reports for various system data.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Applicants Report</div>
                    <div class="card-body">
                        <p class="fw-semibold small mb-4">Generate reports for new applicants within a specific time period. Filter by barangay and date range.</p>
                        <a href="{{ route('reports.show', ['type' => 'applicants']) }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>View Report
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Patients Report</div>
                    <div class="card-body">
                        <p class="fw-semibold small mb-4">Generate reports for patients added within a specific time period. View patient demographics and categories.</p>
                        <a href="{{ route('reports.show', ['type' => 'patients']) }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>View Report
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Applications Report</div>
                    <div class="card-body">
                        <p class="fw-semibold small mb-4">Generate reports for assistance applications. View totals, billed amounts, and assistance provided by service type.</p>
                        <a href="{{ route('reports.show', ['type' => 'applications']) }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>View Report
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold">Tariff Versions Report</div>
                    <div class="card-body">
                        <p class="fw-semibold small mb-4">Generate reports for tariff list versions. Filter by effectivity date and status (Active, Inactive, Draft, Scheduled).</p>
                        <a href="{{ route('reports.show', ['type' => 'tariffs']) }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 p-4 info-box">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Export Options</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-file-excel text-success me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>XLSX Export</strong>
                            <p class="small mb-0 text-muted">Download reports in Excel format for advanced data analysis and manipulation.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-file-pdf text-danger me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>PDF Export</strong>
                            <p class="small mb-0 text-muted">Download beautifully formatted PDF reports for printing and sharing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
