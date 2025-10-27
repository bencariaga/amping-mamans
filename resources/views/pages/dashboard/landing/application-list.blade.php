@extends('layouts.personal-pages')

@section('title', 'Application List')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/application-list.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/landing/application-list.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;
    <a href="{{ route('applications.list') }}" class="text-decoration-none text-white">Applications</a>
@endsection

@section('content')
    <div id="application-list-page" class="container">
        <div id="application-filter-controls">
            <form id="application-filter-form" method="GET" action="{{ route('applications.list') }}">
                <div class="controls-grid">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
                        <div class="filter-search-box me-4">
                            <div class="input-group" id="searchBtn">
                                <input type="text" id="filter-search" name="search" class="form-control fw-bold" placeholder="Search..." value="{{ request('search') }}">
                                <button class="btn btn-primary fw-bold" type="submit">Search</button>
                            </div>
                        </div>

                        <div class="filter-sort-wrapper d-flex align-items-center gap-2">
                            <label for="sortDropdownBtn" class="form-label fw-bold mb-0">Sort:</label>
                            <div class="dropdown filter-sort-dropdown">
                                <button id="sortDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ collect([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
                                        ['value'=>'applicant_asc','display'=>'Applicant (A-Z)'],
                                        ['value'=>'applicant_desc','display'=>'Applicant (Z-A)'],
                                        ['value'=>'amount_asc','display'=>'Amount (Lowest)'],
                                        ['value'=>'amount_desc','display'=>'Amount (Highest)'],
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
                                        ['value'=>'applicant_asc','display'=>'Applicant (A-Z)'],
                                        ['value'=>'applicant_desc','display'=>'Applicant (Z-A)'],
                                        ['value'=>'amount_asc','display'=>'Amount (Lowest)'],
                                        ['value'=>'amount_desc','display'=>'Amount (Highest)'],
                                    ] as $option)
                                    <li>
                                        <a class="dropdown-item @if(request('sort_by','latest') == $option['value']) active @endif" href="#" data-value="{{ $option['value'] }}">
                                            {{ $option['display'] }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="sort_by" id="filter-sort-by" value="{{ request('sort_by','latest') }}">
                            </div>
                        </div>

                        <div class="filter-per-page-wrapper d-flex align-items-center gap-2">
                            <label for="perPageDropdownBtn" class="form-label fw-bold mb-0">Rows:</label>
                            <div class="dropdown filter-per-page-dropdown">
                                <button id="perPageDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('per_page',5) }}
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="perPageDropdownBtn">
                                    @foreach([5, 10, 20, 'all'] as $n)
                                    <li>
                                        <a class="dropdown-item @if(request('per_page', 5) == $n) active @endif" href="#" data-value="{{ $n }}">{{ ucfirst($n) }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="per_page" id="filter-per-page" value="{{ request('per_page', 5) }}">
                            </div>
                        </div>

                        <div id="application-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($applications instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="fw-bold" id="pagination-page-info">{{ $applications->firstItem() }}-{{ $applications->lastItem() }} of {{ $applications->total() }} items</span>
                            <a href="{{ $applications->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$applications->onFirstPage()) active @else disabled @endif">Previous</a>
                            <a href="{{ $applications->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($applications->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                            <span class="fw-bold" id="pagination-page-info">All {{ count($applications) }} items</span>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Previous</a>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Next</a>
                            @endif
                        </div>
                    </div>
                </div>

                @foreach(request()->except(['page','per_page','sort_by','search']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
            </form>
        </div>

        <div class="data-table-container shadow-sm">
            <div class="table-responsive">
                <table class="application-table">
                    <thead>
                        <tr>
                            <th class="text-center application-table-header">Applicant Name</th>
                            <th class="text-center application-table-header">Service Type</th>
                            <th class="text-center application-table-header">Billed Amount</th>
                            <th class="text-center application-table-header">Assistance</th>
                            <th class="text-center application-table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            @php
                                $last = optional($application->applicant->client->member)->last_name ?: '';
                                $first = optional($application->applicant->client->member)->first_name ?: '';
                                $middle = optional($application->applicant->client->member)->middle_name ?: '';
                                $suffix = optional($application->applicant->client->member)->suffix ?: '';

                                $middleInitial = $middle !== '' ? strtoupper(substr(trim($middle), 0, 1)) . '.' : '';
                                $parts = [];

                                if ($last !== '') $parts[] = $last . ',';
                                if ($first !== '') $parts[] = $first;
                                if ($middleInitial !== '') $parts[] = $middleInitial;
                                if ($suffix !== '') $parts[] = $suffix;

                                $name = trim(implode(' ', $parts));
                            @endphp

                            <tr class="{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-4 py-2 text-center">{{ $name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-center">{{ $application->expenseRange->service->service_type ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-center">₱ {{ number_format($application->billed_amount) }}</td>
                                <td class="px-3 py-2 text-center">₱ {{ number_format($application->assistance_amount) }}</td>

                                <td class="py-2 text-center action-buttons">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('applications.destroy', $application->application_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger px-3 py-2 delete-btn" onclick="return confirm('Are you sure you want to delete this service assistance request?')">Delete</button>
                                        </form>

                                        <button class="btn btn-sm btn-secondary px-3 py-2 details-btn" data-application-id="{{ $application->application_id }}">Details</button>
                                        <a class="btn btn-sm btn-primary px-3 py-2 preview-btn" href="{{ route('applications.guarantee-letter.pdf', ['application' => $application->application_id]) }}" data-application-id="{{ $application->application_id }}" target="_blank">Guarantee Letter</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="detailsModal" style="display: none;">
        <div class="modal-container" id="modal-container">
            <div class="modal-header">
                <h2>Application Details</h2>
                <button class="modal-close" onclick="closeModal('detailsModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 005.7 7.11L10.59 12 5.7 16.89a1 1 0 101.41 1.41L12 13.41l4.89 4.89a1 1 0 001.41-1.41L13.41 12l4.89-4.89a1 1 0 000-1.4z"/></svg>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal('detailsModal')">OKAY, I UNDERSTAND</button>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-application')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
