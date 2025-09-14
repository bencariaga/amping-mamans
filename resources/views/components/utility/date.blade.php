@push('styles')
    <link href="{{ asset('css/components/utility/date.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/components/utility/date.js') }}"></script>
@endpush

<div class="d-flex gap-2">
    <div class="dropdown date-day-dropdown">
        <button id="dateDayDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
        <ul class="dropdown-menu" aria-labelledby="dateDayDropdownBtn"></ul>
        <input type="hidden" id="dateDaySelect" name="day">
    </div>

    <div class="dropdown date-month-dropdown">
        <button id="dateMonthDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
        <ul class="dropdown-menu" aria-labelledby="dateMonthDropdownBtn"></ul>
        <input type="hidden" id="dateMonthSelect" name="month">
    </div>

    <div class="dropdown date-year-dropdown">
        <button id="dateYearDropdownBtn" class="btn dropdown-toggle custom-dropdown-btn fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
        <ul class="dropdown-menu" aria-labelledby="dateYearDropdownBtn"></ul>
        <input type="hidden" id="dateYearSelect" name="year">
    </div>
</div>
