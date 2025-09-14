<link href="{{ asset('css/components/overlays/modals/occupations.css') }}" rel="stylesheet">
<script src="{{ asset('js/components/overlays/modals/occupations.js') }}"></script>

<div id="occupations-modal-overlay" class="modal-overlay" style="display: none;">
    <div id="occupations-modal-container" class="modal-container">
        <div class="modal-header">
            <h2>Manage Occupations</h2>
            <button id="occupations-modal-close" class="modal-close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-section" id="form-section">
                <h3 class="fw-bold">Add New Occupation</h3>
                <form id="add-occupation-form">
                    <div class="form-group">
                        <label for="new-occupation-name" class="fw-bold">Occupation Name <span class="required-asterisk">*</span></label>
                        <input type="text" id="new-occupation-name">
                        <span id="new-occupation-name-error" class="error-message" style="display: none;"></span>
                    </div>
                    <button type="submit" id="add-occupation" class="btn btn-primary">ADD OCCUPATION</button>
                </form>
            </div>

            <div class="list-section">
                <h3 class="fw-bold">Existing Occupations</h3>
                <ul id="occupations-list" class="occupations-list">

                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button id="cancel-occupations-changes" class="btn btn-secondary">CANCEL</button>
            <button id="confirm-occupations-changes" class="btn btn-success">CONFIRM CHANGES</button>
        </div>
    </div>
</div>
