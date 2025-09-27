<div @if($show) class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" @else class="modal fade" style="display: none;" @endif tabindex="-1" role="dialog" aria-hidden="{{ $show ? 'false' : 'true' }}">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-title text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <span class="ms-1">WARNING</span>
                </div>

                <button type="button" wire:click="$set('show', false)" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body text-center pt-3 pb-0">
                <div class="mt-2 mb-3">
                    <i class="fas fa-check-circle fa-3x text-warning mb-4 bg-black rounded-circle"></i>
                    <h5 class="text-color fw-bold">Are you sure to do this?</h5>
                </div>

                <p class="modal-message">Do you want to apply this tariff list version for<br>ALL of the service types that have this version?</p>
            </div>

            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn btn-secondary action-buttons px-4" wire:click="$set('show', false)">
                    <i class="fas fa-times me-3"></i><span class="fs-5">CANCEL</span>
                </button>

                <button type="button" id="confirmApplyBtn" class="btn btn-success action-buttons px-4" wire:click="applyVersion">
                    <i class="fas fa-check me-3"></i><span class="fs-5">CONFIRM</span>
                </button>
            </div>
        </div>
    </div>
</div>
