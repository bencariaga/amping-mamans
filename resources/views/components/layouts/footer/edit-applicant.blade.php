@section('footer')
    <a type="button" id="backToListBtn" class="footer-btn" href="{{ route('profiles.applicants.list') }}">
        <div class="nav-icon"><i class="fa-solid fa-arrow-left"></i></div>
        <div class="nav-text">Back to List</div>
    </a>

    <a type="submit" id="updateApplicantBtn" class="footer-btn" onclick="document.getElementById('profileSection').requestSubmit()">
        <div class="nav-icon"><i class="fas fa-save"></i></div>
        <div class="nav-text">Save Changes</div>
    </a>

    <a id="deleteApplicantBtn" class="footer-btn" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <div class="nav-icon"><i class="fa-solid fa-trash-can"></i></div>
        <div class="nav-text">Delete Account</div>
    </a>

    @include('components.layouts.footer.profile-buttons-4')
@endsection
