<div id="tariff-filter-controls">
    <form id="tariff-filter-form" method="GET" action="{{ route('tariff-lists') }}">
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
                        <button id="sortDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-submit-form="tariff-filter-form">
                            {{ collect([
                                ['value'=>'latest', 'display'=>'Latest'],
                                ['value'=>'oldest', 'display'=>'Oldest'],
                            ])->firstWhere('value', request('sort_by','latest'))['display'] }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdownBtn">
                            @foreach([
                                ['value'=>'latest', 'display'=>'Latest'],
                                ['value'=>'oldest', 'display'=>'Oldest'],
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
                        <button id="perPageDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-submit-form="tariff-filter-form">
                            {{ request('per_page',4) == 'all' ? 'All' : request('per_page',4) }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="perPageDropdownBtn">
                            @foreach([4, 16, 'all'] as $n)
                                <li>
                                    <a class="dropdown-item @if(request('per_page', 4) == $n) active @endif" href="#" data-value="{{ $n }}">{{ $n == 'all' ? 'All' : $n }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" id="filter-per-page" name="per_page" value="{{ request('per_page', 4) }}">
                    </div>
                </div>

                <div id="tariff-pagination-controls" class="d-flex align-items-center gap-2">
                    @if(isset($tariffModels) && $tariffModels instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <span class="fw-bold">{{ $tariffModels->firstItem() }}-{{ $tariffModels->lastItem() }} of {{ $tariffModels->total() }} items</span>
                        <a href="{{ $tariffModels->previousPageUrl() }}" class="btn btn-outline-secondary fw-bold @if(!$tariffModels->onFirstPage()) active @else disabled @endif">Previous</a>
                        <a href="{{ $tariffModels->nextPageUrl() }}" class="btn btn-outline-secondary fw-bold @if($tariffModels->hasMorePages()) active @else disabled @endif">Next</a>
                    @else
                        <span class="fw-bold">All {{ isset($tariffModels) ? $tariffModels->count() : 0 }} items</span>
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
