@extends('layouts.personal-pages')

@section('title', 'Guarantee Letter')

@push('styles')
    <link href="{{ asset('css/pages/dashboard/templates/guarantee-letter-template.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-white">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('gl-templates.list') }}" class="text-decoration-none text-white">GL Templates</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('guarantee-letter', $template) }}" class="text-decoration-none text-white">View Template</a>
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
                    <span>Application No. <b>APPLICATION-YEAR-MON-0000</b></span>
                    <span>Guarantee Letter No. <b>GL-YEAR-MON-0000</b></span>
                </div>

                <div class="date-section">
                    <div class="date-label"><u>{{ \Carbon\Carbon::now()->format('F j, Y') }}</u></div>
                    <div><b>Date</b></div>
                </div>

                <div class="body-text">
                    {!! $template->gl_content !!}
                </div>

                <div class="signature-section">
                    <div class="mayor-signature" id="mayor-signature">
                        @if($signature1)
                            <img src="{{ asset($signature1) }}" alt="Signature 1" style="height: 60px; margin-bottom: 5px;">
                        @endif
                        <div class="mayor-name" id="mayor-name">
                            {{ strtoupper($signersData['signer1']['first'] ?? '') }}
                            {{ strtoupper($signersData['signer1']['middle'] ?? '') }}
                            {{ strtoupper($signersData['signer1']['last'] ?? '') }}
                            {{ $signersData['signer1']['suffix'] ? ' ' . strtoupper($signersData['signer1']['suffix']) : '' }}
                        </div>
                        <div class="mayor-title" id="mayor-title">City Mayor</div>
                    </div>

                    <div class="authority">By the Authority of the City Mayor</div>

                    <div class="assistant-signature" id="assistant-signature">
                        @if($signature2)
                            <img src="{{ asset($signature2) }}" alt="Signature 2" style="height: 60px; margin-bottom: 5px;">
                        @endif
                        <div class="assistant-name" id="assistant-name">
                            {{ strtoupper($signersData['signer2']['first'] ?? '') }}
                            {{ strtoupper($signersData['signer2']['middle'] ?? '') }}
                            {{ strtoupper($signersData['signer2']['last'] ?? '') }}
                            {{ $signersData['signer2']['suffix'] ? ' ' . strtoupper($signersData['signer2']['suffix']) : '' }}
                            @if($signersData['signer2']['pnl'])<span style="margin-left: -4px;">,</span> {{ strtoupper($signersData['signer2']['pnl']) }}@endif
                        </div>
                        <div class="assistant-title" id="assistant-title">Executive Assistant III</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('components.layouts.footer.profile-buttons-1')
@endsection
