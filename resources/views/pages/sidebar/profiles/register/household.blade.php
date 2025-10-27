<link href="{{ asset('css/pages/sidebar/profiles/register/household.css') }}" rel="stylesheet">
<script src="{{ asset('js/pages/sidebar/profiles/register/household.js') }}"></script>

<div class="modal-overlay" id="householdModal" style="display: none;">
    <div class="modal-container" id="modal-container">
        <div class="modal-header">
            <h2>Create Family / Household Name</h2>
            <button class="modal-close" onclick="closeHouseholdModal()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 005.7 7.11L10.59 12 5.7 16.89a1 1 0 101.41 1.41L12 13.41l4.89 4.89a1 1 0 001.41-1.41L13.41 12l4.89-4.89a1 1 0 000-1.4z"/></svg>
            </button>
        </div>

        <div class="modal-body">
            <form id="householdForm" method="POST" action="{{ route('profiles.households.store') }}">
                @csrf

                <div class="form-group">
                    <label for="household_name" class="form-label fw-bold" id="form-label">Family / Household Name</label>
                    <input type="text" id="household_name" name="household_name" class="form-control" required>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeHouseholdModal()">Cancel</button>
            <button class="btn btn-primary" onclick="createHousehold()">Create</button>
        </div>
    </div>
</div>
