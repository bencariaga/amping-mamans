@extends('layouts.personal-pages')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Archived Data Management</h2>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Archived Applications</h5>
                    <p class="card-text">View and bulk unarchive archived Application records.</p>
                    <a href="{{ route('archive.applications') }}" class="btn btn-primary">View Applications</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Archived Applicants</h5>
                    <p class="card-text">View and bulk unarchive archived Applicant records.</p>
                    <a href="{{ route('archive.applicants') }}" class="btn btn-primary">View Applicants</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Archived Budget Updates</h5>
                    <p class="card-text">View and bulk unarchive archived BudgetUpdate records.</p>
                    <a href="{{ route('archive.budget-updates') }}" class="btn btn-primary">View Budget Updates</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection