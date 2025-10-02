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
        use Carbon\Carbon;

        $currentYear = Carbon::now()->year;
        $startOfYear = Carbon::create($currentYear, 1, 1, 0, 0, 0);
        $endOfYear = Carbon::create($currentYear, 12, 31, 23, 59, 59);

        $startOfToday = Carbon::now()->startOfDay();
        $endOfToday = Carbon::now()->endOfDay();

        $glYearCount = DB::table('guarantee_letters')->where('gl_status', 'Approved')->count();
        $glTodayCount = DB::table('guarantee_letters')->where('gl_status', 'Approved')->count();

        $pendingCount = DB::table('applications')->leftJoin('guarantee_letters', 'applications.application_id', '=', 'guarantee_letters.application_id')->whereNull('guarantee_letters.gl_id')->count();
    @endphp

    <div class="datetime-section">
        <div class="date-container">
            <div class="datetime" id="live-clock"></div>
        </div>

        @include('components.utility.date')
    </div>

    <div class="dashboard-grid mb-4">
        <div class="budget-section">
            <div class="dashboard-card">
                <a class="text-decoration-none text-reset" onclick="window.openAllocateBudgetModal()">
                    <h6 class="card-title">Allocated Budget:</h6>
                    <div class="amount" id="allocated-budget-amount">Loading...</div>
                </a>
            </div>

            <div class="dashboard-card">
                <a class="text-decoration-none text-reset" href="{{ route('dashboard') }}">
                    <h6 class="card-title">Budget Amount Used:</h6>
                    <div class="amount" id="budget-used-amount">Loading...</div>
                </a>
            </div>

            <div class="dashboard-card">
                <a class="text-decoration-none text-reset" href="{{ route('dashboard') }}">
                    <h6 class="card-title">Remaining Budget:</h6>
                    <div class="amount" id="remaining-budget-amount">Loading...</div>
                </a>
            </div>

            <div class="dashboard-card">
                <a class="text-decoration-none text-reset" onclick="window.openSupplementaryBudgetModal()">
                    <h6 class="card-title">Supplementary Budget Used:</h6>
                    <div class="amount" id="supplementary-budget-status">Loading...</div>
                </a>
            </div>

            <div class="dashboard-card">
                <h6 class="card-title">Approved Guarantee Letters:</h6>

                <div class="d-grid my-2" style="grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, auto); gap: 8px;">
                    <div>
                        <div class="card-subtitle"><b>Today:</b></div>
                        <div id="card-subamount-today" class="card-subamount">{{ $glTodayCount }}</div>
                    </div>

                    <div>
                        <div class="card-subtitle"><b>Pending:</b></div>
                        <div id="card-subamount-pending" class="card-subamount">{{ $pendingCount }}</div>
                    </div>

                    <div>
                        <div class="card-subtitle"><b>In {{ $currentYear }}:</b></div>
                        <div id="card-subamount-year" class="card-subamount">{{ $glYearCount }}</div>
                    </div>
                </div>
                <div class="show-more" style="margin-bottom: -17px;">
                    <a class="show-more-text" href="{{ route('applications.list') }}">View List</a>
                </div>
            </div>

            <div class="trinity-card d-flex flex-column justify-content-between">
                <a class="dashboard-card text-decoration-none d-flex justify-content-between" id="trinityBtns" href="{{ route('applications.assistance-request') }}">
                    <div class="card-special d-flex align-items-center">
                        <i class="fas fa-plus-circle"></i>
                        <h6 class="trinity-text">Assistance Request</h6>
                    </div>
                </a>

                <a class="dashboard-card text-decoration-none d-flex justify-content-between" id="trinityBtns" href="">
                    <div class="card-special d-flex align-items-center">
                        <i class="fas fa-paper-plane"></i>
                        <h6 class="trinity-text">Send Text Message</h6>
                    </div>
                </a>

                <a class="dashboard-card text-decoration-none d-flex justify-content-between" id="trinityBtns" href="{{ route('message-templates.index') }}">
                    <div class="card-special d-flex align-items-center">
                        <i class="fas fa-comment-alt"></i>
                        <h6 class="trinity-text">SMS Templates</h6>
                    </div>
                </a>
            </div>
        </div>

        <div class="card-section">
            <a class="action-card" href="{{ route('guarantee-letter') }}">
                <i class="mt-2 fas fa-file-alt"></i>
                <h6>Guarantee Letter</h6>
            </a>

            <a class="action-card" href="">
                <i class="mt-2 fas fa-user-times"></i>
                <h6>Deactivated Accounts</h6>
            </a>

            <a class="action-card" href="{{ route('tariff-lists.rows.show') }}">
                <i class="mt-2 fas fa-list-alt"></i>
                <h6 class="mb-2">Tariff Lists</h6>
            </a>

            <a class="action-card" href="">
                <i class="mt-2 fas fa-archive"></i>
                <h6 class="mb-2">Archives</h6>
            </a>

            <a class="action-card" href="">
                <i class="mt-2 fas fa-file-alt"></i>
                <h6 class="mb-2">Logs</h6>
            </a>

            <a class="action-card" href="">
                <i class="mt-2 fas fa-chart-line"></i>
                <h6 class="mb-2">Reports</h6>
            </a>
        </div>

        <div class="service-table">
            <table>
                <thead>
                    <tr>
                        <th class="service-header">SERVICE TYPE</th>
                        <th class="tariff-header">TARIFF LIST VERSION USED</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($serviceTariffs as $serviceType => $tariffVersion)
                        <tr>
                            <td class="fw-semibold">{{ $serviceType }}</td>
                            <td class="fw-semibold">{{ $tariffVersion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="show-more mb-2 pt-2">
                <a class="show-more-text" href="{{ route('tariff-lists.rows.show') }}">SHOW MORE...</a>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
