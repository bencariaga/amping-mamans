@extends('errors.errors')

@section('code', '425')
@section('title', 'Too Early')
@section('message', 'Your request was sent too early and cannot be processed yet. This security feature prevents requests from being processed before the system is ready to handle them safely. This is a rare error that usually resolves itself. Please wait a moment and try your request again. Contact support if the issue persists.')
