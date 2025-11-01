@extends('errors.errors')

@section('code', '412')
@section('title', 'Precondition Failed')
@section('message', 'The conditions for your request were not met. This happens when you try to do something that depends on certain conditions being true, but those conditions are not currently met. For example, trying to update something that has already been changed. Please refresh the page and try your action again with the latest information.')
