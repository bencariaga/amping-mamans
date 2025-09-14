@extends('layouts.personal-pages')

@section('title', 'Edit Tariff List Version')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/budget-updates/tariff-list/tariff-list-tables.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        window.tariffListConfig = {
            currentTariffListId: "{{ $tariffDisplayName }}",
            latestEffectiveTariffListId: "{{ $latestEffectiveTariffListId }}"
        };
    </script>

    <script src="{{ asset('js/pages/dashboard/budget-updates/tariff-list/tariff-list-tables.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a> &gt;
    <a href="{{ route('tariff-lists.versions.show') }}" class="text-decoration-none text-reset">Tariff Lists</a> &gt;
    <a href="{{ route('tariff-lists.tables.show', ['tariff_list_id' => $tariffDisplayName]) }}" class="text-decoration-none text-reset">This Version</a>
@endsection

@section('content')
    <div class="container pt-3 pb-4">
        <form action="{{ route('tariff-lists.update') }}" method="POST" id="tariffCreateForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="tariff_list_id" value="{{ $tariffModel->tariff_list_id }}">

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="create-section">
                        <div class="create-header">
                            <legend class="form-legend">
                                <i class="fas fa-list-alt fa-fw"></i><span class="header-title">Tariff List Version: {{ $tariffDisplayName }}</span>
                            </legend>
                        </div>

                        <div class="form-content">
                            <div class="checkbox-row">
                                <input type="checkbox" name="apply_version" value="1" id="apply-version" class="checkbox" {{ old('apply_version', $tariffModel->effectivity_status === 'Effective' ? 'checked' : '') }}>
                                <label class="ms-1" id="applyTariffListVersionNow" for="apply-version">Apply this version upon update and saving</label>
                            </div>

                            <div class="date-row">
                                <label for="effectivity-date">Effectivity Date:</label>
                                <div class="date-input-container">
                                    <input type="date" id="effectivity-date" name="effectivity_date" class="date-input @error('effectivity_date') is-invalid @enderror" value="{{ old('effectivity_date', $tariffModel->effectivity_date) }}" min="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            @error('effectivity_date')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="services-container">
                                @foreach($services as $service)
                                    <div class="service-row">
                                        <input type="checkbox" name="services[]" value="{{ $service->service_id }}" id="service_{{ $service->service_id }}" class="service-checkbox selector-checkbox" data-service-type="{{ $service->service_type }}" {{ (isset($tariffLists[$service->service_type]) && $tariffLists[$service->service_type]->isNotEmpty()) || (old('services') && in_array($service->service_id, old('services'))) ? 'checked' : '' }}>
                                        <a href="#" class="service-label" id="serviceLabel_{{ $service->service_id }}" data-service-type="{{ $service->service_type }}">{{ $service->service_type }}</a>
                                    </div>
                                @endforeach
                            </div>

                            @error('services')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="button-row">
                                <button type="button" class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteTariffListModal">DELETE</button>
                                <a href="{{ route('tariff-lists.versions.show') }}" class="btn btn-secondary cancel-btn">CANCEL</a>
                                <button type="submit" class="btn btn-primary create-btn">UPDATE</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 p-0 mx-0" id="tariffListContainer">
                    <div id="tariffFormWrapper" class="mb-4">
                        <div id="tariffCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($services as $service)
                                    @php
                                        $serviceType = $service->service_type;
                                        $serviceIdForAttr = $service->service_id;
                                        $tariffs = $tariffLists->has($serviceType) ? $tariffLists[$serviceType] : collect();
                                    @endphp
                                    <div class="carousel-item @if($loop->first) active @endif" data-service-type="{{ $serviceType }}" data-service-id="{{ $serviceIdForAttr }}">
                                        <div class="tariff-section p-3 h-100">
                                            <h2 class="section-title fw-bold text-center">
                                                <button class="nav-arrow carousel-control-prev" type="button" data-bs-target="#tariffCarousel" data-bs-slide="prev" aria-label="previous">◀</button>
                                                {{ $serviceType }}
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

                                                    <tbody class="service-rows" data-service-type="{{ $serviceType }}" data-service-id="{{ $serviceIdForAttr }}">
                                                        @if($tariffs->isNotEmpty())
                                                            @foreach($tariffs as $tariff)
                                                                <tr class="money-amount-row" data-expid="{{ $tariff->exp_range_id }}">
                                                                    <td class="money-amount-cell">
                                                                        <div class="money-amount-container">
                                                                            <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                                                                            <span class="money-currency fw-bold">₱</span>
                                                                            <input type="number" step="0.01" name="range_min[{{ $tariff->exp_range_id }}]" class="form-control form-control-sm range-input range-min-input text-end money-value" value="{{ $tariff->exp_range_min }}">
                                                                        </div>
                                                                    </td>

                                                                    <td class="money-amount-cell">
                                                                        <div class="money-amount-container">
                                                                            <span class="money-currency fw-bold">₱</span>
                                                                            <input type="number" step="0.01" name="range_max[{{ $tariff->exp_range_id }}]" class="form-control form-control-sm range-input range-max-input text-end money-value" value="{{ $tariff->exp_range_max }}">
                                                                            <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                                                                        </div>
                                                                    </td>

                                                                    <td class="money-amount-cell">
                                                                        <div class="money-amount-container">
                                                                            <span class="money-currency fw-bold">₱</span>
                                                                            <input type="number" step="0.01" name="tariff_amount[{{ $tariff->exp_range_id }}]" class="form-control form-control-sm tariff-input text-end money-value" value="{{ $tariff->assist_amount }}">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr class="money-amount-row">
                                                                <td class="money-amount-cell">
                                                                    <div class="money-amount-container">
                                                                        <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                                                                        <span class="money-currency fw-bold">₱</span>
                                                                        <input type="number" step="0.01" name="range_min_new[{{ $serviceIdForAttr }}][]" class="form-control form-control-sm range-input range-min-input text-end money-value" value="0.00">
                                                                    </div>
                                                                </td>

                                                                <td class="money-amount-cell">
                                                                    <div class="money-amount-container">
                                                                        <span class="money-currency fw-bold">₱</span>
                                                                        <input type="number" step="0.01" name="range_max_new[{{ $serviceIdForAttr }}][]" class="form-control form-control-sm range-input range-max-input text-end money-value" value="0.00">
                                                                        <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                                                                    </div>
                                                                </td>

                                                                <td class="money-amount-cell">
                                                                    <div class="money-amount-container">
                                                                        <span class="money-currency fw-bold">₱</span>
                                                                        <input type="number" step="0.01" name="tariff_amount_new[{{ $serviceIdForAttr }}][]" class="form-control form-control-sm tariff-input text-end money-value" value="0.00">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
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

    <div class="modal fade" id="deleteTariffListModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('tariff-lists.destroy', ['tariff_list_id' => $tariffDisplayName]) }}" class="delete-tariff-list-modal">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Tariff List Version</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="modal-label">Are you sure you want to delete this tariff list version? This action cannot be undone.</label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-danger delete-btn">DELETE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
