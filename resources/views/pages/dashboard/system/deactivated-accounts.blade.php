@extends('layouts.personal-pages')

@section('title', 'Deactivated Accounts')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/system/deactivated-accounts.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/system/deactivated-accounts.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;
    <a href="{{ route('accounts.deactivated') }}" class="text-decoration-none text-white">Deactivated Accounts</a>
@endsection

@section('content')
    <div id="deactivated-accounts-page" class="container">
        <div id="accounts-filter-controls">
            <form id="accounts-filter-form" method="GET" action="{{ route('accounts.deactivated') }}">
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
                                        ['value'=>'staff','display'=>'Staff'],
                                        ['value'=>'applicants','display'=>'Applicants'],
                                        ['value'=>'sponsors','display'=>'Sponsors'],
                                        ['value'=>'affiliate_partners','display'=>'Affiliate Partners'],
                                    ])->firstWhere('value', request('type','all'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="typeDropdownBtn">
                                    @foreach([
                                        ['value'=>'all','display'=>'All'],
                                        ['value'=>'staff','display'=>'Staff'],
                                        ['value'=>'applicants','display'=>'Applicants'],
                                        ['value'=>'sponsors','display'=>'Sponsors'],
                                        ['value'=>'affiliate_partners','display'=>'Affiliate Partners'],
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
                                        ['value'=>'name_asc','display'=>'Name (A-Z)'],
                                        ['value'=>'name_desc','display'=>'Name (Z-A)'],
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
                                        ['value'=>'name_asc','display'=>'Name (A-Z)'],
                                        ['value'=>'name_desc','display'=>'Name (Z-A)'],
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

                        <div id="accounts-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($accounts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="fw-bold" id="pagination-page-info">{{ $accounts->firstItem() }}-{{ $accounts->lastItem() }} of {{ $accounts->total() }} items</span>
                            <a href="{{ $accounts->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$accounts->onFirstPage()) active @else disabled @endif">Previous</a>
                            <a href="{{ $accounts->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($accounts->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                            <span class="fw-bold" id="pagination-page-info">All {{ count($accounts) }} items</span>
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
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th class="text-center accounts-table-header">Account Name</th>
                            <th class="text-center accounts-table-header">Account Type</th>
                            <th class="text-center accounts-table-header">Time of Deactivation</th>
                            <th class="text-center accounts-table-header">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($accounts as $account)
                            @php
                                $url = '#';

                                if ($account->type === 'Staff' && $account->member_id_for_link) {
                                    $url = route('profiles.users.show', $account->member_id_for_link);
                                } elseif ($account->type === 'Applicant' && $account->member_id_for_link) {
                                    $url = route('profiles.applicants.show', $account->member_id_for_link);
                                } elseif ($account->type === 'Sponsor' && $account->member_id_for_link) {
                                    $url = route('profiles.sponsors.show', $account->member_id_for_link);
                                } elseif ($account->type === 'Affiliate Partner' && $account->member_id_for_link) {
                                    $url = route('profiles.affiliate-partners.show', $account->member_id_for_link);
                                }
                            @endphp

                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-3 py-2 fw-semibold text-center">{{ $account->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $account->type ?? 'N/A' }}</td>
                                <td class="px-3 py-2 fw-semibold text-center">{{ $account->deactivated_at ? \Carbon\Carbon::parse($account->deactivated_at)->format('M d, Y, h:i A') : 'N/A' }}</td>

                                <td class="py-2 text-center action-buttons">
                                    <div class="d-flex gap-3 justify-content-center">
                                        @if ($url !== '#')
                                            <a href="{{ $url }}" class="btn btn-primary px-3 py-2">View Account</a>
                                        @else
                                            <span class="btn btn-secondary px-3 py-2 disabled">No Profile Link</span>
                                        @endif

                                        <form action="{{ route('accounts.reactivate', $account->account_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')

                                            <button class="btn btn-success px-3 py-2 reactivate-btn" onclick="return confirm('Are you sure you want to reactivate this account?')">Reactivate</button>
                                        </form>

                                        <form action="{{ route('accounts.destroy', $account->account_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger px-3 py-2 delete-btn" onclick="return confirm('Are you sure you want to permanently delete this account? This action cannot be undone.')">Permanently Delete</button>
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
