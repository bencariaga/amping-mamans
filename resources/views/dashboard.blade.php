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
        @php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;

    $currentYear = Carbon::now()->year;
    $startOfYear = Carbon::create($currentYear, 1, 1, 0, 0, 0);
    $endOfYear = Carbon::create($currentYear, 12, 31, 23, 59, 59);

    $startOfToday = Carbon::now()->startOfDay();
    $endOfToday = Carbon::now()->endOfDay();

    $glYearCount = DB::table('guarantee_letters')->count();
    $glTodayCount = DB::table('guarantee_letters')->count();
    
    $authUser = Auth::user();
    $userRole = optional(optional($authUser->staff)->role)->role;
    $isEncoder = $userRole === 'Encoder';
        @endphp

        <div id="top-section">
            <div class="datetime-section">
                <div class="date-container">
                    <div class="datetime" id="live-clock"></div>
                </div>

                @include('components.utility.date')
            </div>
        </div>

        <div id="bottom-section">
            <div class="dashboard-container mb-4">
                <!-- Budget Overview Section -->
                <div class="section-header">Budget Overview</div>
                <div class="budget-overview-section">
                    <div class="budget-card clickable" onclick="window.openAllocateBudgetModal()">
                        <div class="budget-label">Allocated Budget</div>
                        <div class="budget-amount" id="allocated-budget-amount">Loading...</div>
                    </div>

                    <div class="budget-card">
                        <div class="budget-label">Budget Used</div>
                        <div class="budget-amount" id="budget-used-amount">Loading...</div>
                    </div>

                    <div class="budget-card">
                        <div class="budget-label">Remaining Budget</div>
                        <div class="budget-amount success" id="remaining-budget-amount">Loading...</div>
                    </div>

                    <div class="budget-card clickable" onclick="window.openSupplementaryBudgetModal()">
                        <div class="budget-label">Supplementary Budget</div>
                        <div class="budget-amount" id="supplementary-budget-status">Loading...</div>
                    </div>
                </div>

                <!-- Quick Actions, Approved Letters & Active Tariff Services Section -->
                <div class="three-column-layout mt-4">
                    <div class="quick-actions-column">
                        <div class="section-header">Quick Actions</div>
                        <div class="quick-actions-section">
                            <a class="action-btn" href="{{ route('profiles.applicants.create') }}">
                                <div class="action-btn-content">
                                    <i class="fas fa-user-plus"></i>
                                    <h6>Register Applicant</h6>
                                    <p class="action-subtitle">Multi-Step Form</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="approved-letters-column">
                        <div class="section-header">Approved Letters</div>
                        <div class="stats-card">
                            <h6 class="stats-title">Approved Guarantee Letters</h6>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-label">As of Today</div>
                                    <div class="stat-value" id="card-subamount-today">{{ $glTodayCount }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">In {{ $currentYear }}</div>
                                    <div class="stat-value" id="card-subamount-year">{{ $glYearCount }}</div>
                                </div>
                            </div>
                            <div class="stats-footer">
                                <a class="stats-link" href="{{ route('applications.list') }}">View All Applications →</a>
                            </div>
                        </div>
                    </div>

                    <div class="active-tariff-column">
                        <div class="section-header">Active Tariff Services</div>
                        <div class="service-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="service-header">SERVICE TYPE</th>
                                        <th class="tariff-header">TARIFF VERSION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($activeServices))
                                        @foreach($activeServices as $service => $tariff)
                                            <tr>
                                                <td class="fw-semibold">{{ $service }}</td>
                                                <td class="fw-semibold">{{ $tariff }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">No active tariff services</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <a class="table-link" href="{{ route('tariff-lists') }}">View All Tariff Lists →</a>
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
