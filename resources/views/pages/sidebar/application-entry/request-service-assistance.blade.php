@extends('layouts.personal-pages')

@section('title', 'Request Service Assistance')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/application-entry/request-service-assistance.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/application-entry/request-service-assistance.js') }}?v={{ time() }}&fix=1"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('request-service-assistance') }}" class="text-decoration-none text-reset">Request Service Assistance</a>
@endsection

@section('content')
    <div class="container">
        <form id="assistanceRequestForm">
            @csrf

            <input type="hidden" id="applicantFirstName" name="applicant_first_name">
            <input type="hidden" id="applicantMiddleName" name="applicant_middle_name">
            <input type="hidden" id="applicantLastName" name="applicant_last_name">
            <input type="hidden" id="applicantSuffix" name="applicant_suffix">
            <input type="hidden" id="applicantId" name="applicant_id">
            <input type="hidden" id="patientFirstName" name="patient_first_name">
            <input type="hidden" id="patientMiddleName" name="patient_middle_name">
            <input type="hidden" id="patientLastName" name="patient_last_name">
            <input type="hidden" id="patientSuffix" name="patient_suffix">
            <input type="hidden" id="patientId" name="patient_id">
            <input type="hidden" id="messageTemplateId" name="msg_tmp_id">
            <input type="hidden" id="autoApplicantData" value="{{ json_encode($applicantData ?? []) }}">

            <div class="two-container-handler" id="twoContainerHandler">
                <div class="left-container">
                    <div class="row gx-3 gy-2">
                        <div class="form-group col-md-6">
                            <label for="phoneNumber" class="form-label fw-bold">Phone Number <span class="required-asterisk">*</span></label>
                            <div class="input-group">
                                <input type="text" style="width: 50%;" class="form-control" id="phoneNumber" placeholder="Example: 0912-345-6789" name="phone_number" maxlength="13">
                                <button class="btn btn-primary fw-bold gap-2" type="button" id="verifyBtn" style="padding: 0 12px;"><i class="far fa-check-circle fa-lg fa-fw"></i><i class="fas fa-phone fa-fw"></i></button>
                            </div>
                            <small id="phoneVerificationMessage" class="form-text mt-1 d-none"></small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="applicantName" class="form-label fw-bold">Applicant Name <span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control" id="applicantName" placeholder="Verify the phone number first." readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="patientNameDropdownBtn" class="form-label fw-bold">Patient Name <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button id="patientNameDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn w-100 fw-bold d-flex justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false" disabled>Verify the phone number first.</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="patientNameDropdownBtn" id="patientNameDropdownList">
                                    <li><a class="dropdown-item" href="#" data-value="">Select a patient.</a></li>
                                </ul>
                                <input type="hidden" id="patientName" name="patient_name_hidden">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
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

                        <div class="form-group col-md-6">
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

                        <div class="form-group col-md-6">
                            <label for="billedAmount" class="form-label fw-bold">Billed Amount (in ₱) <span class="required-asterisk">*</span></label>
                            <div class="input-group">
                                <input type="text" style="width: 50%" class="form-control" id="billedAmount" placeholder="Enter billed amount." name="billed_amount" min="0">
                                <button class="btn btn-primary fw-bold gap-2" type="button" id="calculateBtn" style="padding: 0 12px;"><i class="fas fa-calculator fa-lg fa-fw"></i><i class="fas fa-coins fa-fw"></i></button>
                            </div>
                            <small id="billedAmountMessage" class="form-text mt-1 d-none"></small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="assistanceAmount" class="form-label fw-bold">Assistance Amount (in ₱) <span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control" id="assistanceAmount" placeholder="Calculate the billed amount first." readonly>
                            <input type="hidden" id="assistanceAmountRaw" name="assistance_amount">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="tariffListVersion" class="form-label fw-bold">Tariff List Version <span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control" id="tariffListVersion" placeholder="Calculate the billed amount first." readonly>
                            <input type="hidden" id="tariffListVersionRaw" name="tariff_list_version">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dateApplied" class="form-label fw-bold">Date Applied <span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control" id="dateApplied" readonly>
                            <input type="hidden" id="appliedAtRaw" name="applied_at">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dateToReapply" class="form-label fw-bold">Date to Reapply <span class="required-asterisk">*</span></label>
                            <input type="text" class="form-control" id="dateToReapply" readonly>
                            <input type="hidden" id="reapplyAtRaw" name="reapply_at">
                        </div>

                        <div class="form-group col-md-6 d-flex align-items-end">
                            <a type="button" class="btn btn-primary w-100" id="msgTmpBtn" href="{{ route('message-templates.list') }}">MESSAGE TEMPLATES</a>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="messageTemplateDropdownBtn" class="form-label fw-bold">Message Template <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button id="messageTemplateDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn w-100 fw-bold d-flex justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select an SMS template.</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="messageTemplateDropdownBtn" id="messageTemplateDropdownList">
                                    <li><a class="dropdown-item" href="#" data-value="" data-text="">Select an SMS template.</a></li>
                                    @foreach($message_templates as $template)
                                        <li><a class="dropdown-item" href="#" data-value="{{ $template->msg_tmp_id }}" data-text="{{ $template->msg_tmp_text }}">{{ $template->msg_tmp_title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="right-container">
                    <label for="messagePreviewText" class="form-label fw-bold">Text Message Preview <span class="required-asterisk">*</span></label>
                    <textarea id="messagePreviewText" name="message_text" class="message-preview form-control border rounded" rows="22" required readonly></textarea>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.add-application')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
