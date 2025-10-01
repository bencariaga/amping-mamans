@extends('layouts.personal-pages')

@section('title', 'List of Message Template')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('message-templates.index') }}" class="text-decoration-none text-reset">SMS Template</a>
@endsection

@section('content')
<div class="container">
    <h2>Message Templates</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('message-templates.create') }}" class="btn btn-primary mb-3">Create New Template</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Text</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr>
                    <td>{{ $template->msg_tmp_id }}</td>
                    <td>{{ $template->msg_tmp_title }}</td>
                    <td>{{ $template->msg_tmp_text }}</td>
                    <td>
                        <a href="{{ route('message-templates.edit', $template->msg_tmp_id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <!-- <form action="{{ route('message-templates.destroy', $template->msg_tmp_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this template?')">Delete</button>
                        </form> -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection