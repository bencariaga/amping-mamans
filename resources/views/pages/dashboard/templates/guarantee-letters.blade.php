@extends('layouts.personal-pages')

@section('title', 'Guarantee Letter')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letters.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('guarantee-letter') }}" class="text-decoration-none text-reset">Guarantee Letter</a>
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        <div id="guarantee-letter-container">
            <div class="guarantee-letter-container">
                <div class="header">
                    <div class="left-section">
                        <img src="{{ asset('images/main/general-santos-seal.png') }}" alt="GenSan Seal" class="logo" id="gensan-seal">
                        <img src="{{ asset('images/main/amping-logo.png') }}" alt="AMPING Logo" class="logo" id="amping-logo">
                    </div>

                    <div class="center-section">
                        <div class="republic">Republic of the Philippines</div>
                        <div class="office-title">OFFICE OF THE CITY MAYOR</div>
                        <div class="amping-title">A . M . P . I . N . G</div>
                        <div class="program-desc">Auxiliaries and Medical Program<br>for Individuals and Needy Generals</div>
                        <div class="city">General Santos City</div>
                        <div class="email">E-mail Address: <span class="email-link">gensanamping@gmail.com</span></div>
                    </div>

                    <div class="right-section">
                        <img src="{{ asset('images/main/disiplina-muna.png') }}" alt="Disiplina Muna" class="disiplina-muna">
                        <div class="slogan">"Gobyernong Malinis,<br>Pag-unlad ay Mabilis."</div>
                    </div>
                </div>

                <div class="document-title">GUARANTEE LETTER</div>

                <div class="reference-box">
                    <span>Application No. <b>YEAR-00000</b></span>
                    <span>Guarantee Letter No. <b>YEAR-00000</b></span>
                </div>

                <div class="date-section">
                    <div class="date-label"><u>[date today]</u></div>
                    <div>Date</div>
                </div>

                <div class="addressee">
                    Dear <b><u>[affiliate partner]</u></b>,
                </div>

                <div class="body-text">
                    <p>
                        The Local Government Unit (LGU) of General Santos City under the <b>AMPING</b> guarantees to pay the amount not to exceed
                        <b><u>[AMOUNT SPELLED OUT IN PHILIPPINE PESOS] ONLY (PHP [assistance amount])</u></b>
                        per billed amount of PHP [billed amount] requested by applicant <b><u>[SURNAME], [FIRST NAME] [M. I.]</u></b>
                        intended for the <b><u>[SERVICE TYPE]</u></b>
                        to be provided to the beneficiary / patient <b><u>[SURNAME], [FIRST NAME] [M. I.]</u></b>
                        of <b><u>[BARANGAY], GENERAL SANTOS CITY.</u></b>
                        See the requirements you have submitted for your ready references.
                    </p>
                    <p class="mt-3">
                        Please be informed that the LGU is payable to your company / institution after submitting the Summary of Accounts (SOA) and supporting documents. Thank you for your consideration.
                    </p>
                    <p class="validity mt-3">
                        Valid within 3 days upon issuance and is not convertible to cash.
                    </p>
                </div>

                <div class="signature-section">
                    <div class="mayor-signature">
                        <div class="mayor-name">LORELIE GERONIMO-PACQUIAO</div>
                        <div class="mayor-title">City Mayor</div>
                    </div>

                    <div class="authority">By the Authority of the City Mayor</div>

                    <div class="assistant-signature">
                        <div class="assistant-name">MARITESS D. AMBUANG, MMPA</div>
                        <div class="assistant-title">Executive Assistant III</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
