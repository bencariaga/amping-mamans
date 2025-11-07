<div id="createTariffModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-legend ms-2">
                    <i class="fas fa-plus-circle fa-fw"></i>
                    <span class="header-title ms-2">Create Tariff List Draft</span>
                </div>

                <button type="button" onclick="closeCreateModal()" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body p-2">
                <form id="tariffCreateFormModal">
                    <div class="form-content">
                        <div class="date-row mb-4">
                            <label class="effectivity-date fs-5" for="effectivity-date-input">Effectivity Date:</label>

                            <div class="date-input-container">
                                <input type="date" id="effectivity-date-input" name="effectivity_date" class="date-input form-control fs-5" min="{{ now()->addDay()->toDateString() }}" required>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="service-label fs-5">Select service type/s to include in this draft.</label>
                        </div>

                        <div class="services-container" id="servicesContainerModal">
                            @foreach($services as $service)
                                <div class="form-check mb-3 d-flex align-items-center p-3 service-card ms-3 service-row">
                                    <input class="form-check-input service-checkbox" type="checkbox" name="selectedServices[]" value="{{ $service->service_id }}" id="service_modal_{{ $service->service_id }}">
                                    <label class="form-check-label ms-3 service-label fs-5" for="service_modal_{{ $service->service_id }}">{{ $service->service_type }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-danger" id="selectedServices-error" style="display: none;">
                                <i class="fas fa-exclamation-circle me-2"></i><span id="selectedServices-error-text">Please check at least one service type to include in this draft.</span>
                            </div>

                            <div class="alert alert-danger" id="effectivity_date-error" style="display: none;">
                                <i class="fas fa-exclamation-circle me-2"></i><span id="effectivity_date-error-text"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end pb-3">
                        <button type="button" class="btn btn-secondary action-buttons" onclick="closeCreateModal()">
                            <i class="fas fa-times me-3"></i><span class="fs-5">Cancel</span>
                        </button>

                        <button type="submit" class="btn btn-primary action-buttons" id="createDraftButton">
                            <i class="fas fa-save me-3"></i><span class="fs-5">Create Draft</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
