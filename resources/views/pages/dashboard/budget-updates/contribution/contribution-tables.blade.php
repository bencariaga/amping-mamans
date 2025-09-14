@extends('layouts.personal-pages')

@section('title', 'Sponsor Contributions')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/budget-updates/contribution/contribution-tables.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        window.contributionConfig = {
            sponsorId: "{{ $id }}"
        };
    </script>
    <script src="{{ asset('js/pages/dashboard/budget-updates/contribution/contribution-tables.js') }}"></script>
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a> &gt;
    <a href="{{ route('sponsors.list') }}" class="text-decoration-none text-reset">Sponsors</a> &gt;
    <a href="{{ route('sponsors.tables.show', ['id' => $id]) }}" class="text-decoration-none text-white">Contributions</a>
@endsection

@section('content')
    <div class="container pt-3 pb-4">
        <form action="{{ route('sponsors.contributions.update') }}" method="POST" id="contributionCreateForm">
            @csrf

            <div class="card mb-4 mt-4">
                @foreach($sponsors as $sponsor)
                    @php
                        $sponsor_id = $sponsor->sponsor_id;
                        $last = optional($sponsor->member)->last_name ?: '';
                        $first = optional($sponsor->member)->first_name ?: '';
                        $middle = optional($sponsor->member)->middle_name ?: '';
                        $suffix = optional($sponsor->member)->suffix ?: '';
                        $middleInitial = $middle !== '' ? strtoupper(substr(trim($middle), 0, 1)) . '.' : '';

                        $parts = [];

                        if ($last !== '') $parts[] = $last . ',';
                        if ($first !== '') $parts[] = $first;
                        if ($middleInitial !== '') $parts[] = $middleInitial;
                        if ($suffix !== '') $parts[] = $suffix;

                        $name = trim(implode(' ', $parts));
                    @endphp

                    <div class="table-header d-flex justify-content-between card-header bg-dark text-white fw-bold">
                        <div class="flex-grow-1">Sponsor Name: <span class="fw-normal">{{ $name }}</span></div>
                        <div class="flex-grow-1">Sponsor ID: <span class="fw-normal">{{ $sponsor_id }}</span></div>

                        <div class="d-flex justify-content-between button-group gap-3">
                            <a class="btn-group table-btn-group text-decoration-none" href="{{ route('sponsors.list') }}"><button type="button" class="btn btn-secondary">BACK TO LIST</button></a>
                            <div class="btn-group table-btn-group"><button type="button" class="btn btn-success" id="updateTableBtn">SAVE CHANGES</button></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="contributionListContainer" class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="ordinal-number-header-cell">Ordinal Number</th>
                                        <th class="contribution-amount-header-cell">Contribution Amount</th>
                                        <th class="total-amount-header-cell">Total Amount Contributed</th>
                                        <th class="time-contributed-cell">Time Contributed</th>
                                    </tr>
                                </thead>

                                <tbody class="contribution-rows">
                                    @forelse ($contributions as $contribution)
                                        <tr class="money-amount-row" data-id="{{ $contribution->budget_update_id }}">
                                            <td class="money-amount-cell ordinal-number-cell">
                                                <div class="money-amount-container">
                                                    <span class="ordinal-text"></span>
                                                </div>
                                            </td>

                                            <td class="money-amount-cell">
                                                <div class="money-amount-container">
                                                    <span class="money-currency fw-bold">₱</span>
                                                    <input type="number" step="0.01" name="amount_change[{{ $contribution->budget_update_id }}]" class="form-control form-control-sm contribution-input text-end money-value" value="{{ number_format($contribution->amount_change, 2, '.', '') }}">
                                                    <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                                                </div>
                                            </td>

                                            <td class="money-amount-cell">
                                                <div class="money-amount-container">
                                                    <span class="money-currency fw-bold">₱</span>
                                                    <input type="text" class="form-control form-control-sm total-amount-input text-end money-value" readonly>
                                                    <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                                                </div>
                                            </td>

                                            <td class="text-center time-contributed-cell" data-created="{{ optional($contribution->data)->created_at ? optional($contribution->data)->created_at->format('Y-m-d H:i:s') : '' }}">
                                                <div class="time-contributed-text">
                                                    <span class="time-text">{{ optional($contribution->data)->created_at ? optional($contribution->data)->created_at->format('Y-m-d H:i:s') : '' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="money-amount-row">
                                            <td class="money-amount-cell ordinal-number-cell">
                                                <div class="money-amount-container">
                                                    <span class="ordinal-text"></span>
                                                </div>
                                            </td>

                                            <td class="money-amount-cell">
                                                <div class="money-amount-container">
                                                    <span class="money-currency fw-bold">₱</span>
                                                    <input type="number" step="0.01" name="amount_change_new[]" class="form-control form-control-sm contribution-input text-end money-value" value="0.00">
                                                    <button type="button" class="row-remove-btn" aria-label="remove-row">✘</button>
                                                </div>
                                            </td>

                                            <td class="money-amount-cell">
                                                <div class="money-amount-container">
                                                    <span class="money-currency fw-bold">₱</span>
                                                    <input type="text" class="form-control form-control-sm total-amount-input text-end money-value" readonly>
                                                    <button type="button" class="row-add-btn" aria-label="add-row">✚</button>
                                                </div>
                                            </td>

                                            <td class="text-center time-contributed-cell" data-created="">
                                                <div class="time-contributed-text">
                                                    <span class="time-text"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </form>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
