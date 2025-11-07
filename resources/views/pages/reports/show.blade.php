@extends('layouts.personal-pages')

@section('title', ucfirst($type).' Report')

@push('styles')
    <style>
        .reports-filter .section-title { font-weight: 700; }
        .reports-filter .form-control,
        .reports-filter .form-select,
        .reports-filter .btn { height: 38px; }
        .stat-pill { border-radius: 999px; padding: .35rem .75rem; font-weight: 600; }
        .stat-pill .label { opacity: .8; margin-right: .5rem; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const filterMode = document.getElementById('filterMode');
            const groupRange = document.getElementById('group-range');
            const groupMonth = document.getElementById('group-month');
            const groupYear = document.getElementById('group-year');
            function setMode(v){
                groupRange.style.display = (v==='range') ? '' : 'none';
                groupMonth.style.display = (v==='month') ? '' : 'none';
                groupYear.style.display = (v==='year') ? '' : 'none';
            }
            if (filterMode){
                filterMode.addEventListener('change', function(){ setMode(this.value); });
                setMode(filterMode.value);
            }
        });
    </script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>
    <span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.index') }}" class="text-decoration-none text-white">Reports</a>
    <span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.show', ['type' => $type]) }}" class="text-decoration-none text-reset">{{ ucfirst($type) }}</a>
@endsection

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 no-print">
            <h2 class="m-0">{{ ucfirst($type) }} Report</h2>
            <div>
                <a href="{{ route('reports.csv', array_merge(['type'=>$type], $query)) }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-file-csv me-2"></i>Download CSV
                </a>
                <a href="{{ route('reports.pdf_dom', array_merge(['type'=>$type], $query)) }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                </a>
            </div>
        </div>

        <div class="reports-filter card shadow-sm mb-3 no-print">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.show', ['type' => $type]) }}" class="row g-2 align-items-end">
                    <div class="col-auto" style="min-width: 150px;">
                        <label class="form-label section-title mb-1">Filter Mode</label>
                        <select id="filterMode" class="form-select" name="mode">
                            @php $mode = request('mode','range'); @endphp
                            <option value="range" @selected($mode==='range')>Date Range</option>
                            <option value="month" @selected($mode==='month')>By Month</option>
                            <option value="year" @selected($mode==='year')>By Year</option>
                        </select>
                    </div>

                    <div class="col-auto" id="group-range">
                        <label class="form-label mb-1 fw-bold">Date Range</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From" style="max-width: 160px;"/>
                            <span class="fw-bold text-muted" style="white-space: nowrap;">To:</span>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To" style="max-width: 160px;"/>
                        </div>
                    </div>

                    <div class="col" id="group-month" style="display:none;">
                        <label class="form-label mb-1 fw-bold">Month</label>
                        <div class="d-flex gap-2">
                            <select class="form-select" name="month">
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected((int)request('month')===$m)>{{ \Carbon\Carbon::createFromDate(null,$m,1)->format('F') }}</option>
                                @endfor
                            </select>
                            <input type="number" class="form-control" name="year" placeholder="Year" value="{{ request('year', now()->year) }}" style="max-width: 100px;"/>
                        </div>
                    </div>

                    <div class="col-auto" id="group-year" style="display:none; min-width: 150px;">
                        <label class="form-label mb-1 fw-bold">Year</label>
                        <input type="number" class="form-control" name="year" placeholder="Year" value="{{ request('year', now()->year) }}"/>
                    </div>

                    @if($type==='applicants')
                        <div class="col-auto" style="min-width: 180px;">
                            <label class="form-label mb-1 fw-bold">Barangay</label>
                            <input type="text" class="form-control" name="barangay" value="{{ request('barangay') }}" placeholder="e.g. San Isidro"/>
                        </div>
                    @endif

                    @if($type==='applications')
                        <div class="col-auto" style="min-width: 200px;">
                            <label class="form-label mb-1 fw-bold">Service</label>
                            <select class="form-select" name="service_id">
                                <option value="">— All Services —</option>
                                @foreach(($extra['services'] ?? []) as $srv)
                                    <option value="{{ $srv->service_id }}" @selected(request('service_id')==$srv->service_id)>{{ $srv->service_type }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit" style="min-width: 120px;"><i class="fas fa-filter me-2"></i>Apply</button>
                    </div>
                </form>
                <div class="mt-2 text-muted no-print"><strong>Range:</strong> <strong>{{ $rangeLabel }}</strong></div>
            </div>
        </div>

        <div class="mb-3 d-flex flex-wrap gap-2">
            @if($type==='applications')
                <span class="stat-pill bg-light border"><span class="label">Total:</span> {{ number_format($summary['total']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Billed:</span> ₱ {{ number_format($summary['billed']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Assisted:</span> ₱ {{ number_format($summary['assisted']) }}</span>
            @elseif($type==='tariffs')
                <span class="stat-pill bg-light border"><span class="label">Total:</span> {{ number_format($summary['total']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Active:</span> {{ number_format($summary['active']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Inactive:</span> {{ number_format($summary['inactive']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Draft:</span> {{ number_format($summary['draft']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Scheduled:</span> {{ number_format($summary['scheduled']) }}</span>
            @else
                <span class="stat-pill bg-light border"><span class="label">Total:</span> {{ number_format($summary['total']) }}</span>
            @endif
        </div>

        <div class="data-table-container shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        @if($type==='applicants')
                            <tr>
                                <th>Applicant ID</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Monthly Income</th>
                                <th>Created</th>
                            </tr>
                        @elseif($type==='patients')
                            <tr>
                                <th>Patient ID</th>
                                <th>Full Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Category</th>
                                <th>Created</th>
                            </tr>
                        @elseif($type==='applications')
                            <tr>
                                <th>Applicant</th>
                                <th>Patient</th>
                                <th>Affiliate Partner</th>
                                <th>Service</th>
                                <th>Billed</th>
                                <th>Assisted</th>
                                <th>Applied At</th>
                            </tr>
                        @elseif($type==='tariffs')
                            <tr>
                                <th>Tariff List ID</th>
                                <th>Effectivity Date</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($items as $row)
                            @if($type==='applicants')
                                <tr>
                                    <td>{{ $row->applicant_id }}</td>
                                    <td>{{ $row->full_name }}</td>
                                    <td>{{ $row->phone_number ?? '—' }}</td>
                                    <td>₱ {{ number_format((int)$row->monthly_income, 0) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @elseif($type==='patients')
                                <tr>
                                    <td>{{ $row->patient_id }}</td>
                                    <td>{{ $row->full_name }}</td>
                                    <td>{{ $row->sex ?: '—' }}</td>
                                    <td>{{ $row->age ?: '—' }}</td>
                                    <td>{{ $row->patient_category ?: '—' }}</td>
                                    <td>
                                        @if(!empty($row->created_at))
                                            {{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}
                                        @elseif(!empty($row->created_year))
                                            {{ $row->created_year }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @elseif($type==='applications')
                                <tr>
                                    <td>{{ $row->applicant_name ?? '—' }}</td>
                                    <td>{{ $row->patient_name ?? '—' }}</td>
                                    <td>{{ $row->affiliate_partner_name ?? '—' }}</td>
                                    <td>{{ $row->service_name ?? '—' }}</td>
                                    <td>₱ {{ number_format((int)$row->billed_amount, 0) }}</td>
                                    <td>₱ {{ number_format((int)$row->assistance_amount, 0) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->applied_at)->format('Y-m-d') }}</td>
                                </tr>
                            @elseif($type==='tariffs')
                                <tr>
                                    <td>{{ $row->tariff_list_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->effectivity_date)->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-secondary">{{ $row->tl_status }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No data found for the selected range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-2')
@endsection
