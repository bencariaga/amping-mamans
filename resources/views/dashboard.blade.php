@extends('layouts.personal-pages')

@section('title', 'Dashboard')

@push('styles')
    <link href="{{ asset('css/layouts/dashboard.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/layouts/dashboard.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a>
@endsection

@section('content')
    <div class="container">
        <div class="row mb-2">
            <div class="col-12">
                <div class="datetime-card">
                    <div class="datetime-content">
                        <i class="fas fa-clock fa-2x me-3"></i>
                        <span id="live-clock" class="datetime-text"></span>
                    </div>

                    @include('components.utility.date')
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="info-card">
                    <div class="info-card-header"><span>Approved Applicant Application Entries</span></div>

                    <div class="info-card-body">
                        <div class="gl-stats">
                            <div class="gl-stat-item">
                                <div class="gl-stat-label">Today</div>
                                <div class="gl-stat-value gl-stat-value-primary" id="gl-today-count">{{ $glTodayCount }}</div>
                            </div>

                            <div class="gl-stat-divider"></div>

                            <div class="gl-stat-item">
                                <div class="gl-stat-label">In {{ $currentYear }}</div>
                                <div class="gl-stat-value gl-stat-value-primary" id="gl-year-count">{{ $glYearCount }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card-footer">
                        <a href="{{ route('applications.list') }}" class="info-card-link">View All Application Entries<i class="fas fa-chevron-right ms-2"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center">
                <div class="row g-3 h-100">
                    <div class="col-md-6">
                        <div class="stat-card stat-card-primary" onclick="window.openAllocateBudgetModal()">
                            <div class="stat-card-body">
                                <div class="stat-card-icon"><i class="fas fa-wallet" aria-hidden="true"></i></div>

                                <div class="stat-card-content">
                                    <h6 class="stat-card-title">Allocated Budget<br>&nbsp;</h6>
                                    <div class="stat-card-value" id="allocated-budget-amount">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card stat-card-primary">
                            <div class="stat-card-body">
                                <div class="stat-card-icon"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></div>

                                <div class="stat-card-content">
                                    <h6 class="stat-card-title">Budget Used<br>&nbsp;</h6>
                                    <div class="stat-card-value" id="budget-used-amount">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card stat-card-primary">
                            <div class="stat-card-body">
                                <div class="stat-card-icon"><i class="fas fa-piggy-bank" aria-hidden="true"></i></div>

                                <div class="stat-card-content">
                                    <h6 class="stat-card-title">Remaining Budget<br>&nbsp;</h6>
                                    <div class="stat-card-value" id="remaining-budget-amount">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="stat-card stat-card-primary" onclick="window.openSupplementaryBudgetModal()">
                            <div class="stat-card-body">
                                <div class="stat-card-icon"><i class="fas fa-sack-dollar" aria-hidden="true"></i></div>

                                <div class="stat-card-content">
                                    <h6 class="stat-card-title">Is Supplementary<br>Budget Used?</h6>
                                    <div class="stat-card-value" id="supplementary-budget-status">Loading...</div>
                                </div>
                            </div>
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
