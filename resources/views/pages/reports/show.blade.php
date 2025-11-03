@extends('layouts.personal-pages')

@section('title', ucfirst($type) . ' Report')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/system/reports.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/system/reports.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.index') }}" class="text-decoration-none text-white">Reports</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('reports.show', ['type' => $type]) }}" class="text-decoration-none text-reset">{{ ucfirst($type) }}</a>
@endsection

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2 no-print">
            <h2 class="fw-semibold m-0">{{ ucfirst($type) }} Report</h2>

            <div>
                <a href="{{ route('reports.xlsx', array_merge(['type' => $type], $query)) }}" class="btn btn-outline-success me-3 gap-2 fw-bold">
                    <i class="fas fa-file-excel me-2"></i>Download XLSX
                </a>
                <a href="{{ route('reports.pdf_dom', array_merge(['type' => $type], $query)) }}" class="btn btn-outline-danger gap-2 fw-bold">
                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                </a>
            </div>
        </div>

        <div class="reports-filter card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.show', ['type' => $type]) }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 fw-bold gap-2" type="submit" style="height: 40px;"><i class="fas fa-filter me-2"></i>Apply</button>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label section-title fw-bold">Filter Mode</label>
                        <select id="filterMode" class="form-select custom-select-control fw-bold" name="mode">
                            @php $mode = request('mode', 'range'); @endphp
                            <option value="range" @selected($mode === 'range')>Date Range</option>
                            <option value="month" @selected($mode === 'month')>By Month</option>
                            <option value="year" @selected($mode === 'year')>By Year</option>
                        </select>
                    </div>

                    <div class="col-md-5" id="group-range">
                        <label class="form-label fw-bold">Date Range</label>
                        <div class="d-flex align-items-center gap-2" id="custom-control">
                            <input type="date" class="form-control custom-input-control fw-bold" id="custom-control" name="date_from" value="{{ request('date_from') }}" placeholder="From"/>
                            <span class="fw-bold text-muted" style="white-space: nowrap;">To:</span>
                            <input type="date" class="form-control custom-input-control fw-bold" id="custom-control" name="date_to" value="{{ request('date_to') }}" placeholder="To"/>
                        </div>
                    </div>

                    <div class="col-md-5" id="group-month" style="display:none;">
                        <label class="form-label fw-bold">Month</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <select class="form-select custom-select-control fw-bold" id="custom-control" name="month">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected((int) request('month') === $m)>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control custom-input-control fw-bold" id="custom-control" name="year" placeholder="Year" value="{{ request('year', now()->year) }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5" id="group-year" style="display:none;">
                        <label class="form-label fw-bold">Year</label>
                        <input type="number" class="form-control custom-input-control fw-bold" id="custom-control" name="year" placeholder="Year" value="{{ request('year', now()->year) }}"/>
                    </div>

                    @if($type === 'applicants')
                        <div class="col-md-5" id="custom-control">
                            <label class="form-label fw-bold">Barangay</label>
                            <input type="text" class="form-control custom-input-control fw-bold" name="barangay" value="{{ request('barangay') }}"/>
                        </div>
                    @endif

                    @if($type === 'applications')
                        <div class="col-md-5" id="custom-control">
                            <label class="form-label fw-bold">Service</label>
                            <select class="form-select custom-select-control fw-bold" name="service_id">
                                <option value="">— All Services —</option>
                                @foreach(($extra['services'] ?? []) as $srv)
                                    <option value="{{ $srv->service_id }}" @selected(request('service_id') == $srv->service_id)>{{ $srv->service }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
                <div class="mt-3 text-muted fw-bold">Range: <strong>{{ $rangeLabel }}</strong></div>
            </div>
        </div>

        <div class="mb-3 d-flex flex-wrap gap-2">
            @if($type === 'applications')
                <span class="stat-pill bg-light border"><span class="label">Total:</span> {{ number_format($summary['total']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Billed:</span> ₱ {{ number_format($summary['billed']) }}</span>
                <span class="stat-pill bg-light border"><span class="label">Assisted:</span> ₱ {{ number_format($summary['assisted']) }}</span>
            @elseif($type === 'tariffs')
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
                <table class="report-table">
                    <thead>
                        @if($type === 'applicants')
                            <tr>
                                <th class="text-center report-table-header">Applicant ID</th>
                                <th class="text-center report-table-header">Full Name</th>
                                <th class="text-center report-table-header">Phone</th>
                                <th class="text-center report-table-header">Monthly Income</th>
                                <th class="text-center report-table-header">Created</th>
                            </tr>
                        @elseif($type === 'patients')
                            <tr>
                                <th class="text-center report-table-header">Patient ID</th>
                                <th class="text-center report-table-header">Full Name</th>
                                <th class="text-center report-table-header">Sex</th>
                                <th class="text-center report-table-header">Age</th>
                                <th class="text-center report-table-header">Category</th>
                                <th class="text-center report-table-header">Created</th>
                            </tr>
                        @elseif($type === 'applications')
                            <tr>
                                <th class="text-center report-table-header">Application ID</th>
                                <th class="text-center report-table-header">Applicant</th>
                                <th class="text-center report-table-header">Service ID</th>
                                <th class="text-center report-table-header">Billed</th>
                                <th class="text-center report-table-header">Assisted</th>
                                <th class="text-center report-table-header">Applied At</th>
                            </tr>
                        @elseif($type === 'tariffs')
                            <tr>
                                <th class="text-center report-table-header">Tariff List ID</th>
                                <th class="text-center report-table-header">Effectivity Date</th>
                                <th class="text-center report-table-header">Status</th>
                                <th class="text-center report-table-header">Created</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($items as $row)
                            @if($type === 'applicants')
                                <tr>
                                    <td class="text-center">{{ $row->applicant_id }}</td>
                                    <td class="text-left">{{ $row->full_name }}</td>
                                    <td class="text-center">{{ $row->phone_number ?? '—' }}</td>
                                    <td class="text-right">₱ {{ number_format((int) $row->monthly_income, 0) }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @elseif($type === 'patients')
                                <tr>
                                    <td class="text-center">{{ $row->patient_id }}</td>
                                    <td class="text-left">{{ $row->full_name }}</td>
                                    <td class="text-center">{{ $row->sex ?: '—' }}</td>
                                    <td class="text-center">{{ $row->age ?: '—' }}</td>
                                    <td class="text-center">{{ $row->patient_category ?: '—' }}</td>
                                    <td class="text-center">
                                        @if(!empty($row->created_at))
                                            {{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}
                                        @elseif(!empty($row->created_year))
                                            {{ $row->created_year }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @elseif($type === 'applications')
                                <tr>
                                    <td class="text-center">{{ $row->application_id }}</td>
                                    <td class="text-left">{{ $row->full_name }}</td>
                                    <td class="text-center">{{ $row->service_id }}</td>
                                    <td class="text-right">₱ {{ number_format((int) $row->billed_amount, 0) }}</td>
                                    <td class="text-right">₱ {{ number_format((int) $row->assistance_amount, 0) }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->applied_at)->format('Y-m-d') }}</td>
                                </tr>
                            @elseif($type === 'tariffs')
                                <tr>
                                    <td class="text-center">{{ $row->tariff_list_id }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->effectivity_date)->format('Y-m-d') }}</td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $row->tl_status }}</span></td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No data found for the selected range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
