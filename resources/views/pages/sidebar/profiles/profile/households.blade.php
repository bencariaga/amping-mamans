@extends('layouts.personal-pages')

@section('title', 'Household Profile')

@push('styles')
    <link href="{{ asset('css/pages/sidebar/profiles/profile/households.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/pages/sidebar/profiles/profile/households.js') }}"></script>
    <script>
        const OCCUPATIONS_LIST = @json($occupations);
        const MEMBER_SEARCH_URL = "{{ route('api.households.search') }}";
        const VERIFY_NAME_URL = "{{ route('api.households.verify-name') }}";
        const VERIFY_FULL_NAME_URL = "{{ route('api.households.verify-full-name') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
    </script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.households.list') }}" class="text-decoration-none text-white">Households</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.households.show', $household->household_id) }}" class="text-decoration-none text-white">{{ $household->household_name }} Family / Household Profile</a>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <form method="POST" action="{{ route('profiles.households.update', $household->household_id) }}" id="householdProfileForm">
            @csrf
            @method('PUT')

            <div class="household-header-section py-0 d-flex flex-row align-items-center">
                <div class="d-flex flex-row align-items-center" id="hh-form-left">
                    <input type="text" id="household-name" name="household_name" class="form-control fw-semibold" value="{{ old('household_name', $household->household_name) }}" required>
                    <label class="fw-bold ms-4 fs-4">Family / Household</label>
                </div>
                <div class="d-flex flex-row" id="hh-form-right">
                    <p class="ms-4 pt-3"><b>NOTE:</b> In the first row of the table for household members, type in the first row the first applicant registered into this system in order for it to show up the <a href="{{ route('profiles.households.list') }}" class="hyperlink">Households</a> page.</p>
                </div>
            </div>

            <div class="household-form-section">
                <h3 class="m-0">Family Composition (Household Counts)</h3>

                <div class="table-responsive">
                    <table class="household-table">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Last Name <label id="household-required-asterisk">*</label></th>
                                <th>First Name <label id="household-required-asterisk">*</label></th>
                                <th>Middle Name</th>
                                <th>Suffix</th>
                                <th>Birthdate</th>
                                <th>Age</th>
                                <th>Civil Status</th>
                                <th>Relationship with Applicant <label id="household-required-asterisk">*</label></th>
                                <th>Educational Attainment</th>
                                <th>Occupation</th>
                                <th>Estimated Monthly Income</th>
                            </tr>
                        </thead>
                        <tbody id="household-members-tbody">
                            @foreach($clients as $index => $member)
                                @include('components.household.member-row', ['index' => $index, 'member' => $member, 'occupations' => $occupations])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($errors->any())
                    <div class="alert alert-warning mt-3" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </form>

        <div class="modal fade" id="selectClientModal" tabindex="-1" aria-labelledby="selectClientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectClientModalLabel">Select an existing client.</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="input-group mb-4">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control fw-semibold" id="clientSearchInput" placeholder="Search by name...">
                        </div>

                        <ul class="list-group" id="clientSearchResults">
                            <li class="list-group-item text-center muted-text fw-semibold py-3">Start typing to see results.<br>(Minimum: 2 characters)</li>
                        </ul>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="commonBtn" data-bs-dismiss="modal">CANCEL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.edit-household')
@endsection
