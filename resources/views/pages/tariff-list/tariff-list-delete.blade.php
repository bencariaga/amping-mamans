<div id="deleteTariffModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-title text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <span class="ms-1">Deleting <strong id="delete-tariff-id-display"></strong></span>
                </div>

                <button type="button" onclick="closeDeleteModal()" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body text-center pt-3 pb-0">
                <div class="mt-2 mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-4"></i>
                    <h5 class="text-color fw-bold">Are you sure to do this?</h5>
                </div>

                <p class="modal-message pt-3">Do you want to delete this tariff list version?<br>This action cannot be undone.
                    <br></p>
            </div>

            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn btn-secondary action-buttons px-4" onclick="closeDeleteModal()">
                    <i class="fas fa-times me-3"></i><span class="fs-5">Cancel</span>
                </button>

                <button type="button" class="btn btn-danger action-buttons px-4" id="confirmDeleteButton">
                    <i class="fas fa-check me-3"></i><span class="fs-5">Confirm</span>
                </button>
            </div>
        </div>
    </div>
</div>
