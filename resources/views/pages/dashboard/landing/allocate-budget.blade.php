<link href="{{ asset('css/pages/dashboard/landing/allocate-budget.css') }}" rel="stylesheet">
<script src="{{ asset('js/pages/dashboard/landing/allocate-budget.js') }}"></script>

<div id="allocate-budget-modal-overlay" class="modal-overlay" style="display: none;">
    <div id="allocate-budget-modal-container" class="modal-container">
        <div class="modal-header">
            <h2>Allocate Budget</h2>
            <button id="allocate-budget-modal-close" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <div class="form-section">
                <h3 class="fw-bold">New Budget Allocation</h3>
                <form id="allocate-budget-form">
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="allocation-amount" class="fw-bold">Allocation Amount (in "₱") <span class="required-asterisk">*</span></label>
                            <input type="number" id="allocation-amount" class="form-control" step="0.01" min="0" placeholder="0.00">
                            <span id="allocation-amount-error" class="error-message" style="display: none;"></span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="direction-dropdown-btn" class="fw-bold">Direction <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="direction-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" disabled>Increase</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="direction-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="Increase">Increase</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Decrease">Decrease</a></li>
                                </ul>
                                <input type="hidden" id="direction-id" value="Increase">
                            </div>
                            <span id="allocation-direction-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="possessor-dropdown-btn" class="fw-bold">Possessor <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="possessor-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false">Select Possessor</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="possessor-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="AMPING">AMPING</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sponsor">Sponsor</a></li>
                                </ul>
                                <input type="hidden" id="possessor-id" value="">
                            </div>
                            <span id="possessor-type-error" class="error-message" style="display: none;"></span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="reason-dropdown-btn" class="fw-bold">Reason <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="reason-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" disabled>Select Reason</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="reason-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="Yearly Budget Provision">Yearly Budget Provision</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Supplementary Budget">Supplementary Budget</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sponsor Donation" style="display: none;">Sponsor Donation</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Budget Manipulation">Budget Manipulation</a></li>
                                </ul>
                                <input type="hidden" id="reason-id" value="">
                            </div>
                            <span id="allocation-reason-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div id="sponsor-container" class="row gx-3 gy-3 mb-3" style="display: none;">
                        <div class="form-group col-md-12">
                            <label for="sponsor-dropdown" class="fw-bold">Sponsor Name <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="sponsor-dropdown" data-bs-toggle="dropdown" aria-expanded="false">Select Sponsor</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="sponsor-dropdown" id="sponsor-list">
                                </ul>
                                <input type="hidden" id="sponsor-id" value="">
                            </div>
                            <span id="sponsor-name-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="remaining-amount" class="fw-bold">Remaining Amount (in "₱")</label>
                            <input type="number" id="remaining-amount" class="form-control readonly" step="0.01" min="0" placeholder="0.00" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="current-amount" class="fw-bold">Current Amount (in "₱")</label>
                            <input type="number" id="current-amount" class="form-control readonly" step="0.01" min="0" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <input type="hidden" id="amount-before-hidden" value="0.00">
                    <input type="hidden" id="amount-accum-hidden" value="0.00">
                </form>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" id="submit-allocation" class="btn btn-primary">CONFIRM BUDGET ALLOCATION</button>
            <button id="cancel-allocation-changes" class="btn btn-secondary">CANCEL</button>
        </div>
    </div>
</div>
