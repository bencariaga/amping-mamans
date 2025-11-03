@extends('layouts.personal-pages')

@section('title', 'Audit Logs')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/system/audit-logs.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/system/audit-logs.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;
    <a href="{{ route('audit-logs.list') }}" class="text-decoration-none text-white">Audit Logs</a>
@endsection

@section('content')
    <div id="audit-logs-page" class="container">
        <div id="logs-filter-controls">
            <form id="logs-filter-form" method="GET" action="{{ route('audit-logs.list') }}">
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
                                        ['value'=>'Login','display'=>'Login'],
                                        ['value'=>'Logout','display'=>'Logout'],
                                        ['value'=>'Page Access','display'=>'Page Access'],
                                        ['value'=>'Data Creation','display'=>'Data Creation'],
                                        ['value'=>'Data Update','display'=>'Data Update'],
                                        ['value'=>'Data Deletion','display'=>'Data Deletion'],
                                    ])->firstWhere('value', request('type','all'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="typeDropdownBtn">
                                    @foreach([
                                        ['value'=>'all','display'=>'All'],
                                        ['value'=>'Login','display'=>'Login'],
                                        ['value'=>'Logout','display'=>'Logout'],
                                        ['value'=>'Page Access','display'=>'Page Access'],
                                        ['value'=>'Data Creation','display'=>'Data Creation'],
                                        ['value'=>'Data Update','display'=>'Data Update'],
                                        ['value'=>'Data Deletion','display'=>'Data Deletion'],
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

                        <div id="logs-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="fw-bold" id="pagination-page-info">{{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }} items</span>
                            <a href="{{ $logs->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$logs->onFirstPage()) active @else disabled @endif">Previous</a>
                            <a href="{{ $logs->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($logs->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                            <span class="fw-bold" id="pagination-page-info">All {{ count($logs) }} items</span>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Previous</a>
                            <a href="#" class="btn btn-outline-secondary fw-bold disabled">Next</a>
                            @endif
                        </div>

                        <div class="filter-role-wrapper d-flex align-items-center gap-2">
                            <label for="roleDropdownBtn" class="form-label fw-bold mb-0">Role:</label>
                            <div class="dropdown filter-role-dropdown">
                                <button id="roleDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ request('role', 'All') }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="roleDropdownBtn">
                                    <li>
                                        <a class="dropdown-item @if(request('role') == 'All' || !request('role')) active @endif" href="#" data-value="All">All</a>
                                    </li>
                                    @foreach($roles as $role)
                                    <li>
                                        <a class="dropdown-item @if(request('role') == $role->role) active @endif" href="#" data-value="{{ $role->role }}">{{ $role->role }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="role" id="filter-role" value="{{ request('role', 'All') }}">
                            </div>
                        </div>
                    </div>
                </div>

                @foreach(request()->except(['page','per_page','sort_by','search','type','role']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
            </form>
        </div>

        <div class="data-table-container shadow-sm">
            <div class="table-responsive">
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th class="text-center logs-table-header">Staff Member Name</th>
                            <th class="text-center logs-table-header">Role</th>
                            <th class="text-center logs-table-header">Action Type</th>
                            <th class="text-center logs-table-header">Description</th>
                            <th class="text-center logs-table-header">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-3 py-2 fw-semibold text-center">{{ $log->staff_name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $log->role ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $log->al_type ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $log->al_text ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') : 'N/A' }}</td>
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
