@extends('layouts.personal-pages')

@section('content')
<div class="container">
    <h2>Archived Applicants</h2>
    <form method="POST" action="{{ route('archive.bulk-unarchive', ['model' => 'applicants']) }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAllApplicants"></th>
                    <th>Applicant ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applicants as $applicant)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $applicant->applicant_id }}"></td>
                        <td>{{ $applicant->applicant_id }}</td>
                        <td>{{ $applicant->client->member->full_name }}</td>
                        <td>{{ $applicant->client->contacts[0]->phone_number ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Bulk Unarchive</button>
    </form>
</div>
<script>
document.getElementById('checkAllApplicants').onclick = function() {
    var checkboxes = document.querySelectorAll('input[name="ids[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>
@endsection