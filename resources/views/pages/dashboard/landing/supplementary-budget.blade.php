<link href="{{ asset('css/pages/dashboard/landing/supplementary-budget.css') }}" rel="stylesheet">
<script src="{{ asset('js/pages/dashboard/landing/supplementary-budget.js') }}"></script>

<div id="supplementary-budget-modal-overlay" class="modal-overlay" style="display: none;">
    <div id="supplementary-budget-modal-container" class="modal-container">
        <div class="modal-header">
            <h2>Supplementary Budget</h2>
            <button id="supplementary-budget-modal-close" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <div class="form-section">
                <h3 class="fw-bold">Set Supplementary Budget</h3>
                <form id="supplementary-budget-form">
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="supplementary-allocation-amount" class="fw-bold">Allocation Amount (in "₱") <span class="required-asterisk">*</span></label>
                            <input type="number" id="supplementary-allocation-amount" class="form-control" step="0.01" min="0" placeholder="0.00">
                            <span id="supplementary-allocation-amount-error" class="error-message" style="display: none;"></span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="supplementary-direction-dropdown-btn" class="fw-bold">Direction <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="supplementary-direction-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" disabled>Increase</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="supplementary-direction-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="Increase">Increase</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Decrease">Decrease</a></li>
                                </ul>
                                <input type="hidden" id="supplementary-direction-id" value="Increase">
                            </div>
                            <span id="supplementary-allocation-direction-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="supplementary-possessor-dropdown-btn" class="fw-bold">Possessor <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="supplementary-possessor-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" disabled>AMPING</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="supplementary-possessor-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="AMPING">AMPING</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sponsor">Sponsor</a></li>
                                </ul>
                                <input type="hidden" id="supplementary-possessor-id" value="AMPING">
                            </div>
                            <span id="supplementary-possessor-type-error" class="error-message" style="display: none;"></span>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="supplementary-reason-dropdown-btn" class="fw-bold">Reason <span class="required-asterisk">*</span></label>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle w-100" type="button" id="supplementary-reason-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false" disabled>Supplementary Budget</button>
                                <ul class="dropdown-menu w-100" aria-labelledby="supplementary-reason-dropdown-btn">
                                    <li><a class="dropdown-item" href="#" data-value="Yearly Budget Provision">Yearly Budget Provision</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Supplementary Budget">Supplementary Budget</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Sponsor Donation" style="display: none;">Sponsor Donation</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="Budget Manipulation">Budget Manipulation</a></li>
                                </ul>
                                <input type="hidden" id="supplementary-reason-id" value="Supplementary Budget">
                            </div>
                            <span id="supplementary-allocation-reason-error" class="error-message" style="display: none;"></span>
                        </div>
                    </div>

                    <div class="row gx-3 gy-3 mb-3">
                        <div class="form-group col-md-6">
                            <label for="supplementary-remaining-amount" class="fw-bold">Remaining Amount (in "₱")</label>
                            <input type="number" id="supplementary-remaining-amount" class="form-control readonly" step="0.01" min="0" placeholder="0.00" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="supplementary-current-amount" class="fw-bold">Current Amount (in "₱")</label>
                            <input type="number" id="supplementary-current-amount" class="form-control readonly" step="0.01" min="0" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <input type="hidden" id="supplementary-amount-before-hidden" value="0.00">
                    <input type="hidden" id="supplementary-amount-accum-hidden" value="0.00">
                </form>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" id="submit-supplementary-allocation" class="btn btn-primary">CONFIRM BUDGET ALLOCATION</button>
            <button id="cancel-supplementary-allocation-changes" class="btn btn-secondary">CANCEL</button>
        </div>
    </div>
</div>
