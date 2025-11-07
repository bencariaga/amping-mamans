@extends('layouts.personal-pages')

@section('title', 'Edit Tariff List Services')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-list-view.css') }}" rel="stylesheet">
    <style>
        #editServicesBtn,
        #saveServicesBtn {
            height: 4rem !important;
            display: flex !important;
            align-items: center !important;
        }
        
        #editServicesBtn .nav-text,
        #saveServicesBtn .nav-text {
            line-height: 1.3 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/pages/tariff-list/tariff-list-edit-services.js') }}" defer></script>
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
                <span class="edit-mode" style="display: none; color: #dc3545;">Currently Editing Services</span>
            </h3>
        </div>

        <ul class="nav nav-tabs mb-4" id="serviceTabs" role="tablist">
            @foreach($usedServices as $index => $service)
                <li class="nav-item" role="presentation" data-service-type="{{ $service->service_type }}" data-service-id="{{ $service->service_id }}">
                    <div class="service-tab-wrapper">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#service-{{ $index }}" type="button" role="tab" aria-controls="service-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $service->service_type }}
                        </button>

                        <button type="button" 
                                class="btn-remove-service" 
                                data-service-type="{{ $service->service_type }}" 
                                data-service-id="{{ $service->service_id }}" 
                                title="Remove this service type"
                                style="display: none;"
                                {{ $usedServices->count() <= 1 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </li>
            @endforeach

            <li class="nav-item" role="presentation" id="addServiceDropdownContainer" style="display: none;">
                <select class="form-select service-type-dropdown" id="addServiceDropdown">
                    <option value="" selected>— Select Service —</option>
                    @foreach($availableServices as $service)
                        <option value="{{ $service->service_id }}">{{ $service->service_type }}</option>
                    @endforeach
                </select>
            </li>
        </ul>

        <div class="tab-content" id="serviceTabsContent">
            @foreach($usedServices as $index => $service)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="service-{{ $index }}" role="tabpanel" aria-labelledby="tab-{{ $index }}">
                    <div class="alert alert-info view-mode">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Service: {{ $service->service_type }}</strong><br>
                        Click "Edit Services" button below to add or remove services.
                    </div>
                    <div class="alert alert-info edit-mode" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Service: {{ $service->service_type }}</strong><br>
                        You can add or remove services using the buttons above.
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <input type="hidden" id="tariffListId" value="{{ $tariffListModel->tariff_list_id }}">
@endsection

@section('footer')
    <a href="#" class="footer-btn view-mode" id="editServicesBtn">
        <div class="nav-icon"><i class="fas fa-edit"></i></div>
        <div class="nav-text">Edit<br>Services</div>
    </a>

    <a href="#" class="footer-btn edit-mode" id="saveServicesBtn" style="display: none;">
        <div class="nav-icon"><i class="fas fa-save"></i></div>
        <div class="nav-text">Save<br>Changes</div>
    </a>

    @include('components.layouts.footer.profile-buttons-2')
@endsection
