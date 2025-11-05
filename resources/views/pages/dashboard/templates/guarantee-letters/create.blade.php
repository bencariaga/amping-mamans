@extends('layouts.personal-pages')

@section('title', 'Create GL Template')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letters.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letter-template.css') }}" rel="stylesheet">
    <style>
        .main-content-body {
            padding-top: 20rem;
            padding-bottom: 4rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/dashboard/templates/guarantee-letters.js') }}"></script>
    <script src="{{ asset('js/pages/dashboard/templates/guarantee-letter-template.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.list') }}" class="text-decoration-none text-white">GL Templates</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.create') }}" class="text-decoration-none text-white">Create GL Template</a>
@endsection

@section('content')
    <div class="container" id="gl-template-container">
        <form id="gl-template-form" action="{{ route('gl-templates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" id="program-head-data" value="{{ $programHead ? json_encode($programHead->member) : '' }}">
            <input type="hidden" id="old-gl-content" value="{{ old('gl_content', '') }}">
            <input type="hidden" id="old-signers" value="{{ old('signers', '') }}">

            <div class="card placeholder-bank-card border-0" id="placeholder-bank-card">
                <div class="card-body px-4 pt-4 pb-2">
                    <div class="template-placeholder-group">
                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->first_name" data-label="Applicant's First Name">
                                Applicant's<br>First Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="applicant->client->member->middle_name" data-label="Applicant's Middle Initial">
                                Applicant's<br>Middle Initial
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
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->service" data-label="Service">
                                Service
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->billed_amount" data-label="Billed Amount">
                                Billed<br>Amount
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->applicant->barangay" data-label="Barangay">
                                Barangay
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->application_date" data-label="Application Date">
                                Application<br>Date
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->first_name" data-label="Patient's First Name">
                                Patient's<br>First Name
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-primary text-color w-100 placeholder-btn" data-key="patient->client->member->middle_name" data-label="Patient's Middle Initial">
                                Patient's<br>Middle Initial
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
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->assistance_amount_spelled_out" data-label="Assistance Amount (Spelled Out)">
                                Ass. Amount<br>(Spelled Out)
                            </button>
                        </div>

                        <div class="template-placeholder">
                            <button type="button" class="btn btn-outline-success text-color w-100 placeholder-btn" data-key="application->reapplication_date" data-label="Reapplication Date">
                                Reapplication<br>Date
                            </button>
                        </div>
                    </div>

                    <div class="tmp-title mt-4 mb-1 d-flex flex-row">
                        <input type="text" name="gl_tmp_title" id="gl_tmp_title" class="form-control border-black m-0" placeholder="e.g. Weekday GL Template" maxlength="30" required value="{{ old('gl_tmp_title', '') }}">

                        <div class="formatting-toolbar ms-2 mb-2" id="gl-formatting-toolbar">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-label="Undo">
                                    <i class="fas fa-undo"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-label="Redo">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="bold" title="Bold">
                                    <i class="fas fa-bold"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="italic" data-toggle="tooltip" data-placement="top" title="Italic">
                                    <i class="fas fa-italic"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="underline" data-toggle="tooltip" data-placement="top" title="Underline">
                                    <i class="fas fa-underline"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="pesoSign" data-toggle="tooltip" data-placement="top" title="Philippine Peso Sign">
                                    <span class="fw-normal">₱</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="justifyLeft" data-toggle="tooltip" data-placement="top" title="Align Left">
                                    <i class="fas fa-align-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="justifyCenter" data-toggle="tooltip" data-placement="top" title="Align Center">
                                    <i class="fas fa-align-center"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="justifyRight" data-toggle="tooltip" data-placement="top" title="Align Right">
                                    <i class="fas fa-align-right"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="justifyFull" title="Justify">
                                    <i class="fas fa-align-justify"></i>
                                </button>
                            </div>

                            <div class="btn-group ms-2" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="insertTab" data-toggle="tooltip" data-placement="top" title="Insert Tab / Indent">
                                    <span>Tab</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary format-btn" data-command="removeFormat" data-toggle="tooltip" data-placement="top" title="Clear Formatting">
                                    <span>Clear Formatting</span>
                                </button>
                            </div>

                            <div class="form-check d-flex align-items-center ms-3">
                                <input class="form-check-input my-0" type="checkbox" id="use-program-head" value="1">
                                <label class="form-check-label ms-3 me-2 fw-semibold" for="use-program-head">
                                    Let Program Head be a Signer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="stack-layout">
                <div class="card template-input-card border-0">
                    <div class="details-body">
                        <div class="guarantee-letter-preview-container">
                            <div id="guarantee-letter-container">
                                <div class="guarantee-letter-container">
                                    <div class="header">
                                        <div class="left-section">
                                            <img src="{{ asset('images/main/general-santos-seal.png') }}" alt="GenSan Seal" class="logo" id="gensan-seal">
                                            <img src="{{ asset('images/main/amping-logo.png') }}" alt="AMPING Logo" class="logo" id="amping-logo">
                                        </div>

                                        <div class="center-section">
                                            <div class="republic">Republic of the Philippines</div>
                                            <div class="office-title">OFFICE OF THE CITY MAYOR</div>
                                            <div class="amping-title">A . M . P . I . N . G</div>
                                            <div class="program-desc">Auxiliaries and Medical Program<br>for Individuals and Needy Generals</div>
                                            <div class="city">General Santos City</div>
                                            <div class="email">E-mail Address: <span class="email-link">gensanamping@gmail.com</span></div>
                                        </div>

                                        <div class="right-section">
                                            <img src="{{ asset('images/main/disiplina-muna.png') }}" alt="Disiplina Muna" class="disiplina-muna">
                                            <div class="slogan">"Gobyernong Malinis,<br>Pag-unlad ay Mabilis."</div>
                                        </div>
                                    </div>

                                    <div class="document-title">GUARANTEE LETTER</div>

                                    <div class="reference-box">
                                        <span>Application No. <b>YEAR-00000</b></span>
                                        <span>Guarantee Letter No. <b>YEAR-00000</b></span>
                                    </div>

                                    <div class="letter-section" id="editable-letter-section" contenteditable="true"></div>

                                    <div class="signature-section">
                                        <div class="mayor-signature">
                                            <input type="file" id="signature-file-1" name="signature_file_1" accept="image/*" style="display: none;">

                                            <div class="signature-box" id="signature-box-1">
                                                <div class="signature-rectangle">
                                                    <img class="signature-image" id="sig-img-1" src="" alt="Mayor's Signature">
                                                    <span class="signature-text">Click to upload an image of signature ideally with transparent background.</span>
                                                    <span class="signature-plus" id="sig-plus-1">+</span>
                                                    <div class="signature-overlay">
                                                        <i class="fas fa-repeat"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mayor-name" id="signer-1">
                                                <input type="text" class="signer-input" id="signer-1-first" placeholder="First" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-1-middle" placeholder="Middle" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-1-last" placeholder="Last" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-1-suffix" placeholder="Suffix" maxlength="10">
                                                <input type="text" class="signer-input" id="signer-1-pnl" placeholder="Post-Nominal Letters" maxlength="20">
                                            </div>
                                            <div class="mayor-title">City Mayor</div>
                                        </div>

                                        <div class="authority">By the Authority of the City Mayor</div>

                                        <div class="assistant-signature">
                                            <input type="file" id="signature-file-2" name="signature_file_2" accept="image/*" style="display: none;">

                                            <div class="signature-box" id="signature-box-2">
                                                <div class="signature-rectangle">
                                                    <img class="signature-image" id="sig-img-2" src="" alt="Executive Assistant Signature">
                                                    <span class="signature-text">Click to upload an image of signature ideally with transparent background.</span>
                                                    <span class="signature-plus" id="sig-plus-2">+</span>
                                                    <div class="signature-overlay">
                                                        <i class="fas fa-repeat"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="assistant-name" id="signer-2">
                                                <input type="text" class="signer-input" id="signer-2-first" placeholder="First" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-2-middle" placeholder="Middle" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-2-last" placeholder="Last" maxlength="20">
                                                <input type="text" class="signer-input" id="signer-2-suffix" placeholder="Suffix" maxlength="10">
                                                <input type="text" class="signer-input" id="signer-2-pnl" placeholder="Post-Nominal Letters" maxlength="20">
                                            </div>
                                            <div class="assistant-title">Executive Assistant III</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="gl_content" id="gl_content">
                        <input type="hidden" name="signers" id="signers">
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
