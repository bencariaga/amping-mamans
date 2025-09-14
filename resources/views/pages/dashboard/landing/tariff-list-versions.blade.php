@extends('layouts.personal-pages')

@section('title', 'Tariff List Versions')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/tariff-list-versions.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/landing/tariff-list-versions.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a> &gt;
    <a href="{{ route('tariff-lists.versions.show') }}" class="text-decoration-none text-reset">Tariff Lists</a>
@endsection

@section('content')
    <div class="container pt-3">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <div class="col">
                <a href="{{ route('tariff-lists.create') }}" class="add-card-link text-decoration-none">
                    <div class="card add-card h-100" id="addCard">
                        <div class="card-body">
                            <span class="d-flex align-items-center justify-content-center text-center w-100 h-100 fs-1 text-secondary">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            @foreach($groupedTariffs as $data_id => $services)
                @php
                    $tariffModel = $tariffModels[$data_id];
                    $tariffUrl = route('tariff-lists.preview', ['tariff_list_id' => $tariffModel->tariff_list_id]);
                @endphp

                <div class="col">
                    <div class="card h-100 {{ $tariffModel->effectivity_status == 'Effective' ? 'border border-primary border-3' : '' }}">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-truncate mb-0">{{ $tariffModel->tariff_list_id }}</h6>

                            @if($tariffModel->effectivity_status == 'Effective')
                                <span class="badge bg-primary text-white ms-2 d-flex align-items-center">EFFECTIVE</span>
                            @endif
                        </div>

                        <div class="card-body d-block flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-truncate mb-2">Effectivity Date:</h6>
                                <span class="text-end ms-2 mb-2">{{ \Carbon\Carbon::parse($tariffModel->effectivity_date)->format('F d, Y') }}</span>
                            </div>

                            <div class="mt-auto mb-3" id="viewDetailsBtn">
                                <a href="{{ route('tariff-lists.tables.show', ['tariff_list_id' => $tariffModel->tariff_list_id]) }}" class="btn btn-primary btn-sm w-100">VIEW DETAILS</a>
                            </div>

                            <ul class="list-unstyled d-grid gap-2">
                                @forelse($services as $service)
                                    <li>
                                        <a href="#" style="display: flex; justify-content: center; align-items: center;" class="service-item service-modal-trigger text-decoration-none" data-tariff-url="{{ $tariffUrl }}" data-service="{{ $service }}">{{ $service }}</a>
                                    </li>
                                @empty
                                    <li><span class="text-muted">No services assigned.</span></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="tariffServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" id="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" id="btn-close-modal" data-bs-dismiss="modal" aria-label="close"></button>
                </div>

                <div class="modal-body p-0" id="modal-body"></div>

                <div class="modal-footer" id="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
