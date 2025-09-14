@section('footer')
    <a type="button" id="backToListBtn" class="footer-btn" href="{{ route('profiles.applicants.list') }}">
        <div class="nav-icon"><i class="fa-solid fa-arrow-left"></i></div>
        <div class="nav-text">Back to List</div>
    </a>

    <a id="addApplicantBtn" type="submit" class="footer-btn" onclick="document.getElementById('profileSection').requestSubmit()">
        <div class="nav-icon"><i class="fas fa-plus"></i></div>
        <div class="nav-text">Add Applicant</div>
    </a>

    @include('components.layouts.footer.profile-buttons-3')
@endsection
