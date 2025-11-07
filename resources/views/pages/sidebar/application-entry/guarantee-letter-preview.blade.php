@php
    // Define image paths for web display
    $imgSeal = asset('images/main/general-santos-seal.png');
    $imgAmping = asset('images/main/amping-logo.png');
    $imgDisiplina = asset('images/main/disiplina-muna.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guarantee Letter - {{ $gl_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #525659;
            padding: 0;
            margin: 0;
        }

        .preview-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            min-height: 100vh;
        }

        .toolbar {
            background: #323639;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .toolbar-left {
            font-size: 14px;
            font-weight: 500;
        }

        .toolbar-right {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .toolbar-btn {
            background: transparent;
            border: none;
            color: white;
            padding: 8px;
            cursor: pointer;
            border-radius: 2px;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            transition: background 0.2s;
        }

        .toolbar-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        .toolbar-btn svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .content-wrapper {
            padding: 40px 60px;
            background: white;
        }

        /* Guarantee Letter Styles */
        .guarantee-letter-container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.2in 0.5in 0.3in 0.5in;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            background: white;
            min-height: 11in;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #000;
        }

        .left-section {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .center-section {
            flex: 1;
            text-align: center;
            padding: 0 20px;
        }

        .republic {
            font-size: 16px;
            margin-bottom: 2px;
        }

        .office-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .amping-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0;
            margin-bottom: 3px;
        }

        .program-desc {
            font-size: 12px;
            font-style: normal;
            margin-bottom: 3px;
        }

        .city {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .email {
            font-size: 12px;
        }

        .email-link {
            color: #0066cc;
        }

        .right-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .disiplina-logo {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        .disiplina-text {
            font-size: 8px;
            text-align: center;
            margin-top: 3px;
            font-style: italic;
        }

        .title {
            text-align: center;
            font-size: 19px;
            font-weight: bold;
            margin: 18px 0 15px 0;
            letter-spacing: 1px;
        }

        .reference-box {
            border: 2px solid #000;
            padding: 5px 10px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            font-size: 15px;
        }

        .date-section {
            margin-bottom: 1.5rem;
            font-size: 15px;
        }

        .date-label {
            font-weight: bold;
        }

        .salutation {
            margin-bottom: 15px;
            font-size: 15px;
        }

        .body-text {
            text-align: justify;
            margin-bottom: 0;
            font-size: 15px;
            line-height: 1.7;
        }

        .body-text p {
            text-indent: 3rem;
            margin-bottom: 0;
        }

        .body-text.no-indent {
            text-indent: 0;
        }

        .amount {
            font-weight: bold;
        }

        .validity-notice {
            font-style: italic;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            font-size: 15px;
        }

        .signatures {
            margin-top: 2.5rem;
            font-size: 15px;
        }

        .mayor-signature {
            text-align: center;
            margin-bottom: 15px;
        }

        .mayor-name {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .mayor-title {
            font-size: 15px;
        }

        .authority {
            margin-top: 1rem;
            margin-bottom: 2rem;
            font-size: 15px;
        }

        .assistant-signature {
            margin-bottom: 15px;
        }

        .assistant-name {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .assistant-title {
            font-size: 15px;
        }

        @media print {
            body {
                background: white;
            }
            .toolbar {
                display: none;
            }
            .preview-container {
                box-shadow: none;
                max-width: 100%;
            }
            .content-wrapper {
                padding: 0;
            }
            .guarantee-letter-container {
                page-break-inside: avoid;
            }
            .signatures {
                page-break-inside: avoid;
            }
        }

        @page {
            size: letter;
            margin: 0.4in 0.5in;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="toolbar">
            <div class="toolbar-left">
                Guarantee Letter - {{ $gl_id }}
            </div>
            <div class="toolbar-right">
                <button class="toolbar-btn" onclick="window.print()" title="Download">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                </button>
                <button class="toolbar-btn" onclick="window.print()" title="Print">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                </button>
                <button class="toolbar-btn" title="More options">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="guarantee-letter-container">
                <div class="header">
                    <div class="left-section">
                        <img src="{{ $imgSeal }}" alt="GenSan Seal" class="logo">
                        <img src="{{ $imgAmping }}" alt="AMPING Logo" class="logo">
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
                        <img src="{{ $imgDisiplina }}" alt="Disiplina Muna" class="disiplina-logo">
                        <div class="disiplina-text">"Gobyernong Malinis,<br>Pag-unlad ay Mabilis."</div>
                    </div>
                </div>

                <div class="title">GUARANTEE LETTER</div>

                <div class="reference-box">
                    <div>Application No. <strong>{{ $application_alias }}</strong></div>
                    <div>Guarantee Letter No. <strong>{{ $gl_alias }}</strong></div>
                </div>

                <div class="date-section">
                    {{ $applied_at_formatted }}<br>
                    <span class="date-label">Date</span>
                </div>

                <div class="salutation">
                    Dear <u><strong>{{ $affiliate_partner_name }}</strong></u>,
                </div>

                <div class="body-text">
                    <p>The Local Government Unit (LGU) of General Santos City under the <strong>AMPING</strong> guarantees to pay the amount not to exceed <strong class="amount">{{ strtoupper($amount_in_words) }} (PHP {{ $assistance_amount_formatted }})</strong> per billed amount of <strong>PHP {{ $billed_amount_formatted }}</strong> requested by applicant <u><strong>{{ $applicant_full_name }}</strong></u> intended for the <u><strong>{{ $service_type }}</strong></u> to be provided to the beneficiary / patient <u><strong>{{ $patient_full_name }}</strong></u> of <u><strong>{{ $barangay }}, GENERAL SANTOS CITY</strong></u>. See the requirements you have submitted for your ready references.</p>

                    <p style="text-indent: 0;">Please be informed that the LGU is payable to your company / institution after submitting the Summary of Accounts (SOA) and supporting documents. Thank you for your consideration.</p>

                    <p class="validity" style="text-align: center; font-style: italic; font-weight: bold; text-indent: 0;">Valid within 3 days upon issuance and is not convertible to cash.</p>
                </div>

                <div class="signatures">
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

                    <div class="authority">
                        By the Authority of the City Mayor
                    </div>

                    <div class="assistant-signature">
                        <div class="assistant-name">
                            @if(optional($assistant)->first_name)
                                {{ optional($assistant)->first_name }} {{ optional($assistant)->middle_name ? optional($assistant)->middle_name . ' ' : '' }}{{ optional($assistant)->last_name }} {{ (optional($assistant)->suffix && optional($assistant)->suffix !== 'N / A') ? optional($assistant)->suffix . ', ' : '' }}{{ optional($assistant)->post_nominal_letters ?: '' }}
                            @else
                                MARITESS DAPIDRAN AMBUANG, MMPA
                            @endif
                        </div>
                        <div class="assistant-title">Executive Assistant III</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Optional: Auto-print on load (uncomment if desired)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
