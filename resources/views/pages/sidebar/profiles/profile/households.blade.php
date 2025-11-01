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
    <a href="{{ route('profiles.applicants.list') }}" class="text-decoration-none text-white">Applicants</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.households.list') }}" class="text-decoration-none text-white">Households</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('profiles.households.show', $household->household_id) }}" class="text-decoration-none text-white">{{ $household->household_name }} Family / Household Profile</a>
@endsection

@section('content')
    <div class="container-fluid py-4">
        @livewire('client.household-profile', ['household' => $household, 'clients' => $clients, 'occupations' => $occupations])

        <div class="modal fade" id="selectClientModal" tabindex="-1" aria-labelledby="selectClientModalLabel" aria-hidden="false" data-bs-focus="false">
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
    @include('components.layouts.footer.profile-buttons-4')
@endsection
