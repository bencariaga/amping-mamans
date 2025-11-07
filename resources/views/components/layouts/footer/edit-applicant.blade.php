<a type="button" id="backToListBtn" class="footer-btn" href="{{ route('profiles.applicants.list') }}">
    <div class="nav-icon"><i class="fa fa-long-arrow-alt-left"></i></div>
    <div class="nav-text">Back to List</div>
</a>

<a type="submit" id="updateApplicantBtn" class="footer-btn"
    onclick="document.getElementById('profileSection').requestSubmit()">
    <div class="nav-icon"><i class="fas fa-save"></i></div>
    <div class="nav-text">Save Changes</div>
</a>
