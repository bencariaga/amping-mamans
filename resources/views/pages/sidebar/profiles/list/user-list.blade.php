@extends('layouts.personal-pages')

@section('title', 'Users')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/list/user-list.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/list/user-list.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a> &gt;
    <a href="{{ route('profiles.users.list') }}" class="text-decoration-none text-white">Users</a>
@endsection

@section('content')
    <div id="user-list-page" class="container">
        <div id="user-filter-controls">
            <form id="user-filter-form" method="GET" action="{{ route('profiles.users.list') }}">
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
                                        ['value'=>'last_name_asc','display'=>'Surname'],
                                        ['value'=>'role_asc','display'=>'Role']
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest','display'=>'Latest'],
                                        ['value'=>'oldest','display'=>'Oldest'],
                                        ['value'=>'last_name_asc','display'=>'Surname'],
                                        ['value'=>'role_asc','display'=>'Role']
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
                                    {{ request('per_page',4) }}
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="perPageDropdownBtn">
                                    <li><a class="dropdown-item @if(request('per_page',4)==4) active @endif" href="#" data-value="4">4</a></li>
                                    <li><a class="dropdown-item @if(request('per_page',4)==16) active @endif" href="#" data-value="16">16</a></li>
                                    <li><a class="dropdown-item @if(request('per_page',4)=='all') active @endif" href="#" data-value="all">All</a></li>
                                </ul>
                                <input type="hidden" name="per_page" id="filter-per-page" value="{{ request('per_page',4) }}">
                            </div>
                        </div>

                        <div id="user-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <span class="fw-bold" id="pagination-page-info">{{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} items</span>
                                <a href="{{ $users->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$users->onFirstPage()) active @else disabled @endif">Previous</a>
                                <a href="{{ $users->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($users->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                                <span class="fw-bold" id="pagination-page-info">All {{ count($users) }} items</span>
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

        <form id="user-role-update-form" method="POST" action="{{ route('profiles.users.roles.update') }}">
            @csrf
            @method('PUT')

            <div id="user-card-grid">
                @foreach($users as $user)
                    @php
                        $imageRecord = $user->files()->firstWhere('file_type', 'Image');
                        $file        = optional($imageRecord)->filename;
                        $name        = "{$user->last_name}, {$user->first_name}" . ($user->middle_name ? ' ' . strtoupper(substr($user->middle_name, 0, 1)) . '.' : '') . " {$user->suffix}";
                        $currentRole = ($user->member_type === 'Staff' && $user->staff) ? optional($user->staff->role)->role : 'N/A';
                        $url         = Auth::id() === $user->member_id ? route('user.profile.show') : route('profiles.users.show', $user->member_id);
                    @endphp

                    <div class="user-card-box">
                        @if($file)
                            <a href="{{ $url }}">
                                <img src="{{ asset('storage/' . $file) }}" alt="{{ $name }}">
                            </a>
                        @else
                            <a href="{{ $url }}">
                                <div class="user-avatar-placeholder">
                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                            </a>
                        @endif

                        <span class="user-fullname">{{ $name }}</span>
                        <span class="user-role-label">{{ $currentRole }}</span>

                        <div class="dropdown user-role-dropdown mt-1 d-none" id="roleDropdownBtn">
                            <button id="roleDropdownBtn-{{ $user->member_id }}" class="btn dropdown-toggle w-100 custom-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $user->staff?->role?->role ?? '' }}
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="roleDropdownBtn-{{ $user->member_id }}">
                                @foreach($roles as $roleItem)
                                    <li>
                                        <a class="dropdown-item @if($roleItem->role_id === $user->staff?->role_id) active @endif" href="#" data-value="{{ $roleItem->role_id }}">
                                            {{ $roleItem->role }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="roles[{{ $user->member_id }}]" id="roleInput-{{ $user->member_id }}" value="{{ $user->staff?->role_id }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-user')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
