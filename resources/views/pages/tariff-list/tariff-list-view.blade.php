@extends('layouts.personal-pages')

@section('title', 'Edit Tariff List Version')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-list-view.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/tariff-list/tariff-list-view.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span
        class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span
            class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists') }}" class="text-decoration-none text-white">Tariff List Versions</a><span
        class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span
            class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists.view', $tariffListModel->tariff_list_id) }}"
        class="text-decoration-none text-reset">{{ $tariffListModel->tariff_list_id }}</a>
@endsection

@section('content')
    <div class="container" id="container">
        <form id="tariffForm" class="mb-5" method="POST" action="{{ route('tariff-lists.update', $tariffListModel->tariff_list_id) }}">
            @csrf
            @method('PUT')

            <div class="service-info">
                <h3 class="service-count">TL Version's Number of Services: {{ count($serviceLists) }}</h3>
                <h3 class="service-status">
                    <span class="view-mode">Currently Viewing: <span
                            id="currentService">{{ $serviceTypes[0] ?? 'No Service' }}</span></span>
                    <span class="edit-mode" style="display: none;">Currently Editing: <span
                            id="editingService">{{ $serviceTypes[0] ?? 'No Service' }}</span></span>
                </h3>
            </div>

            <ul class="nav nav-tabs mb-4" id="serviceTabs" role="tablist">
                @foreach($serviceTypes as $index => $type)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $index }}" data-bs-toggle="tab"
                            data-bs-target="#service-{{ $index }}" type="button" role="tab" aria-controls="service-{{ $index }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $type }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="serviceTabsContent">
                @foreach($serviceTypes as $index => $type)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="service-{{ $index }}" role="tabpanel"
                        aria-labelledby="tab-{{ $index }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="shadow-sm tariff-section p-3 mx-auto">
                                    <div class="table-responsive">
                                        <table class="expense-table w-100">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="money-amount-header text-center"
                                                        id="money-amount-header-1">Expense Range</th>
                                                    <th rowspan="2" class="money-amount-header text-center"
                                                        id="money-amount-header-2">Coverage<br>(%)</th>
                                                    <th rowspan="2" class="money-amount-header text-center"
                                                        id="money-amount-header-3">Actions</th>
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
                                                                <input type="text" name="range_min[{{ $row->service_id }}][{{ $row->exp_range_id }}]"
                                                                    class="form-control form-control-sm range-input text-end money-value"
                                                                    value="{{ number_format($row->exp_range_min, 0) }}"
                                                                    maxlength="8" readonly>
                                                            </div>
                                                        </td>

                                                        <td class="money-amount-cell">
                                                            <div class="money-amount-container">
                                                                <span class="money-currency fw-bold pe-2">₱</span>
                                                                <input type="text" name="range_max[{{ $row->service_id }}][{{ $row->exp_range_id }}]"
                                                                    class="form-control form-control-sm range-input text-end money-value"
                                                                    value="{{ number_format($row->exp_range_max, 0) }}"
                                                                    maxlength="8" readonly>
                                                            </div>
                                                        </td>

                                                        <td class="money-amount-cell">
                                                            <div class="money-amount-container">
                                                                <input type="text" name="tariff_amount[{{ $row->service_id }}][{{ $row->exp_range_id }}]"
                                                                    class="form-control form-control-sm tariff-input text-end money-value"
                                                                    value="{{ $row->coverage_percent }}"
                                                                    maxlength="4" readonly>
                                                                <span class="money-currency fw-bolder pe-1">%</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button" class="btn btn-sm btn-primary btn-add-row"
                                                                    data-service-id="{{ $row->service_id }}"
                                                                    title="Add a row below.">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger btn-remove-row"
                                                                    data-service-id="{{ $row->service_id }}"
                                                                    title="Remove this row." {{ count($serviceLists[$type] ?? []) <= 1 ? 'disabled' : '' }}>
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                <input type="hidden" name="row_ids[]"
                                                                    value="{{ $row->exp_range_id ?? "new-{$loop->index}" }}">
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
