<!DOCTYPE html>
<html>
<head>
    <title>Assistance Requests Report ({{ $year }})</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Assistance Requests Report ({{ $year }})</h2>
    <table>
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Applicant</th>
                <th>Service</th>
                <th>Billed Amount</th>
                <th>Assistance Amount</th>
                <th>Applied At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
                @php
                    $applied_at = \Carbon\Carbon::parse($app->applied_at);
                @endphp
                <tr>
                    <td>{{ $app->application_id }}</td>
                    <td>{{ $app->applicant->client->member->full_name ?? 'N/A' }}</td>
                    <td>{{ $app->expenseRange->service->service_type ?? 'N/A' }}</td>
                    <td>{{ number_format($app->billed_amount, 2) }}</td>
                    <td>{{ number_format($app->assistance_amount, 2) }}</td>
                    <td>{{ $applied_at->format('M. d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>