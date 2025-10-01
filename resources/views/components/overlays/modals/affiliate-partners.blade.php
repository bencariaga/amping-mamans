<link href="{{ asset('css/components/overlays/modals/affiliate-partners.css') }}" rel="stylesheet">
<script src="{{ asset('js/components/overlays/modals/affiliate-partners.js') }}"></script>

<div id="affiliate-partners-modal-overlay" class="modal-overlay" style="display: none;">
    <div id="affiliate-partners-modal-container" class="modal-container">
        <div class="modal-header">
            <h2>Manage Affiliate Partners</h2>
            <button id="affiliate-partners-modal-close" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-section" id="form-section">
                <h3 class="fw-bold">Add New Affiliate Partner</h3>
                <form id="add-affiliate-partner-form">
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="affiliate-partner-name" class="fw-bold">Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="affiliate-partner-name" name="affiliate-partner-name">
                            <span id="affiliate-partner-name-error" class="error-message" style="display: none;"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="affiliate-partner-type-dropdown" class="fw-bold">Type <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" id="affiliate-partner-type-dropdown" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu" aria-labelledby="affiliate-partner-type-dropdown">
                                    <li><a class="dropdown-item" href="#" data-value="Hospital / Clinic">Hospital / Clinic</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Pharmacy / Drugstore">Pharmacy / Drugstore</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                </ul>
                                <input type="hidden" id="affiliate-partner-type" name="affiliate-partner-type" value="">
                            </div>
                            <span id="affiliate-partner-type-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>
                    <button type="submit" id="add-affiliate-partner" class="btn btn-primary">ADD AFFILIATE PARTNER</button>
                </form>
            </div>

            <div class="list-section">
                <h3 class="fw-bold">Existing Affiliate Partners</h3>
                <ul id="affiliate-partners-list" class="affiliate-partners-list">

                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button id="cancel-affiliate-partners-changes" class="btn btn-secondary">CANCEL</button>
            <button id="confirm-affiliate-partners-changes" class="btn btn-success">CONFIRM CHANGES</button>
        </div>
    </div>
</div>
