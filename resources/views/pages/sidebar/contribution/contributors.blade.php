@extends('layouts.personal-pages')

@section('title', 'Sponsors')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/contribution/contributors.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/contribution/contributors.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('sponsors.list') }}" class="text-decoration-none text-white">Sponsors</a>
@endsection

@section('content')
    <div class="container">
        <div id="sponsor-filter-controls">
            <form id="sponsor-filter-form" method="GET" action="{{ route('sponsors.list') }}">
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
                                        ['value'=>'latest', 'display'=>'Latest'],
                                        ['value'=>'oldest', 'display'=>'Oldest'],
                                        ['value'=>'name_asc', 'display'=>'Name'],
                                        ['value'=>'type_asc', 'display'=>'Type'],
                                        ['value'=>'designation_asc', 'display'=>'Designation'],
                                        ['value'=>'amount_asc', 'display'=>'Total Amount'],
                                    ])->firstWhere('value', request('sort_by', 'latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest', 'display'=>'Latest'],
                                        ['value'=>'oldest', 'display'=>'Oldest'],
                                        ['value'=>'name_asc', 'display'=>'Name'],
                                        ['value'=>'type_asc', 'display'=>'Type'],
                                        ['value'=>'designation_asc', 'display'=>'Designation'],
                                        ['value'=>'amount_asc', 'display'=>'Total Amount'],
                                    ] as $opt)
                                        <li>
                                            <a class="dropdown-item @if(request('sort_by', 'latest') == $opt['value']) active @endif" href="#" data-value="{{ $opt['value'] }}">
                                                {{ $opt['display'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" id="filter-sort-by" name="sort_by" value="{{ request('sort_by', 'latest') }}">
                            </div>
                        </div>

                        <div class="filter-per-page-wrapper d-flex align-items-center gap-2">
                            <label for="perPageDropdownBtn" class="form-label fw-bold mb-0">Rows:</label>
                            <div class="dropdown filter-per-page-dropdown">
                                <button id="perPageDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('per_page', 5) }}
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

                        <div id="sponsor-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($sponsors instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <span class="fw-bold">{{ $sponsors->firstItem() }}-{{ $sponsors->lastItem() }} of {{ $sponsors->total() }} items</span>
                                <a href="{{ $sponsors->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$sponsors->onFirstPage()) active @else disabled @endif">Previous</a>
                                <a href="{{ $sponsors->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($sponsors->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                                <span class="fw-bold">All {{ count($sponsors) }} items</span>
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
                <table class="sponsor-table">
                    <thead>
                        <tr>
                            <th class="text-center sponsor-table-header">Sponsor Name</th>
                            <th class="text-center sponsor-table-header">Sponsor Type</th>
                            <th class="text-center sponsor-table-header">Designation</th>
                            <th class="text-center sponsor-table-header">Total Amount Contributed</th>
                            <th class="text-center sponsor-table-header">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($sponsors as $sponsor)
                            @php
                                $last = optional($sponsor->member)->last_name ?? '';
                                $first = optional($sponsor->member)->first_name ?? '';
                                $middle = optional($sponsor->member)->middle_name ?? '';
                                $suffix = optional($sponsor->member)->suffix ?? '';

                                $middleInitial = $middle !== '' ? strtoupper(substr(trim($middle), 0, 1)) . '.' : '';

                                $parts = [];

                                if ($last !== '') $parts[] = $last . ',';
                                if ($first !== '') $parts[] = $first;
                                if ($middleInitial !== '') $parts[] = $middleInitial;
                                if ($suffix !== '') $parts[] = $suffix;

                                $name = trim(implode(' ', $parts));
                                $total_amount = number_format(isset($sponsor->total_amount_contributed) && $sponsor->total_amount_contributed !== null ? (float) $sponsor->total_amount_contributed : 0, 0);
                            @endphp

                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-4 py-2 text-left">{{ $name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-center">{{ $sponsor->sponsor_type ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-center">{{ $sponsor->designation ?? 'N/A' }}</td>
                                <td class="py-2 text-left" style="padding-left: 72.5px;">₱ {{ $total_amount ?? 'N/A' }}</td>

                                <td class="py-2 text-center action-buttons">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('sponsors.tables.show', ['id' => $sponsor->sponsor_id]) }}" class="btn btn-sm btn-primary px-3 py-2">Show Contributions</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-sponsor')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
