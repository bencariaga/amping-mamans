@extends('layouts.personal-pages')

@section('title', 'Reports')

@push('styles')
    <style>
        .reports-card { border: 1px solid #e5e7eb; border-radius: .5rem; }
        .reports-card .card-header { 
            background: #f8fafc !important; 
            background-color: #f8fafc !important;
            color: #000 !important;
        }
    </style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>
    <span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.index') }}" class="text-decoration-none text-white">Reports</a>
@endsection

@section('content')
    <div class="container">
        <div class="row g-3 justify-content-center">
            <div class="col-md-4">
                <div class="reports-card card h-100">
                    <div class="card-header fw-bold" style="color: #000 !important; background-color: #e3f2fd !important;">Applications / Assistance</div>
                    <div class="card-body">
                        <p class="text-muted small">Totals and amounts by period</p>
                        <a href="{{ route('reports.show', ['type' => 'applications']) }}" class="btn btn-primary btn-sm">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
