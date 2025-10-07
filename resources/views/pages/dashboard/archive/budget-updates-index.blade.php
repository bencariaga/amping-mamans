@extends('layouts.personal-pages')

@section('content')
<div class="container">
    <h2>Archived Budget Updates</h2>
    <form method="POST" action="{{ route('archive.bulk-unarchive', ['model' => 'budget-updates']) }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAllBudget"></th>
                    <th>Budget Update ID</th>
                    <th>Reason</th>
                    <th>Amount Recent</th>
                    <th>Amount Spent</th>
                    <th>Date Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgetUpdates as $update)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $update->budget_update_id }}"></td>
                        <td>{{ $update->budget_update_id }}</td>
                        <td>{{ $update->reason }}</td>
                        <td>{{ number_format($update->amount_recent, 2) }}</td>
                        <td>{{ number_format($update->amount_spent, 2) }}</td>
                        <td>{{ $update->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Bulk Unarchive</button>
    </form>
</div>
<script>
document.getElementById('checkAllBudget').onclick = function() {
    var checkboxes = document.querySelectorAll('input[name="ids[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>
@endsection