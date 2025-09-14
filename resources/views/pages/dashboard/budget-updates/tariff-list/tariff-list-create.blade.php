@extends('layouts.personal-pages')

@section('title', 'Create Tariff List Version')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/budget-updates/tariff-list/tariff-list-create.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/budget-updates/tariff-list/tariff-list-create.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a> &gt;
    <a href="{{ route('tariff-lists.versions.show') }}" class="text-decoration-none text-reset">Tariff Lists</a> &gt;
    <a href="{{ route('tariff-lists.create') }}" class="text-decoration-none text-reset">Create</a>
@endsection

@section('content')
    <div class="container pt-3 pb-4">
        <form action="{{ route('tariff-lists.store') }}" method="POST" id="tariffCreateForm">
            @csrf
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="create-section">
                        <div class="create-header">
                            <legend class="form-legend">
                                <i class="fas fa-list-alt fa-fw"></i><span class="header-title">Create New Tariff List Version</span>
                            </legend>
                        </div>

                        <div class="form-content">
                            <div class="checkbox-row">
                                <input type="checkbox" name="apply_version" value="1" id="apply-version" class="checkbox" checked>
                                <label class="ms-1" id="applyTariffListVersionNow" for="apply-version">Apply this version upon creation and saving</label>
                            </div>

                            <div class="date-row">
                                <label for="effectivity-date">Effectivity Date:</label>
                                <div class="date-input-container">
                                    <input type="date" id="effectivity-date" name="effectivity_date" class="date-input @error('effectivity_date') is-invalid @enderror" value="{{ old('effectivity_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            @error('effectivity_date')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="services-container">
                                @foreach($services as $service)
                                    <div class="service-row">
                                        <input type="checkbox" name="services[]" value="{{ $service->service_id }}" id="service_{{ $service->service_id }}" class="service-checkbox selector-checkbox" data-service-type="{{ $service->service_type }}" {{ isset($tariffLists[$service->service_type]) || old('services') && in_array($service->service_id, old('services')) ? 'checked' : '' }}>
                                        <a href="#" class="service-label" id="serviceLabel_{{ $service->service_id }}" data-service-type="{{ $service->service_type }}">{{ $service->service_type }}</a>
                                    </div>
                                @endforeach
                            </div>

                            @error('services')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="button-row">
                                <a href="{{ route('tariff-lists.versions.show') }}" class="btn btn-secondary cancel-btn">CANCEL</a>
                                <button type="submit" class="btn btn-primary create-btn">CREATE</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 p-0 mx-0" id="tariffListContainer">
                    <div id="tariffFormWrapper" class="mb-4">
                        <div id="tariffCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($services as $service)
                                    <div class="carousel-item @if($loop->first) active @endif" data-service-type="{{ $service->service_type }}">
                                        <div class="tariff-section p-3 h-100" id="tariffSection_{{ $service->service_id }}">
                                            <h2 class="section-title fw-bold text-center">
                                                <button class="nav-arrow carousel-control-prev" type="button" data-bs-target="#tariffCarousel" data-bs-slide="prev" aria-label="previous">◀</button>
                                                {{ $service->service_type }}
                                                <button class="nav-arrow carousel-control-next" type="button" data-bs-target="#tariffCarousel" data-bs-slide="next" aria-bs-slide="next" aria-label="next">▶</button>
                                            </h2>

                                            <div class="table-responsive">
                                                <table class="tariff-list-table">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2" class="money-amount-header text-center">Expense Range</th>
                                                            <th rowspan="2" class="money-amount-header text-center">Monetary<br>Assistance<br>Amount</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="money-amount-header text-center">Minimum</th>
                                                            <th class="money-amount-header text-center">Maximum</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody class="service-rows" data-service-id="{{ $service->service_id }}" data-service-type="{{ $service->service_type }}">
                                                        <tr class="money-amount-row">
                                                            <td class="money-amount-cell">
                                                                <div class="money-amount-container">
                                                                    <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                                                                    <span class="money-currency fw-bold">₱</span>
                                                                    <input type="number" step="0.01" name="range_min_{{ $service->service_id }}[]" class="form-control form-control-sm range-input range-min-input text-end money-value" value="0.00">
                                                                </div>
                                                            </td>

                                                            <td class="money-amount-cell">
                                                                <div class="money-amount-container">
                                                                    <span class="money-currency fw-bold">₱</span>
                                                                    <input type="number" step="0.01" name="range_max_{{ $service->service_id }}[]" class="form-control form-control-sm range-input range-max-input text-end money-value" value="0.00">
                                                                    <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                                                                </div>
                                                            </td>

                                                            <td class="money-amount-cell">
                                                                <div class="money-amount-container">
                                                                    <span class="money-currency fw-bold">₱</span>
                                                                    <input type="number" step="0.01" name="tariff_amount_{{ $service->service_id }}[]" class="form-control form-control-sm tariff-input text-end money-value" value="0.00">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
