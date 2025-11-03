@extends('layouts.personal-pages')

@section('title', 'Editing Tariff List Version\'s Services')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-list-view.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/tariff-list/tariff-list-services.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists') }}" class="text-decoration-none text-white">Tariff List Versions</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('tariff-lists.edit', $tariffListModel->tariff_list_id) }}" class="text-decoration-none text-reset">{{ $tariffListModel->tariff_list_id }}</a>
@endsection

@section('content')
    <div class="container" id="container">
        <div class="service-info">
            <h3 class="service-count">TL Version's Number of Services: {{ $usedServices->count() }}</h3>

            <h3 class="service-status">
                <span class="view-mode">Currently Viewing Services</span>
                <span class="edit-mode" style="display: none;">Currently Editing Services</span>
            </h3>
        </div>

        <ul class="nav nav-tabs mb-4" id="serviceTabs" role="tablist">
            @foreach($usedServices as $index => $service)
                <li class="nav-item" role="presentation" data-service-type="{{ $service->service }}" data-service-id="{{ $service->service_id }}">
                    <div class="service-tab-wrapper">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#service-{{ $index }}" type="button" role="tab" aria-controls="service-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $service->service }}
                        </button>

                        <button type="button" class="btn-remove-service" data-service-type="{{ $service->service }}" data-service-id="{{ $service->service_id }}" title="Remove this service type" style="display: none;" {{ $usedServices->count() <= 1 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </li>
            @endforeach

            <li class="nav-item add-service-tab" role="presentation" id="addServiceDropdownContainer" style="display: none;">
                <div class="dropdown w-100">
                    <select class="form-select service-type-dropdown custom-dropdown-btn fw-bold w-100" id="addServiceDropdown">
                        <option value="" selected>— Select Service —</option>
                        @foreach($availableServices as $service)
                            <option value="{{ $service->service_id }}">{{ $service->service }}</option>
                        @endforeach
                    </select>
                </div>
            </li>
        </ul>

        <div class="tab-content" id="serviceTabsContent">
            @foreach($usedServices as $index => $service)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="service-{{ $index }}" role="tabpanel" aria-labelledby="tab-{{ $index }}">
                    <div class="alert alert-info view-mode" id="alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Service: {{ $service->service }}</strong><br>
                        Click "Edit Services" button below to add or remove services.
                    </div>

                    <div class="alert alert-info edit-mode" id="alert-info" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Service: {{ $service->service }}</strong><br>
                        You can add or remove services using the buttons above.
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <input type="hidden" id="tariffListId" value="{{ $tariffListModel->tariff_list_id }}">
@endsection

@section('footer')
    @include('components.layouts.footer.edit-tariff-services')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
