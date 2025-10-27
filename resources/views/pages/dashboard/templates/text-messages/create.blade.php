@extends('layouts.personal-pages')

@section('title', 'Create SMS Template')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/text-messages.css') }}" rel="stylesheet">
    <style>
        .main-content-body {
            padding-top: 18rem;
            padding-bottom: 42px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/templates/text-messages.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('message-templates.list') }}" class="text-decoration-none text-white">SMS Templates</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info\">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('message-templates.create') }}" class="text-decoration-none text-white">Create SMS Template</a>
@endsection

@section('content')
    <div class="container" id="sms-template-container">
        <form id="sms-template-form" action="{{ route('message-templates.store') }}" method="POST">
            @csrf

            <div class="card placeholder-bank-card" id="placeholder-bank-card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">Template Placeholders</h6>
                </div>

                <div class="card-body" style="padding: 1.5rem;">
                    <div class="template-placeholder-group">
                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->first_name" data-label="Applicant's First Name">
                                First Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->middle_name" data-label="Applicant's Middle Name">
                                Middle Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->last_name" data-label="Applicant's Last Name">
                                Last Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->suffix" data-label="Applicant's Suffix">
                                Suffix
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-info text-color w-100 placeholder-btn" data-key="application->applied_at" data-label="Applied At">
                                Applied At
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->service_type" data-label="Service Type">
                                Service Type
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->affiliate_partner->affiliate_partner_name" data-label="Affiliate Partner">
                                Affiliate Partner
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->billed_amount" data-label="Billed Amount">
                                Billed Amount
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->assistance_amount" data-label="Assistance Amount">
                                Assistance Amount
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-info text-color w-100 placeholder-btn" data-key="application->reapply_at" data-label="Reapply At">
                                Reapply At
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="stack-layout">
                <div class="card template-input-card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">SMS Template Details</h6>
                    </div>

                    <div class="details-body">
                        <div class="tmp-title mb-4">
                            <label for="msg_tmp_title" class="form-label fw-bold" style="user-select: none;">TITLE (<span id="title-count" class="counter">0</span> / 30 characters)</label>
                            <input type="text" name="msg_tmp_title" id="msg_tmp_title" class="form-control form-control-lg" placeholder="Example: Text Message Template 1" maxlength="30" required>
                            <div class="invalid-feedback" id="title-error"></div>
                        </div>

                        <div class="tmp-text-context mb-4" style="margin-top: 1.5rem;">
                            <label for="msg_tmp_text" class="form-label fw-bold"><span style="user-select: none;">TEXT CONTENT (<span id="char-count" class="counter">0</span> / 1000 characters)</span></label><span type="button" class="do-buttons ms-3 me-2" id="undo-button" disabled><i class="fas fa-undo"></i></span><span type="button" class="do-buttons ms-1" id="redo-button" disabled><i class="fas fa-redo"></i></span>
                            <textarea name="msg_tmp_text" id="msg_tmp_text" class="form-control sms-template-textarea" rows="12" placeholder="Start typing your template here and use template placeholders." maxlength="1000" required></textarea>
                            <div class="invalid-feedback" id="text-error"></div>
                        </div>
                    </div>
                </div>

                <div class="card preview-card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="card-title mb-0">Output:<span class="float-end"><span id="sms-length-char" class="counter">0</span> (<span id="sms-credit-count">1 / 5</span> prepaid text message credits)</span></h6>
                    </div>

                    <div class="preview-body">
                        <div id="output-text-preview" class="sms-preview-output d-flex align-items-start justify-content-start">
                            <div class="preview-placeholder">
                                <div class="muted-text fw-semibold">Live preview of text content goes here.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.add-sms-tmp')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
