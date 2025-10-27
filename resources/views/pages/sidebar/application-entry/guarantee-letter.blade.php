@php
    $pdfMode = (isset($pdf) && $pdf === true) || request()->has('pdf');
    $imgSeal = $pdfMode ? public_path('images/main/general-santos-seal.png') : asset('images/main/general-santos-seal.png');
    $imgAmping = $pdfMode ? public_path('images/main/amping-logo.png') : asset('images/main/amping-logo.png');
    $imgDisiplina = $pdfMode ? public_path('images/main/disiplina-muna.png') : asset('images/main/disiplina-muna.png');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AMPING-MAMANS | Guarantee Letter</title>
        <style>
            .guarantee-letter-container {
                font-family: Arial, Helvetica, sans-serif;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                background-color: #fff;
                color: #000;
                width: 8.5in;
                height: 11in;
                padding: 0.2in 0.5in 0.5in 0.5in;
                box-sizing: border-box;
            }

            .header {
                display: -webkit-box;
                -webkit-box-pack: justify;
                -webkit-box-align: start;
                padding-bottom: 10px;
                border-bottom: 2px solid #000;
            }

            .left-section,
            .center-section,
            .right-section {
                height: 175px;
            }

            .left-section,
            .right-section {
                width: calc(100% / 3);
                display: -webkit-box;
                align-items: center;
                text-align: center;
            }

            .left-section {
                -webkit-box-orient: horizontal;
                margin-top: 18px;
                gap: 1rem;
            }

            .right-section {
                -webkit-box-orient: vertical;
            }

            .center-section {
                width: calc(200% / 3);
                text-align: center;
                line-height: 1.8;
            }

            .logo {
                width: 80px;
                height: 80px;
                object-fit: contain;
            }

            .republic {
                font-size: 16px;
            }

            .office-title {
                font-size: 16px;
                font-weight: bold;
            }

            .amping-title {
                font-size: 18px;
                font-weight: bold;
            }

            .program-desc {
                font-size: 12px;
            }

            .city {
                font-size: 16px;
                font-weight: bold;
            }

            .email {
                font-size: 12px;
            }

            .email-link {
                color: blue;
            }

            .disiplina-muna {
                width: 160px;
                height: auto;
                object-fit: contain;
            }

            .slogan {
                font-size: 14px;
                font-style: italic;
                font-weight: bold;
            }

            .document-title {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                margin: 20px 0;
                letter-spacing: 1px;
            }

            .reference-box {
                display: -webkit-box;
                -webkit-box-pack: justify;
                border: 2px solid black;
                padding: 5px 10px;
                margin-bottom: 25px;
                font-size: 16px;
            }

            .app-number,
            .gl-number {
                display: -webkit-box;
            }

            .app-number {
                -webkit-box-pack: start;
            }

            .gl-number {
                -webkit-box-pack: end;
            }

            .date-section {
                margin-bottom: 2rem;
                font-size: 16px;
            }

            .addressee {
                margin-bottom: 20px;
                font-size: 16px;
            }

            .body-text {
                text-align: justify;
                font-size: 16px;
                line-height: 2;
            }

            .body-text p {
                text-indent: 3rem;
            }

            .body-text .validity {
                font-style: italic;
                font-weight: bold;
            }

            .signature-section {
                margin-top: 5rem;
                font-size: 16px;
            }

            .mayor-signature {
                text-align: center;
            }

            .mayor-name {
                font-weight: bold;
                margin-bottom: 8px;
            }

            .authority {
                margin-top: 2rem;
                font-size: 16px;
            }

            .assistant-signature {
                margin-top: 4.5rem;
            }

            .assistant-name {
                font-weight: bold;
                margin-bottom: 8px;
            }
        </style>
    </head>
    <body>
        <div class="guarantee-letter-container">
            <div class="header">
                <div class="left-section">
                    <img src="{{ $imgSeal }}" alt="GenSan Seal" class="logo" id="gensan-seal">
                    <img src="{{ $imgAmping }}" alt="AMPING Logo" class="logo" id="amping-logo">
                </div>

                <div class="center-section">
                    <div class="republic">Republic of the Philippines</div>
                    <div class="office-title">OFFICE OF THE CITY MAYOR</div>
                    <div class="amping-title">A . M . P . I . N . G</div>
                    <div class="program-desc">Auxiliaries and Medical Program<br>for Individuals and Needy Generals</div>
                    <div class="city">General Santos City</div>
                    <div class="email">Email Address: <span class="email-link">gensanamping@gmail.com</span></div>
                </div>

                <div class="right-section">
                    <img src="{{ $imgDisiplina }}" alt="Disiplina Muna" class="disiplina-muna">
                    <div class="slogan">"Gobyernong Malinis,<br>Pag-unlad ay Mabilis."</div>
                </div>
            </div>

            <div class="document-title">GUARANTEE LETTER</div>

            <div class="reference-box">
                <span class="app-number">Application No. <b>{{ $application_alias ?? ($application->application_id ?? 'N/A') }}</b></span>
                <span class="gl-number">Guarantee Letter No. <b>{{ $gl_alias ?? ($gl_id ?? 'N/A') }}</b></span>
            </div>

            <div class="date-section">
                <div class="date-label"><u>{{ $applied_at_formatted ?? \Carbon\Carbon::now()->format('F j, Y') }}</u></div>
                <div style="margin-top: 8px;"><b>Date</b></div>
            </div>

            <div class="addressee">
                Dear <b><u>{{ $affiliate_partner_name ?? '[affiliate partner]' }}</u></b>,
            </div>

            <div class="body-text">
                <p>
                    The Local Government Unit (LGU) of General Santos City under the <span style="letter-spacing: 1px; margin-left: 9px; margin-right: 9px;"><b>AMPING</b></span> guarantees to pay the amount not to exceed
                    <b><u><span style="margin-left: 2px; margin-right: 8px;">{{ $amount_in_words ?? '' }}</span> <span style="margin-left: 8px; margin-right: 2px;">(PHP {{ $assistance_amount_formatted ?? number_format($assistance_amount ?? 0) }})</span></u></b>
                    per billed amount of PHP {{ $billed_amount_formatted ?? number_format($application->billed_amount ?? 0) }} requested by applicant <b><u><span style="margin-left: 2px; margin-right: 1px;">{{ $applicant_full_name ?? '' }}</span></u></b>
                    intended for the <b><u>{{ $service_type ?? '[SERVICE TYPE]' }}</u></b>
                    to be provided to the beneficiary / patient <b><u><span style="margin-left: 2px; margin-right: 1px;">{{ $patient_full_name ?? '' }}</span></u></b>
                    of <b><u>{{ strtoupper($barangay ?? '') }}, GENERAL SANTOS CITY.</u></b>
                    See the requirements you have submitted for your ready references.
                </p>
                <p style="margin-top: 1rem;">
                    Please be informed that the LGU is payable to your company / institution after submitting the Summary of Accounts (SOA) and supporting documents. Thank you for your consideration.
                </p>
                <p class="validity" style="margin-top: 1rem;">
                    Valid within 3 days upon issuance and is not convertible to cash.
                </p>
            </div>

            <div class="signature-section">
                <div class="mayor-signature">
                    <div class="mayor-name">
                        @if(optional($mayor)->first_name)
                            {{ optional($mayor)->first_name }} {{ optional($mayor)->middle_name ? optional($mayor)->middle_name . ' ' : '' }}{{ optional($mayor)->last_name }} {{ (optional($mayor)->suffix && optional($mayor)->suffix !== 'N / A') ? optional($mayor)->suffix : '' }}
                        @else
                            LORELIE GERONIMO-PACQUIAO
                        @endif
                    </div>
                    <div class="mayor-title">City Mayor</div>
                </div>

                <div class="authority">By the Authority of the City Mayor</div>

                <div class="assistant-signature">
                    <div class="assistant-name">
                        <div class="assistant-name">
                            @if(optional($assistant)->first_name)
                                {{ optional($assistant)->first_name }}
                                {{ optional($assistant)->middle_name ? optional($assistant)->middle_name . ' ' : '' }}
                                {{ optional($assistant)->last_name }}
                                {{ optional($assistant)->suffix ? ' ' . optional($assistant)->suffix : '' }}
                                @if(optional($assistant)->post_nominal_letters)<span style="margin-left: -4px;">,</span> {{ optional($assistant)->post_nominal_letters }}@endif
                            @else
                                MARITESS D. AMBUANG, MMPA
                            @endif
                        </div>
                    </div>

                    <div class="assistant-title">Executive Assistant III</div>
                </div>
            </div>
        </div>
    </body>
</html>
