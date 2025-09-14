@extends('layouts.personal-pages')

@section('title', 'Applicants')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/list/applicant-list.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/list/applicant-list.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a> &gt;
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-white">Applicants</a>
@endsection

@section('content')
    <div class="container">
        <div id="client-filter-controls">
            <form id="client-filter-form" method="GET" action="{{ route('profiles.applicants.list') }}">
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

                        <div id="client-pagination-controls" class="d-flex align-items-center gap-2">
                            @if($clients instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <span class="fw-bold">{{ $clients->firstItem() }}-{{ $clients->lastItem() }} of {{ $clients->total() }} items</span>
                                <a href="{{ $clients->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$clients->onFirstPage()) active @else disabled @endif">Previous</a>
                                <a href="{{ $clients->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($clients->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                                <span class="fw-bold">All {{ count($clients) }} items</span>
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

        <div class="client-card-flex mt-3">
            @foreach($clients as $client)
                @php
                    $m    = $client->member;
                    $app  = $client->applicant;
                    $name = "{$m->last_name}, {$m->first_name}" . ($m->middle_name ? ' '.strtoupper(substr($m->middle_name,0,1)).'.' : '') . ($m->suffix ? " {$m->suffix}" : '');
                    $phone = optional($client->contacts->firstWhere('contact_type', 'Application'))->phone_number ?? optional($client->contacts->first())->phone_number ?? '';
                    $occupation = optional($client->occupation)->occupation;
                    $income     = number_format($client->monthly_income, 2);
                @endphp

                @if($app)
                    <a href="{{ route('profiles.applicants.show', ['applicant' => $app->applicant_id]) }}" class="text-decoration-none text-reset">
                        <div class="client-card-box">
                            <div class="client-info d-flex align-items-center">
                                <div class="field client-name">{{ $name }}</div>
                                <div class="field client-phone-number">{{ $phone }}
                                    <button class="copy-symbol btn btn-primary" data-phone-number="{{ $phone }}" aria-hidden="true">
                                        <i class="fa fa-copy"><span class="copy-word"">&nbsp;&nbsp;&nbsp;copy</span></i>
                                    </button>
                                </div>
                                <div class="field client-occupation">{{ $occupation }}</div>
                                <div class="field client-income">₱ {{ $income }}</div>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.list-applicant')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
