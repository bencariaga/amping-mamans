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

        $glYearCount = DB::table('guarantee_letters')
            ->where('gl_status', 'Approved')
            ->count();

        $startOfToday = Carbon::now()->startOfDay();
        $endOfToday = Carbon::now()->endOfDay();

        $glTodayCount = DB::table('guarantee_letters')
            ->where('gl_status', 'Approved')
            ->count();

        $pendingCount = DB::table('applications')
            ->leftJoin('guarantee_letters', 'applications.application_id', '=', 'guarantee_letters.application_id')
            ->whereNull('guarantee_letters.gl_id')
            ->count();
    @endphp

    <div class="header-container d-flex align-items-center justify-content-between">
        <div class="header-left d-flex align-items-center">
            <div class="live-clock-container">
                <p id="live-clock" class="overview-heading fw-bold mb-0"></p>
            </div>
        </div>
        @include('components.utility.date')
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a class="text-decoration-none text-reset" onclick="window.openAllocateBudgetModal()">
                <div class="card-budget">
                    <div class="card-title">Allocated Budget:</div>
                    <div class="card-amount" id="allocated-budget-amount">Loading...</div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a class="text-decoration-none text-reset" href="{{ route('dashboard') }}">
                <div class="card-budget">
                    <div class="card-title">Budget Amount Used:</div>
                    <div class="card-amount" id="budget-used-amount">Loading...</div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a class="text-decoration-none text-reset" href="{{ route('dashboard') }}">
                <div class="card-budget">
                    <div class="card-title">Remaining Budget:</div>
                    <div class="card-amount" id="remaining-budget-amount">Loading...</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a class="text-decoration-none text-reset" href="{{ route('tariff-lists.show') }}">
                <div class="card-budget">
                    <div class="card-title">Tariff List Version Currently Used:</div>
                    <div class="card-amount">{{ $latestTariffListVersion }}</div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a class="text-decoration-none text-reset" href="{{ route('applications.list') }}">
                <div class="card-budget">
                    <div class="card-title">Guarantee Letters Approved:</div>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <div class="card-subtitle"><b>In {{ $currentYear }}:</b></div>
                            <div id="card-subamount-year" class="card-subamount">{{ $glYearCount }}</div>
                        </div>
                        <div>
                            <div class="card-subtitle"><b>Today:</b></div>
                            <div id="card-subamount-today" class="card-subamount">{{ $glTodayCount }}</div>
                        </div>
                        <div>
                            <div class="card-subtitle"><b>Pending:</b></div>
                            <div id="card-subamount-pending" class="card-subamount">{{ $pendingCount }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a class="text-decoration-none text-reset" onclick="window.openSupplementaryBudgetModal()">
                <div class="card-budget">
                    <div class="card-title">Supplementary Budget Used:</div>
                    <div id="supplementary-budget-status" class="card-amount">Loading...</div>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
