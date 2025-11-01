@extends('errors.errors')

@section('code', '428')
@section('title', 'Precondition Required')
@section('message', 'Your request needs additional conditions to be processed. This security feature prevents conflicts when multiple people try to update the same information at the same time. You need to include specific conditions in your request to show you have the latest version of the data. Please refresh the page and try again with the most current information.')
