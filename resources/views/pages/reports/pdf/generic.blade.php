<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 10px 15px; }
        .pdf-header { width: 100%; border-collapse: collapse; margin-bottom: 8px; border-bottom: 2px solid #000; }
        .pdf-header td { vertical-align: middle; padding-bottom: 4px; }
        .header-left { width: 18%; text-align: center; padding-top: 4px; }
        .header-left img { width: 45px; height: 45px; object-fit: contain; margin: 0 2px; }
        .header-center { width: 64%; text-align: center; line-height: 1.2; }
        .republic { font-size: 12px; margin: 1px 0; }
        .office-title { font-size: 12px; font-weight: bold; margin: 1px 0; }
        .amping-title { font-size: 13px; font-weight: bold; margin: 1px 0; letter-spacing: 1px; }
        .program-desc { font-size: 9px; margin: 1px 0; line-height: 1.1; }
        .city { font-size: 12px; font-weight: bold; margin: 1px 0; }
        .email { font-size: 9px; margin: 1px 0; }
        .email-link { color: blue; }
        .header-right { width: 18%; text-align: center; }
        .disiplina-muna { width: 70px; height: auto; object-fit: contain; }
        .slogan { font-size: 9px; font-style: italic; font-weight: bold; margin-top: 2px; line-height: 1.1; }
        h2 { font-size: 15px; margin: 8px 0 6px; font-weight: bold; text-align: center; }
        .meta { font-size: 10px; margin-bottom: 6px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; table-layout: auto; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 3px 3px; font-size: 10px; white-space: nowrap; }
        .data-table th { background: #f4f6f8; text-align: left; font-weight: bold; }
        .right { text-align: right; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <table class="pdf-header">
        <tr>
            <td class="header-left">
                <img src="{{ public_path('images/main/general-santos-seal.png') }}" alt="GenSan Seal">
                <img src="{{ public_path('images/main/amping-logo.png') }}" alt="AMPING Logo">
            </td>
            <td class="header-center">
                <div class="republic">Republic of the Philippines</div>
                <div class="office-title">OFFICE OF THE CITY MAYOR</div>
                <div class="amping-title">A . M . P . I . N . G</div>
                <div class="program-desc">Auxiliaries and Medical Program<br>for Individuals and Needy Generals</div>
                <div class="city">General Santos City</div>
                <div class="email">Email Address: <span class="email-link">gensanamping@gmail.com</span></div>
            </td>
            <td class="header-right">
                <img src="{{ public_path('images/main/disiplina-muna.png') }}" alt="Disiplina Muna" class="disiplina-muna">
                <div class="slogan">"Gobyernong Malinis,<br>Pag-unlad ay Mabilis."</div>
            </td>
        </tr>
    </table>
    <h2>{{ ucfirst($type) }} Report</h2>
    <div class="meta">
        Range: <strong>{{ $rangeLabel }}</strong>
    </div>

    @if($type==='applications')
        <p class="meta">
            <strong>Total:</strong> {{ number_format($summary['total'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Billed:</strong> ₱ {{ number_format($summary['billed'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Assisted:</strong> ₱ {{ number_format($summary['assisted'] ?? 0) }}
        </p>
    @elseif($type==='tariffs')
        <p class="meta">
            <strong>Total:</strong> {{ number_format($summary['total'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Active:</strong> {{ number_format($summary['active'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Inactive:</strong> {{ number_format($summary['inactive'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Draft:</strong> {{ number_format($summary['draft'] ?? 0) }}
            &nbsp; | &nbsp; <strong>Scheduled:</strong> {{ number_format($summary['scheduled'] ?? 0) }}
        </p>
    @else
        <p class="meta"><strong>Total:</strong> {{ number_format($summary['total'] ?? 0) }}</p>
    @endif

    <table class="data-table">
        <thead>
            @if($type==='applicants')
                <tr>
                    <th>Applicant ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th class="right">Monthly Income</th>
                    <th>Created</th>
                </tr>
            @elseif($type==='patients')
                <tr>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Category</th>
                    <th>Created</th>
                </tr>
            @elseif($type==='applications')
                <tr>
                    <th>Applicant</th>
                    <th>Patient</th>
                    <th>Affiliate Partner</th>
                    <th>Service</th>
                    <th class="right">Billed</th>
                    <th class="right">Assisted</th>
                    <th>Applied At</th>
                </tr>
            @elseif($type==='tariffs')
                <tr>
                    <th>Tariff List ID</th>
                    <th>Effectivity Date</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            @endif
        </thead>
        <tbody>
        @forelse($items as $row)
            @if($type==='applicants')
                <tr>
                    <td>{{ $row->applicant_id }}</td>
                    <td>{{ $row->full_name }}</td>
                    <td>{{ $row->phone_number ?? '—' }}</td>
                    <td class="right">₱ {{ number_format((int)$row->monthly_income, 0) }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                </tr>
            @elseif($type==='patients')
                <tr>
                    <td>{{ $row->patient_id }}</td>
                    <td>{{ $row->full_name }}</td>
                    <td>{{ $row->sex ?: '—' }}</td>
                    <td>{{ $row->age ?: '—' }}</td>
                    <td>{{ $row->patient_category ?: '—' }}</td>
                    <td>
                        @if(!empty($row->created_at))
                            {{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}
                        @elseif(!empty($row->created_year))
                            {{ $row->created_year }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @elseif($type==='applications')
                <tr>
                    <td>{{ $row->applicant_name ?? '—' }}</td>
                    <td>{{ $row->patient_name ?? '—' }}</td>
                    <td>{{ $row->affiliate_partner_name ?? '—' }}</td>
                    <td>{{ $row->service_name ?? '—' }}</td>
                    <td class="right">₱ {{ number_format((int)$row->billed_amount, 0) }}</td>
                    <td class="right">₱ {{ number_format((int)$row->assistance_amount, 0) }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->applied_at)->format('Y-m-d') }}</td>
                </tr>
            @elseif($type==='tariffs')
                <tr>
                    <td>{{ $row->tariff_list_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->effectivity_date)->format('Y-m-d') }}</td>
                    <td>{{ $row->tl_status }}</td>
                    <td>
                        @if(!empty($row->created_at))
                            {{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="{{ $type==='applications' ? 7 : ($type==='patients' ? 6 : ($type==='applicants' ? 5 : 4)) }}" class="muted">No data found for the selected range.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
