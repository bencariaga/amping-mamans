@php
    use Illuminate\Support\Carbon;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>{{ ucfirst($type) }} Report</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 24px;
                color: #1f2937;
                line-height: 1.5;
                padding: 40px;
                background: #ffffff;
            }

            .header {
                border-bottom: 6px solid #2563eb;
                padding-bottom: 25px;
                margin-bottom: 40px;
            }

            h1 {
                font-size: 72px;
                color: #1e40af;
                margin-bottom: 16px;
                font-weight: 700;
            }

            .meta {
                font-size: 30px;
                color: #4b5563;
            }

            .meta strong {
                color: #1f2937;
                font-weight: 600;
                font-size: 30px;
            }

            .summary-box {
                background: #f3f4f6;
                border-left: 8px solid #2563eb;
                border-radius: 6px;
                margin-bottom: 0;
                font-size: 24px;
                padding: 20px;
            }

            .summary-box strong {
                color: #1e40af;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-top: 0;
            }

            thead {
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            }

            th {
                color: #ffffff;
                text-align: left;
                padding: 15px 20px;
                font-weight: 600;
                font-size: 28px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            td {
                border: 1px solid #e5e7eb;
                padding: 15px 20px;
                background: #ffffff;
                font-size: 24px;
            }

            tbody tr:nth-child(even) td {
                background: #f9fafb;
            }

            tbody tr:hover td {
                background: #eff6ff;
            }

            .right {
                text-align: right;
            }

            .muted {
                color: #6b7280;
                margin-left: 15px;
                font-style: italic;
                font-size: 24px;
            }

            .footer {
                margin-top: 50px;
                padding-top: 25px;
                border-top: 3px solid #e5e7eb;
                text-align: center;
                color: #6b7280;
                font-size: 24px;
            }

            @media print {
                body {
                    padding: 25px;
                }
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>{{ ucfirst($type) }} Report</h1>
            <div class="meta">Generated on: <strong>{{ Carbon::now()->format('F d, Y \a\t h:i A') }}</strong></div>
            <div class="meta">Date Range: <strong>{{ $rangeLabel }}</strong></div>
        </div>

        @if($type === 'applications')
            <div class="summary-box">
                <strong>Total Records:</strong> {{ number_format($summary['total'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Total Billed:</strong> ₱ {{ number_format($summary['billed'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Total Assisted:</strong> ₱ {{ number_format($summary['assisted'] ?? 0) }}
            </div>
        @elseif($type === 'tariffs')
            <div class="summary-box">
                <strong>Total:</strong> {{ number_format($summary['total'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Active:</strong> {{ number_format($summary['active'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Inactive:</strong> {{ number_format($summary['inactive'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Draft:</strong> {{ number_format($summary['draft'] ?? 0) }}
                &nbsp; | &nbsp;
                <strong>Scheduled:</strong> {{ number_format($summary['scheduled'] ?? 0) }}
            </div>
        @else
            <div class="summary-box">
                <strong>Total Records:</strong> {{ number_format($summary['total'] ?? 0) }}
            </div>
        @endif

        <table>
            <thead>
                @if($type === 'applicants')
                    <tr>
                        <th>Applicant ID</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th class="right">Monthly Income</th>
                        <th>Created</th>
                    </tr>
                @elseif($type === 'patients')
                    <tr>
                        <th>Patient ID</th>
                        <th>Full Name</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Category</th>
                        <th>Created</th>
                    </tr>
                @elseif($type === 'applications')
                    <tr>
                        <th>Application ID</th>
                        <th>Applicant</th>
                        <th>Service ID</th>
                        <th class="right">Billed</th>
                        <th class="right">Assisted</th>
                        <th>Applied At</th>
                    </tr>
                @elseif($type === 'tariffs')
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
                    @if($type === 'applicants')
                        <tr>
                            <td>{{ $row->applicant_id }}</td>
                            <td>{{ $row->full_name }}</td>
                            <td>{{ $row->phone_number ?? '—' }}</td>
                            <td class="right">₱ {{ number_format((int) $row->monthly_income, 0) }}</td>
                            <td>{{ Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @elseif($type === 'patients')
                        <tr>
                            <td>{{ $row->patient_id }}</td>
                            <td>{{ $row->full_name }}</td>
                            <td>{{ $row->sex ?: '—' }}</td>
                            <td>{{ $row->age ?: '—' }}</td>
                            <td>{{ $row->patient_category ?: '—' }}</td>
                            <td>
                                @if(!empty($row->created_at))
                                    {{ Carbon::parse($row->created_at)->format('Y-m-d') }}
                                @elseif(!empty($row->created_year))
                                    {{ $row->created_year }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @elseif($type === 'applications')
                        <tr>
                            <td>{{ $row->application_id }}</td>
                            <td>{{ $row->full_name }}</td>
                            <td>{{ $row->service_id }}</td>
                            <td class="right">₱ {{ number_format((int) $row->billed_amount, 0) }}</td>
                            <td class="right">₱ {{ number_format((int) $row->assistance_amount, 0) }}</td>
                            <td>{{ Carbon::parse($row->applied_at)->format('Y-m-d') }}</td>
                        </tr>
                    @elseif($type === 'tariffs')
                        <tr>
                            <td>{{ $row->tariff_list_id }}</td>
                            <td>{{ Carbon::parse($row->effectivity_date)->format('Y-m-d') }}</td>
                            <td>{{ $row->tl_status }}</td>
                            <td>
                                @if(!empty($row->created_at))
                                    {{ Carbon::parse($row->created_at)->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="muted">
                            No data is found for the selected range.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>This report was automatically generated by the AMPING system.</p>
            <p>&copy; {{ Carbon::now()->year }} Auxiliaries and Medical Program for Individuals and Needy Generals. All rights reserved.</p>
        </div>
    </body>
</html>
