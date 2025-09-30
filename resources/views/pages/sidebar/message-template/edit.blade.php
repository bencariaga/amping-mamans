@extends('layouts.personal-pages')

@section('title', 'Edit Message Template')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-reset">Dashboard</a><span class="cursor-default unselectable">&nbsp;&nbsp;&nbsp;<span class="fw-normal text-info">&gt;</span>&nbsp;&nbsp;</span>
    <a href="{{ route('message-templates.index') }}" class="text-decoration-none text-reset">SMS Template</a>
@endsection

@section('content')
<div class="container">
    <h2>Edit Message Template</h2>
    <form method="POST" action="{{ route('message-templates.update', $template->msg_tmp_id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="msg_tmp_title" class="form-label">Title</label>
            <input type="text" name="msg_tmp_title" id="msg_tmp_title" class="form-control" value="{{ old('msg_tmp_title', $template->msg_tmp_title) }}">
            @error('msg_tmp_title')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="msg_tmp_text" class="form-label">Template Text</label>
            <textarea name="msg_tmp_text" id="msg_tmp_text" class="form-control" rows="5">{{ old('msg_tmp_text', $template->msg_tmp_text) }}</textarea>
            @error('msg_tmp_text')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('message-templates.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection