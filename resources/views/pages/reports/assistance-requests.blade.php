@extends('layouts.personal-pages')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-center text-uppercase">Assistance Requests Report ({{ $year }})</h2>
        </div>
    </div>
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <form method="GET" action="{{ route('reports.assistance-requests') }}" class="row g-2 align-items-end">
                <div class="btn-toolbar justify-content-between align-items-stretch" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="input-group h-100">
                        <div class="input-group-prepend">
                            <div class="input-group-text h-100">Year</div>
                        </div>
                        <input type="number" name="year" class="form-control h-100" value="{{ $year }}" min="2000" max="{{ now()->year }}">
                    </div>
                    <div class="btn-group h-100" role="group">
                        <button type="submit" class="btn btn-primary h-100"><i class="fas fa-filter"></i>&nbsp;Filter</button>
                        <a href="{{ route('reports.assistance-requests.pdf', ['year' => $year]) }}" class="btn btn-success h-100"><i class="fas fa-download"></i>&nbsp;Download PDF</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="table-responsive shadow rounded">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Application ID</th>
                            <th scope="col">Applicant</th>
                            <th scope="col">Service</th>
                            <th scope="col">Billed Amount</th>
                            <th scope="col">Assistance Amount</th>
                            <th scope="col">Date Applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            @php
                                $applied_at = \Carbon\Carbon::parse($app->applied_at);
                            @endphp
                            <tr>
                                <td>{{ $app->application_id }}</td>
                                <td>{{ $app->applicant->client->member->full_name ?? 'N/A' }}</td>
                                <td>{{ $app->expenseRange->service->service_type ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($app->billed_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($app->assistance_amount, 2) }}</td>
                                <td>{{ $applied_at->format('M. d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No assistance requests found for {{ $year }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection