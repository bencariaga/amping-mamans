@extends('layouts.personal-pages')

@section('title', 'Assistance Request')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/application-entry/assistance-request.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/application-entry/assistance-request.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('applications.assistance-request') }}" class="text-decoration-none text-reset">Assistance Request</a>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="assistanceRequestForm">
            @csrf

            <input type="hidden" id="applicantId" name="applicant_id">
            <input type="hidden" id="patientId" name="patient_id">

            <div id="profileContainer">
                <div class="row gx-3 gy-3">
                    <div class="form-group col-md-3">
                        <label for="applicant" class="form-label fw-bold">Applicant<span class="required-asterisk">*</span></label>
                        <select class="form-select form-select-lg" id="applicant" name="applicantId"></select>
                        <small id="applicantVerificationMessage" class="form-text mt-1 d-none"></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="label-hidden">Create New Applicant</label>
                        <a class="btn btn-primary w-100 fw-bold" href="/profiles/applicants/add">Create New Applicant</a>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="serviceType" class="form-label fw-bold">Service Type <span class="required-asterisk">*</span></label>
                        <div class="dropdown">
                            <button id="serviceTypeDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn w-100 fw-bold d-flex justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select a service.</button>
                            <ul class="dropdown-menu w-100" aria-labelledby="serviceTypeDropdownBtn">
                                <li><a class="dropdown-item" href="#" data-value="">Select a service.</a></li>
                                @foreach($services as $service)
                                <li><a class="dropdown-item" href="#" data-value="{{ $service->service_id }}">{{ $service->service_type }}</a></li>
                                @endforeach
                            </ul>
                            <input type="hidden" id="serviceType" name="service_id">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="billedAmount" class="form-label fw-bold">Billed Amount (in ₱) <span class="required-asterisk">*</span></label>
                        <input type="number" class="form-control" id="billedAmount" placeholder="Enter billed amount." name="billed_amount" min="0">
                        <small id="billedAmountMessage" class="form-text mt-1 d-none"></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="label-hidden">Calculate</label>
                        <button class="btn btn-primary w-100 fw-bold" type="button" id="calculateBtn">CALCULATE</button>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="assistanceAmount" class="form-label fw-bold">Assistance Amount (in ₱) <span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control" id="assistanceAmount" placeholder="Calculate the billed amount first." readonly>
                        <input type="hidden" id="assistanceAmountRaw" name="assistance_amount">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="affiliatePartner" class="form-label fw-bold">Affiliate Partner <span class="required-asterisk">*</span></label>
                        <div class="dropdown">
                            <button id="affiliatePartnerDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn w-100 fw-bold d-flex justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select an affiliate partner.</button>
                            <ul class="dropdown-menu w-100" aria-labelledby="affiliatePartnerDropdownBtn">
                                <li><a class="dropdown-item" href="#" data-value="">Select an affiliate partner.</a></li>
                                @foreach($affiliate_partners as $partner)
                                <li><a class="dropdown-item" href="#" data-value="{{ $partner->affiliate_partner_id }}">{{ $partner->affiliate_partner_name }}</a></li>
                                @endforeach
                            </ul>
                            <input type="hidden" id="affiliatePartner" name="affiliate_partner_id">
                        </div>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tariffListVersion" class="form-label fw-bold">Tariff List Version <span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control" id="tariffListVersion" placeholder="Calculate the billed amount first." readonly>
                        <input type="hidden" id="tariffListVersionRaw" name="tariff_list_version">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="dateApplied" class="form-label fw-bold">Date Applied <span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control" id="dateApplied" readonly>
                        <input type="hidden" id="appliedAtRaw" name="applied_at">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="dateToReapply" class="form-label fw-bold">Date to Reapply <span class="required-asterisk">*</span></label>
                        <input type="text" class="form-control" id="dateToReapply" readonly>
                        <input type="hidden" id="reapplyAtRaw" name="reapply_at">
                    </div>

                    <div class="form-group col-md-3 d-flex align-items-end">
                        <button id="submitBtn" type="submit" class="btn btn-success fw-bold w-100">SUBMIT</button>
                    </div>

                    <div id="patientsContainer" class="col-md-12 mb-4 d-none">
                        <div class="card mt-3">
                            <div class="card-header fw-bold">
                                <i class="fas fa-hospital-user me-3"></i>Select any one of the patients associated with the applicant. Only one is allowed.
                            </div>
                            <div class="card-body">
                                <div id="patientsList" class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
