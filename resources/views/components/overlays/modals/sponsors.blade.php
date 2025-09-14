<link href="{{ asset('css/components/overlays/modals/sponsors.css') }}" rel="stylesheet">
<script src="{{ asset('js/components/overlays/modals/sponsors.js') }}"></script>

<div id="sponsors-modal-overlay" class="modal-overlay" style="display: none;">
    <div id="sponsors-modal-container" class="modal-container">
        <div class="modal-header">
            <h2>Manage Sponsors</h2>
            <button id="sponsors-modal-close" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <div class="form-section" id="form-section">
                <h3 class="fw-bold">Add New Sponsor</h3>
                <form id="add-sponsor-form">
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-4">
                            <label for="sponsor-first-name" class="fw-bold">First Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="sponsor-first-name" class="form-control" required>
                            <span id="sponsor-first-name-error" class="error-message" style="display: none;"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="sponsor-middle-name" class="fw-bold">Middle Name</label>
                            <input type="text" id="sponsor-middle-name" class="form-control">
                            <span id="sponsor-middle-name-error" class="error-message" style="display: none;"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="sponsor-last-name" class="fw-bold">Last Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="sponsor-last-name" class="form-control" required>
                            <span id="sponsor-last-name-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-2">
                            <label for="sponsor-suffix-dropdown" class="fw-bold">Suffix</label>
                            <div class="dropdown" id="sponsor-suffix">
                                <button class="btn dropdown-toggle w-100" type="button" id="sponsor-suffix-dropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu w-100" aria-labelledby="sponsor-suffix-dropdown">
                                    <li><a class="dropdown-item" href="#" data-value=""></a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Jr.">Jr.</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sr.">Sr.</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="II">II</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="III">III</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="IV">IV</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="V">V</a></li>
                                </ul>
                                <input type="hidden" id="sponsor-suffix-input" value="">
                            </div>
                            <span id="sponsor-suffix-error" class="error-message" style="display: none;"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="sponsor-type-dropdown" class="fw-bold">Sponsor Type <span class="required-asterisk">*</span></label>
                            <div class="dropdown" id="sponsor-type">
                                <button class="btn dropdown-toggle w-100" type="button" id="sponsor-type-dropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu w-100" aria-labelledby="sponsor-type-dropdown" id="sponsor-type-dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-value="Politician">Politician</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Business Owner">Business Owner</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Non-Governmental">Non-Governmental</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Non-Profit">Non-Profit</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                </ul>
                                <input type="hidden" id="sponsor-type-input" value="">
                            </div>
                            <span id="sponsor-type-error" class="error-message" style="display: none;"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designation" class="fw-bold">Designation</label>
                            <input type="text" id="designation" class="form-control">
                            <span id="designation-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-12">
                            <label for="organization-name" class="fw-bold">Organization Name</label>
                            <input type="text" id="organization-name" class="form-control">
                            <span id="organization-name-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <button type="submit" id="add-sponsor" class="btn btn-primary">ADD SPONSOR</button>
                </form>
            </div>

            <div class="list-section">
                <h3 class="fw-bold">Existing Sponsors</h3>
                <ul id="sponsors-list" class="sponsors-list">

                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button id="cancel-sponsors-changes" class="btn btn-secondary">CANCEL</button>
            <button id="confirm-sponsors-changes" class="btn btn-success">CONFIRM CHANGES</button>
        </div>
    </div>
</div>
