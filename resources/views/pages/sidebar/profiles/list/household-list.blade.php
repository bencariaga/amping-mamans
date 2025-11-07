@extends('layouts.personal-pages')

@section('title', 'Households')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/list/household-list.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/list/household-list.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-white">Applicants</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.households.list') }}" class="text-decoration-none text-white">Households</a>
@endsection

@section('content')
    <div class="container">
        <div id="household-filter-controls">
            <form id="household-filter-form" method="GET" action="{{ route('profiles.households.list') }}">
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
                                        ['value'=>'latest',          'display'=>'Latest'],
                                        ['value'=>'oldest',          'display'=>'Oldest'],
                                        ['value'=>'last_name_asc',   'display'=>'Surname'],
                                        ['value'=>'occupation_asc',  'display'=>'Occupation'],
                                        ['value'=>'phone_asc',       'display'=>'Phone No.'],
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest',          'display'=>'Latest'],
                                        ['value'=>'oldest',          'display'=>'Oldest'],
                                        ['value'=>'last_name_asc',   'display'=>'Surname'],
                                        ['value'=>'occupation_asc',  'display'=>'Occupation'],
                                        ['value'=>'phone_asc',       'display'=>'Phone No.'],
                                    ] as $opt)
                                        <li>
                                            <a class="dropdown-item @if(request('sort_by','latest') == $opt['value']) active @endif" href="#" data-value="{{ $opt['value'] }}">
                                                {{ $opt['display'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" id="filter-sort-by" name="sort_by" value="{{ request('sort_by','latest') }}">
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
                                <input type="hidden" id="filter-per-page" name="per_page" value="{{ request('per_page', 5) }}">
                            </div>
                        </div>

                        <div id="household-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($households instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <span class="fw-bold">{{ $households->firstItem() }}-{{ $households->lastItem() }} of {{ $households->total() }} items</span>
                                <a href="{{ $households->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$households->onFirstPage()) active @else disabled @endif">Previous</a>
                                <a href="{{ $households->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($households->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                                <span class="fw-bold">All {{ count($households) }} items</span>
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
                <table class="household-table">
                    <thead>
                        <tr>
                            <th class="text-center household-table-header">Family / Household Name</th>
                            <th class="text-center household-table-header">First Applicant Registered</th>
                            <th class="text-center household-table-header">First Patient Registered</th>
                            <th class="text-center household-table-header">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($households as $household)
                            @php
                                $firstApplicant = $household->clients?->first();
                                $firstPatient = $household->clients?->patients?->first();

                                $applicantNameParts = $firstApplicant->member ?? null;
                                $applicantLastName = optional($applicantNameParts)->last_name ?: '';
                                $applicantFirstName = optional($applicantNameParts)->first_name ?: '';
                                $applicantMiddleName = optional($applicantNameParts)->middle_name ?: '';
                                $applicantSuffix = optional($applicantNameParts)->suffix ?: '';

                                $patientNameParts = $firstPatient->member ?? null;
                                $patientLastName = optional($patientNameParts)->last_name ?: '';
                                $patientFirstName = optional($patientNameParts)->first_name ?: '';
                                $patientMiddleName = optional($patientNameParts)->middle_name ?: '';
                                $patientSuffix = optional($patientNameParts)->suffix ?: '';

                                $applicantMiddleInitial = $applicantMiddleName !== '' ? strtoupper(substr(trim($applicantMiddleName), 0, 1)) . '.' : '';
                                $patientMiddleInitial = $patientMiddleName !== '' ? strtoupper(substr(trim($patientMiddleName), 0, 1)) . '.' : '';

                                $applicantParts = [];
                                $patientParts = [];

                                if ($applicantLastName !== '') $applicantParts[] = $applicantLastName . ',';
                                if ($applicantFirstName !== '') $applicantParts[] = $applicantFirstName;
                                if ($applicantMiddleInitial !== '') $applicantParts[] = $applicantMiddleInitial;
                                if ($applicantSuffix !== '') $applicantParts[] = $applicantSuffix;

                                if ($patientLastName !== '') $patientParts[] = $patientLastName . ',';
                                if ($patientFirstName !== '') $patientParts[] = $patientFirstName;
                                if ($patientMiddleInitial !== '') $patientParts[] = $patientMiddleInitial;
                                if ($patientSuffix !== '') $patientParts[] = $patientSuffix;

                                $applicantName = trim(implode(' ', $applicantParts));
                                $patientName = trim(implode(' ', $patientParts));
                            @endphp

                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-4 py-2 text-center fw-bold">{{ $household->household_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-left">{{ $applicantName ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-left">{{ $patientName ?? 'N/A' }}</td>
                                <td class="px-0 py-2 text-center action-buttons">
                                    <div class="gap-3 d-flex justify-content-center">
                                        <a href="{{ route('profiles.households.show', ['household' => $household->household_id]) }}" class="btn btn-sm btn-primary px-4 py-2">Edit Household</a>
                                        <form action="{{ route('profiles.households.destroy', $household->household_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger px-4 py-2 delete-btn" onclick="return confirm('Are you sure you want to delete this family / household?')">Delete Household</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include('pages.sidebar.profiles.register.household')
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-household')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
