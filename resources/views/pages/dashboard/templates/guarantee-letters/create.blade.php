@extends('layouts.personal-pages')

@section('title', 'Create GL Template')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letters.css') }}" rel="stylesheet">
    <style>
        .main-content-body {
            padding-top: 18rem;
            padding-bottom: 42px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/templates/guarantee-letters.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.list') }}" class="text-decoration-none text-white">GL Templates</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.create') }}" class="text-decoration-none text-white">Create GL Template</a>
@endsection

@section('content')
    <div class="container" id="gl-template-container">
        <form id="gl-template-form" action="{{ route('gl-templates.store') }}" method="POST">
            @csrf

            <div class="card placeholder-bank-card" id="placeholder-bank-card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">Template Placeholders</h6>
                </div>

                <div class="card-body" style="padding: 1rem;">
                    <div class="template-placeholder-group">
                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->first_name" data-label="Applicant's First Name">
                                Applicant's<br>First Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->middle_name" data-label="Applicant's Middle Name">
                                Applicant's<br>Middle Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->last_name" data-label="Applicant's Last Name">
                                Applicant's<br>Last Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->suffix" data-label="Applicant's Suffix Name">
                                Applicant's<br>Suffix Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->service_type" data-label="Service Type">
                                Service<br>Type
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->billed_amount" data-label="Billed Amount">
                                Billed<br>Amount
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-info text-color w-100 placeholder-btn" data-key="application->applied_at" data-label="Applied At">
                                Applied At
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->first_name" data-label="Patient's First Name">
                                Patient's<br>First Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->middle_name" data-label="Patient's Middle Name">
                                Patient's<br>Middle Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->last_name" data-label="Patient's Last Name">
                                Patient's<br>Last Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->suffix" data-label="Patient's Suffix Name">
                                Patient's<br>Suffix Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->affiliate_partner->affiliate_partner_name" data-label="Affiliate Partner">
                                Affiliate<br>Partner
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->assistance_amount" data-label="Assistance Amount">
                                Assistance<br>Amount
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-info text-color w-100 placeholder-btn" data-key="application->applicant->barangay" data-label="Barangay">
                                Barangay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="stack-layout">
                <div class="card template-input-card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">GL Template Details</h6>
                    </div>

                    <div class="details-body">
                        <div class="tmp-title mb-4">
                            <label for="gl_tmp_title" class="form-label fw-bold" style="user-select: none;">TITLE (<span id="title-count" class="counter">0</span> / 30 characters)</label>
                            <input type="text" name="gl_tmp_title" id="gl_tmp_title" class="form-control form-control-lg" placeholder="Example: Guarantee Letter Template 1" maxlength="30" required>
                            <div class="invalid-feedback" id="title-error"></div>
                        </div>

                        <div class="tmp-text-context mb-4" style="margin-top: 1.5rem;">
                            <label for="gl_content" class="form-label fw-bold"><span style="user-select: none;">CONTENT (<span id="char-count" class="counter">0</span> / 5000 characters)</span></label><span type="button" class="do-buttons ms-3 me-2" id="undo-button" disabled><i class="fas fa-undo"></i></span><span type="button" class="do-buttons ms-1" id="redo-button" disabled><i class="fas fa-redo"></i></span>
                            <textarea name="gl_content" id="gl_content" class="form-control gl-template-textarea" rows="12" placeholder="Start typing your template here and use template placeholders." maxlength="5000" required></textarea>
                            <div class="invalid-feedback" id="text-error"></div>
                        </div>

                        <input type="hidden" name="signers" id="signers" value="Mayor,Executive Assistant">
                        <input type="hidden" name="signatures" id="signatures" value="LORELIE GERONIMO-PACQUIAO,MARITESS D. AMBUANG, MMPA">
                    </div>
                </div>

                <div class="card preview-card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="card-title mb-0">Output:<span class="float-end"><span id="gl-length-char" class="counter">0</span></span></h6>
                    </div>

                    <div class="preview-body">
                        <div id="output-text-preview" class="gl-preview-output d-flex align-items-start justify-content-start">
                            <div class="preview-placeholder">
                                <div class="muted-text fw-semibold">Live preview of content goes here.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.add-gl-tmp')
    @include('components.layouts.footer.profile-buttons-3')
@endsection
