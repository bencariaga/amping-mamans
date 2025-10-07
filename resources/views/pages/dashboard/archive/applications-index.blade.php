@extends('layouts.personal-pages')

@section('content')
<div class="container">
    <h2>Archived Applications</h2>
    <form method="POST" action="{{ route('archive.bulk-unarchive', ['model' => 'applications']) }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Application ID</th>
                    <th>Applicant</th>
                    <th>Date Applied</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $app->application_id }}"></td>
                        <td>{{ $app->application_id }}</td>
                        <td>{{ $app->applicant->client->member->full_name ?? 'N/A' }}</td>
                        <td>{{ $app->applied_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Bulk Unarchive</button>
    </form>
</div>
<script>
document.getElementById('checkAll').onclick = function() {
    var checkboxes = document.querySelectorAll('input[name="ids[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>
@endsection