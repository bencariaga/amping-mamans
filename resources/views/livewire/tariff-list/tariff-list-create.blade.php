<div @if($show) class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" @else class="modal fade" style="display: none;" @endif tabindex="-1" role="dialog" aria-hidden="{{ $show ? 'false' : 'true' }}">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-legend ms-2">
                    <i class="fas fa-plus-circle fa-fw"></i>
                    <span class="header-title ms-2">{{ $previewBase }}-{{ $previewNextNumber }} (New Version)</span>
                </div>

                <button type="button" wire:click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body p-2">
                <form wire:submit.prevent="create" id="tariffCreateFormModal">
                    <div class="form-content">
                        <div class="date-row mb-4">
                            <label class="effectivity-date fs-5" for="effectivity-date-modal">Effectivity Date:</label>

                            <div class="date-input-container">
                                <input type="date" id="effectivity-date-modal" name="effectivity_date" class="date-input form-control fs-5" wire:model.defer="effectivity_date" min="{{ now()->toDateString() }}" required>
                            </div>
                        </div>

                        <h5 class="select-service fw-bold mb-3">Select service types to include in this draft.</h5>

                        <div class="services-container" id="servicesContainerModal">
                            @foreach($services as $service)
                                <div class="form-check mb-3 d-flex align-items-center p-3 service-card ms-3 service-row">
                                    <input class="form-check-input service-checkbox" type="checkbox" value="{{ $service->service_id }}" id="service_modal_{{ $service->service_id }}" wire:model="selectedServices">
                                    <label class="form-check-label ms-3 service-label fs-5" for="service_modal_{{ $service->service_id }}">{{ $service->service_type }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            @error('selectedServices')
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                </div>
                            @enderror

                            @error('effectivity_date')
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end pb-3">
                        <button type="button" class="btn btn-secondary action-buttons" wire:click="closeModal">
                            <i class="fas fa-times me-3"></i><span class="fs-5">Cancel</span>
                        </button>

                        <button type="submit" class="btn btn-primary action-buttons">
                            <i class="fas fa-save me-3"></i><span class="fs-5">Save Draft</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
