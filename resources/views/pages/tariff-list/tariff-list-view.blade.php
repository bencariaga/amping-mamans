@extends('layouts.personal-pages')

@section('title', 'Edit Tariff List Version')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-list-view.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/pages/tariff-list/tariff-list-view.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists') }}" class="text-decoration-none text-white">Tariff List Versions</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists.view', $tariffListModel->tariff_list_id) }}" class="text-decoration-none text-reset">{{ $tariffListModel->tariff_list_id }}</a>
@endsection

@section('content')
    @php $hasData = !empty($serviceTypes) && count($serviceTypes) > 0; @endphp
    <div class="container" id="container" data-has-data="{{ $hasData ? '1' : '0' }}">
        <form id="tariffForm" class="mb-5" method="POST" action="{{ route('tariff-lists.update', $tariffListModel->tariff_list_id) }}">
            @csrf
            @method('PUT')

            <div id="unsavedChangesBanner" class="alert alert-warning d-none" role="alert">
                <i class="fas fa-triangle-exclamation me-2"></i>
                You have unsaved changes. Click <strong>Save Changes</strong> below to persist your edits.
            </div>

            @php
                $effDate = \Illuminate\Support\Carbon::parse($tariffListModel->effectivity_date);
                $createdAt = \Illuminate\Support\Carbon::parse($tariffListModel->data->created_at);
                $currentDateTime = \Illuminate\Support\Carbon::now();

                $serviceIds = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffListModel->tariff_list_id)
                    ->whereNotNull('exp_range_min')
                    ->whereNotNull('exp_range_max')
                    ->whereNotNull('coverage_percent')
                    ->where('exp_range_min', '>', 0)
                    ->where('exp_range_max', '>', 0)
                    ->where('coverage_percent', '>', 0)
                    ->distinct()
                    ->pluck('service_id');

                $hasValidRanges = false;
                foreach ($serviceIds as $serviceId) {
                    $rangeCount = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffListModel->tariff_list_id)
                        ->where('service_id', $serviceId)
                        ->whereNotNull('exp_range_min')
                        ->whereNotNull('exp_range_max')
                        ->whereNotNull('coverage_percent')
                        ->where('exp_range_min', '>', 0)
                        ->where('exp_range_max', '>', 0)
                        ->where('coverage_percent', '>', 0)
                        ->count();
                    if ($rangeCount >= 2) { $hasValidRanges = true; break; }
                }

                if (!$hasValidRanges) {
                    $status = 'Draft'; $badgeClass = 'warning'; $textColorClass = 'black';
                } elseif ($effDate->startOfDay()->lte($currentDateTime->copy()->startOfDay())) {
                    $allTariffLists = \App\Models\Operation\TariffList::all();
                    $tariffServiceIds = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffListModel->tariff_list_id)->distinct()->pluck('service_id')->toArray();
                    $hasActiveService = false;
                    foreach ($tariffServiceIds as $sid) {
                        $latestForService = $allTariffLists->filter(function ($tl) use ($sid, $currentDateTime) {
                            $tlEffDate = \Illuminate\Support\Carbon::parse($tl->effectivity_date)->startOfDay();
                            if ($tlEffDate->gt($currentDateTime->copy()->startOfDay())) return false;
                            return \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tl->tariff_list_id)->where('service_id', $sid)->exists();
                        })->sortByDesc(function ($tl) { return \Illuminate\Support\Carbon::parse($tl->effectivity_date)->timestamp; })->first();
                        if ($latestForService && $latestForService->tariff_list_id === $tariffListModel->tariff_list_id) { $hasActiveService = true; break; }
                    }
                    if ($hasActiveService) { $status = 'Active'; $badgeClass = 'success'; $textColorClass = 'white'; }
                    else { $status = 'Inactive'; $badgeClass = 'secondary'; $textColorClass = 'white'; }
                } else {
                    $hoursUntilEffective = $currentDateTime->diffInHours($effDate->startOfDay(), false);
                    $status = 'Scheduled';
                    $badgeClass = $hoursUntilEffective <= 24 ? 'danger' : 'primary';
                    $textColorClass = 'white';
                }
            @endphp

            <div class="service-info">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h3 class="service-count m-0">TL Version's Number of Services: {{ count($serviceLists) }}</h3>
                    <span class="badge rounded-pill bg-{{ $badgeClass }} text-{{ $textColorClass }} fw-bold px-3 py-2">{{ $status }}</span>
                </div>
                <h3 class="service-status mt-3">
                    <span class="view-mode">Currently Viewing: <span id="currentService">{{ $serviceTypes[0] ?? 'No Service' }}</span></span>

                    @if($tariffListModel->tl_status === 'Draft')
                        <span class="edit-mode" style="display: none;">Currently Adding: <span id="editingService">{{ $serviceTypes[0] ?? 'No Service' }}</span></span>
                    @else
                        <span class="edit-mode" style="display: none;">Currently Editing: <span id="editingService">{{ $serviceTypes[0] ?? 'No Service' }}</span></span>
                    @endif
                </h3>
            </div>

            <ul class="nav nav-tabs mb-4" id="serviceTabs" role="tablist">
                @foreach($serviceTypes as $index => $type)
                    @php
                        $serviceId = null;

                        foreach($serviceLists[$type] ?? [] as $range) {
                            $serviceId = $range->service_id;
                            break;
                        }
                    @endphp

                    <li class="nav-item" role="presentation" data-service-type="{{ $type }}" data-service-id="{{ $serviceId }}">
                        <div class="service-tab-wrapper">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#service-{{ $index }}" type="button" role="tab" aria-controls="service-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $type }}
                            </button>

                            <button type="button" class="btn-remove-service" data-service-type="{{ $type }}" data-service-id="{{ $serviceId }}" title="Remove this service type" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </li>
                @endforeach

                <li class="nav-item" role="presentation" id="addServiceDropdownContainer" style="display: none;">
                    <select class="form-select service-type-dropdown" id="addServiceDropdown">
                        <option value="" selected>— Select Service —</option>
                        @foreach($allServiceTypes as $serviceId => $serviceType)
                            @if(!in_array($serviceId, $usedServiceIds))
                                <option value="{{ $serviceId }}">{{ $serviceType }}</option>
                            @endif
                        @endforeach
                    </select>
                </li>
            </ul>

            <div class="tab-content" id="serviceTabsContent">
                @foreach($serviceTypes as $index => $type)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="service-{{ $index }}" role="tabpanel" aria-labelledby="tab-{{ $index }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="shadow-sm tariff-section p-3 mx-auto">
                                    <div class="table-responsive">
                                        <table class="expense-table w-100">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="money-amount-header text-center" id="money-amount-header-1">Expense Range</th>
                                                    <th rowspan="2" class="money-amount-header text-center" id="money-amount-header-2">Coverage<br>(%)</th>
                                                    <th rowspan="2" class="money-amount-header text-center" id="money-amount-header-3">Actions</th>
                                                </tr>
                                                <tr>
                                                    <th class="money-amount-header text-center">Minimum</th>
                                                    <th class="money-amount-header text-center">Maximum</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($serviceLists[$type] ?? [] as $row)
                                                    <tr>
                                                        <td class="money-amount-cell">
                                                            <div class="money-amount-container">
                                                                <span class="money-currency fw-bold pe-2">₱</span>
                                                                <input type="text" name="range_min[{{ $row->service_id }}][{{ $row->exp_range_id }}]" class="form-control form-control-sm range-input range-min text-end money-value" value="{{ number_format($row->exp_range_min, 0) }}" placeholder="0" maxlength="8" readonly>
                                                            </div>
                                                        </td>

                                                        <td class="money-amount-cell">
                                                            <div class="money-amount-container">
                                                                <span class="money-currency fw-bold pe-2">₱</span>
                                                                <input type="text" name="range_max[{{ $row->service_id }}][{{ $row->exp_range_id }}]" class="form-control form-control-sm range-input range-max text-end money-value" value="{{ number_format($row->exp_range_max, 0) }}" maxlength="8" readonly disabled>
                                                            </div>
                                                        </td>

                                                        <td class="money-amount-cell">
                                                            <div class="money-amount-container">
                                                                <input type="text" name="tariff_amount[{{ $row->service_id }}][{{ $row->exp_range_id }}]" class="form-control form-control-sm tariff-input coverage-percent text-end money-value" value="{{ $row->coverage_percent }}" maxlength="4" readonly disabled>
                                                                <span class="money-currency fw-bolder pe-1">%</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button" class="btn btn-sm btn-primary btn-add-row" data-service-id="{{ $row->service_id }}" title="Add a row below.">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger btn-remove-row" data-service-id="{{ $row->service_id }}" title="Remove this row." {{ count($serviceLists[$type] ?? []) <= 1 ? 'disabled' : '' }}>
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                <input type="hidden" name="row_ids[]" value="{{ $row->exp_range_id ?? "new-{$loop->index}" }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.edit-tariff')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
