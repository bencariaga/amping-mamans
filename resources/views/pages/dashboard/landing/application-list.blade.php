@extends('layouts.personal-pages')

@section('title', 'Application List')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/landing/application-list.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/landing/application-list.js') }}" defer></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a>&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;
    <a href="{{ route('applications.list') }}" class="text-decoration-none text-white">Applications</a>
@endsection

@section('content')
    <div id="application-list-page" class="container">
        <div id="application-filter-controls">
            <form id="application-filter-form" method="GET" action="{{ route('applications.list') }}">
                <div class="controls-grid">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
                        {{-- ...existing filter controls... --}}
                    </div>
                </div>
                @foreach(request()->except(['page','per_page','sort_by','search']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
            </form>
        </div>

        <form method="POST" action="{{ route('search.archive', ['model' => 'applications']) }}">
            @csrf
            <div class="data-table-container shadow-sm">
                <div class="table-responsive">
                    <table class="application-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllApplications"></th>
                                <th class="text-center application-table-header">Applicant Name</th>
                                <th class="text-center application-table-header">Service Type</th>
                                <th class="text-center application-table-header">Status</th>
                                <th class="text-center application-table-header">Assistance</th>
                                <th class="text-center application-table-header">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                @php
                                    $last = optional($application->applicant->client->member)->last_name ?: '';
                                    $first = optional($application->applicant->client->member)->first_name ?: '';
                                    $middle = optional($application->applicant->client->member)->middle_name ?: '';
                                    $suffix = optional($application->applicant->client->member)->suffix ?: '';
                                    $middleInitial = $middle !== '' ? strtoupper(substr(trim($middle), 0, 1)) . '.' : '';
                                    $parts = [];
                                    if ($last !== '') $parts[] = $last . ',';
                                    if ($first !== '') $parts[] = $first;
                                    if ($middleInitial !== '') $parts[] = $middleInitial;
                                    if ($suffix !== '') $parts[] = $suffix;
                                    $name = trim(implode(' ', $parts));
                                    $gl = \Illuminate\Support\Facades\DB::table('guarantee_letters')->where('application_id', $application->application_id)->orderBy('gl_id', 'desc')->first();
                                    $status = $gl->gl_status ?? 'Pending';
                                @endphp

                                <tr class="{{ $loop->even ? 'bg-light' : '' }}">
                                    <td class="px-2 py-2 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $application->application_id }}">
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-center">{{ $application->expenseRange->service->service_type ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($status === 'Approved')
                                            <span class="badge bg-success d-flex align-items-center justify-content-center px-2 py-2">Approved</span>
                                        @elseif($status === 'Rejected')
                                            <span class="badge bg-danger d-flex align-items-center justify-content-center px-2 py-2">Rejected</span>
                                        @else
                                            <span class="badge bg-warning text-black d-flex align-items-center justify-content-center px-2 py-2">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">₱ {{ number_format($application->assistance_amount, 2) }}</td>
                                    <td class="py-2 text-center action-buttons">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-secondary px-3 py-2 details-btn" data-application-id="{{ $application->application_id }}">Details</button>
                                            <button class="btn btn-sm btn-success px-3 py-2 authorize-btn hide" data-application-id="{{ $application->application_id }}" @if($status !== 'Pending') disabled @endif>Approve</button>
                                            <button class="btn btn-sm btn-danger px-3 py-2 reject-btn hide" data-application-id="{{ $application->application_id }}" @if($status !== 'Pending') disabled @endif>Reject</button>
                                            <a class="btn btn-sm btn-primary px-3 py-2 preview-btn {{ $status !== 'Approved' ? 'disabled' : '' }}" href="{{ route('applications.guarantee-letter.pdf', ['application' => $application->application_id]) }}" data-application-id="{{ $application->application_id }}" target="_blank" aria-disabled="{{ $status !== 'Approved' ? 'true' : 'false' }}">Preview</a>
                                            <button type="submit" class="btn btn-sm btn-warning px-3 py-2" formaction="{{ route('search.archive', ['model' => 'applications']) }}" formmethod="POST" name="ids[]" value="{{ $application->application_id }}">Archive</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="submit" class="btn btn-warning mt-3">Bulk Archive</button>
        </form>
    </div>

    <script>
        document.getElementById('checkAllApplications').onclick = function() {
            var checkboxes = document.querySelectorAll('input[name="ids[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>

    <div class="modal-overlay" id="detailsModal" style="display: none;">
        <div class="modal-container" id="modal-container">
            <div class="modal-header">
                <h2>Application Details</h2>
                <button class="modal-close" onclick="closeModal('detailsModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 005.7 7.11L10.59 12 5.7 16.89a1 1 0 101.41 1.41L12 13.41l4.89 4.89a1 1 0 001.41-1.41L13.41 12l4.89-4.89a1 1 0 000-1.4z"/></svg>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal('detailsModal')">OKAY, I UNDERSTAND</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="approveModal" style="display: none;">
        <div class="modal-container" id="modal-container">
            <div class="modal-header">
                <h2>Confirm Authorization</h2>
                <button class="modal-close" onclick="closeModal('approveModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 005.7 7.11L10.59 12 5.7 16.89a1 1 0 101.41 1.41L12 13.41l4.89 4.89a1 1 0 001.41-1.41L13.41 12l4.89-4.89a1 1 0 000-1.4z"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p>This confirmation action cannot be undone. Please click "CONFIRM" to continue to approve this assistance request.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('approveModal')">CANCEL</button>
                <button class="btn btn-success" id="confirmAuthorize">CONFIRM</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="rejectModal" style="display: none;">
        <div class="modal-container" id="modal-container">
            <div class="modal-header">
                <h2>Confirm Rejection</h2>
                <button class="modal-close" onclick="closeModal('rejectModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 005.7 7.11L10.59 12 5.7 16.89a1 1 0 101.41 1.41L12 13.41l4.89 4.89a1 1 0 001.41-1.41L13.41 12l4.89-4.89a1 1 0 000-1.4z"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p>This confirmation action cannot be undone. Please click "CONFIRM" to continue to reject this assistance request.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('rejectModal')">CANCEL</button>
                <button class="btn btn-danger" id="confirmReject">CONFIRM</button>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
