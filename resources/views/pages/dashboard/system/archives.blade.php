@extends('layouts.personal-pages')

@section('title', 'Archives')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/system/archives.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/system/archives.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;
    <a href="{{ route('archives.list') }}" class="text-decoration-none text-white">Archives</a>
@endsection

@section('content')
    <div id="archives-page" class="container">
        <div id="archives-filter-controls">
            <form id="archives-filter-form" method="GET" action="{{ route('archives.list') }}">
                <div class="controls-grid">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
                        <div class="filter-search-box me-4">
                            <div class="input-group" id="searchBtn">
                                <input type="text" id="filter-search" name="search" class="form-control fw-bold" placeholder="Search..." value="{{ request('search') }}">
                                <button class="btn btn-primary fw-bold" type="submit" style="height: 40px;">Search</button>
                            </div>
                        </div>

                        <div class="filter-type-wrapper d-flex align-items-center gap-2">
                            <label for="typeDropdownBtn" class="form-label fw-bold mb-0">Type:</label>
                            <div class="dropdown filter-type-dropdown">
                                <button id="typeDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ collect([
                                        ['value'=>'all','display'=>'All'],
                                        ['value'=>'applications','display'=>'Applications'],
                                        ['value'=>'tariff_lists','display'=>'Tariff List Versions'],
                                        ['value'=>'message_templates','display'=>'Message Templates'],
                                        ['value'=>'roles','display'=>'Roles'],
                                        ['value'=>'occupations','display'=>'Occupations'],
                                        ['value'=>'services','display'=>'Services'],
                                    ])->firstWhere('value', request('type','all'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="typeDropdownBtn">
                                    @foreach([
                                        ['value'=>'all','display'=>'All'],
                                        ['value'=>'applications','display'=>'Applications'],
                                        ['value'=>'tariff_lists','display'=>'Tariff List Versions'],
                                        ['value'=>'message_templates','display'=>'Message Templates'],
                                        ['value'=>'roles','display'=>'Roles'],
                                        ['value'=>'occupations','display'=>'Occupations'],
                                        ['value'=>'services','display'=>'Services'],
                                    ] as $option)
                                    <li>
                                        <a class="dropdown-item @if(request('type','all') == $option['value']) active @endif" href="#" data-value="{{ $option['value'] }}">
                                            {{ $option['display'] }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="type" id="filter-type" value="{{ request('type','all') }}">
                            </div>
                        </div>

                        <div class="filter-sort-wrapper d-flex align-items-center gap-2">
                            <label for="sortDropdownBtn" class="form-label fw-bold mb-0">Sort:</label>
                            <div class="dropdown filter-sort-dropdown">
                                <button id="sortDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ collect([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
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

                        <div id="archives-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($archives instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="fw-bold" id="pagination-page-info">{{ $archives->firstItem() }}-{{ $archives->lastItem() }} of {{ $archives->total() }} items</span>
                            <a href="{{ $archives->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$archives->onFirstPage()) active @else disabled @endif">Previous</a>
                            <a href="{{ $archives->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($archives->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                            <span class="fw-bold" id="pagination-page-info">All {{ count($archives) }} items</span>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Previous</a>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Next</a>
                            @endif
                        </div>
                    </div>
                </div>

                @foreach(request()->except(['page','per_page','sort_by','search','type']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
            </form>
        </div>

        <div class="data-table-container shadow-sm">
            <div class="table-responsive">
                <table class="archives-table">
                    <thead>
                        <tr>
                            <th class="text-center archives-table-header">Data Type</th>
                            <th class="text-center archives-table-header">Name / Title</th>
                            <th class="text-center archives-table-header">Archived At</th>
                            <th class="text-center archives-table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archives as $archive)
                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-3 py-2 fw-semibold text-center">{{ $archive->type ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $archive->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $archive->archived_at ? \Carbon\Carbon::parse($archive->archived_at)->format('M d, Y h:i A') : 'N/A' }}</td>

                                <td class="py-2 text-center action-buttons">
                                    <div class="d-flex gap-3 justify-content-center">
                                        <form action="{{ route('archives.unarchive', $archive->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')

                                            <button class="btn btn-success px-3 py-2 unarchive-btn" onclick="return confirm('Are you sure you want to unarchive this item?')">Unarchive</button>
                                        </form>

                                        <form action="{{ route('archives.destroy', $archive->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger px-3 py-2 delete-btn" onclick="return confirm('Are you sure you want to permanently delete this item? This action cannot be undone.')">Permanently Delete</button>
                                        </form>
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
    @include('components.layouts.footer.profile-buttons-1')
@endsection
