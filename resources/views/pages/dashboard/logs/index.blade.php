@extends('layouts.personal-pages')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Log List</h1>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Staff ID</th>
                    <th>Log Type</th>
                    <th>Model</th>
                    <th>Payload</th>
                    <th>Happened At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    @php
                        $logInfo = json_decode($log->log_info);
                    @endphp
                    <tr>
                        <td>{{ $log->log_id }}</td>
                        <td>{{ $log->staff_id ?? 'N/A' }}</td>
                        <td><span class="badge bg-{{ 
                            str_contains($log->log_type, 'created') ? 'success' : 
                            (str_contains($log->log_type, 'updated') ? 'primary' : 
                            (str_contains($log->log_type, 'deleted') ? 'danger' : 'secondary')) 
                        }}">{{ $log->log_type }}</span></td>
                        <td>{{ $logInfo->model ?? 'N/A' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                data-bs-toggle="modal" data-bs-target="#logModal-{{ $log->log_id }}">
                                View Payload
                            </button>
                        </td>
                        <td>{{ $log->happened_at->format('Y-m-d H:i:s') }}</td>
                    </tr>

                    <!-- Modal for viewing JSON payload -->
                    <div class="modal fade" id="logModal-{{ $log->log_id }}" tabindex="-1" aria-labelledby="logModalLabel-{{ $log->log_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="logModalLabel-{{ $log->log_id }}">Log Payload: #{{ $log->log_id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="background-color: #f8f9faff;">
                                    <pre><code class="text-start">{{ json_encode($logInfo, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection