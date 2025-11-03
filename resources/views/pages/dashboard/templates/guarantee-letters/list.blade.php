@extends('layouts.personal-pages')

@section('title', 'GL Templates')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letters.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/templates/guarantee-letters.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.list') }}" class="text-decoration-none text-white">Guarantee Letter Templates</a>
@endsection

@php
    $labelMap = [
        '[$application->applicant->client->member->first_name]' => '[Applicant\'s First Name]',
        '[$application->applicant->client->member->middle_name]' => '[Applicant\'s Middle Name]',
        '[$application->applicant->client->member->last_name]' => '[Applicant\'s Last Name]',
        '[$application->applicant->client->member->suffix]' => '[Applicant\'s Suffix]',
        '[$application->patient->client->member->first_name]' => '[Patient\'s First Name]',
        '[$application->patient->client->member->middle_name]' => '[Patient\'s Middle Name]',
        '[$application->patient->client->member->last_name]' => '[Patient\'s Last Name]',
        '[$application->patient->client->member->suffix]' => '[Patient\'s Suffix]',
        '[$application->service_type]' => '[Service Type]',
        '[$application->affiliate_partner->affiliate_partner_name]' => '[Affiliate Partner]',
        '[$application->billed_amount]' => '[Billed Amount]',
        '[$application->assistance_amount]' => '[Assistance Amount]',
        '[$application->applied_at]' => '[Applied At]',
        '[$application->reapply_at]' => '[Reapply At]',
        '[$application->applicant->barangay]' => '[Barangay]',
    ];

    $backendMap = array_flip($labelMap);
@endphp

@section('content')
    <div class="container">
        <div id="template-filter-controls">
            <form id="template-filter-form" method="GET" action="{{ route('gl-templates.list') }}">
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
                                        ['value'=>'latest',     'display'=>'Latest'],
                                        ['value'=>'oldest',     'display'=>'Oldest'],
                                        ['value'=>'title_asc',  'display'=>'Title (A-Z)'],
                                        ['value'=>'title_desc', 'display'=>'Title (Z-A)'],
                                    ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                                    @foreach([
                                        ['value'=>'latest',     'display'=>'Latest'],
                                        ['value'=>'oldest',     'display'=>'Oldest'],
                                        ['value'=>'title_asc',  'display'=>'Title (A-Z)'],
                                        ['value'=>'title_desc', 'display'=>'Title (Z-A)'],
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
                            @if($templates instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <span class="fw-bold">{{ $templates->firstItem() }}-{{ $templates->lastItem() }} of {{ $templates->total() }} items</span>
                                <a href="{{ $templates->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$templates->onFirstPage()) active @else disabled @endif">Previous</a>
                                <a href="{{ $templates->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($templates->hasMorePages()) active @else disabled @endif">Next</a>
                            @else
                                <span class="fw-bold">All {{ count($templates) }} items</span>
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
                <table class="template-table">
                    <thead>
                        <tr>
                            <th class="text-center template-table-header fs-5">Template Title</th>
                            <th class="text-center template-table-header fs-5">Template Content</th>
                            <th class="text-center template-table-header fs-5">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($templates as $template)
                            @php
                                $readableText = str_replace(array_keys($labelMap), array_values($labelMap), $template->gl_content);
                                $readableText = str_replace(';', '<br>', $readableText);
                            @endphp

                            <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                <td class="px-4 py-3 text-center fw-bold fs-5">{{ $template->gl_tmp_title }}</td>
                                <td class="px-5 py-3 template-text">{!! $readableText !!}</td>

                                <td class="px-3 py-3 text-center action-buttons">
                                    <div class="gap-3 d-flex justify-content-center">
                                        <a href="{{ route('gl-templates.edit', $template->gl_tmp_id) }}" class="btn btn-sm btn-warning px-2 py-2 fs-6" style="width: 12rem;">Edit Template</a>
                                        <a href="{{ route('guarantee-letter', $template->gl_tmp_id) }}" class="btn btn-sm btn-warning px-2 py-2 fs-6" style="width: 12rem;">View Template</a>

                                        <form action="{{ route('gl-templates.destroy', $template->gl_tmp_id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger px-2 py-2 fs-6" style="width: 12rem;" onclick="return confirm('Are you sure you want to delete this GL template?')">Delete Template</button>
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
    @include('components.layouts.footer.list-gl-tmp')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
