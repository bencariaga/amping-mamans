@extends('errors.errors')

@section('code', '409')
@section('title', 'Conflict')
@section('message', 'There is a conflict with your request. This usually happens when you try to make a change that conflicts with something that already exists or is currently being used. For example, trying to create something that already exists or updating information that has been changed by someone else. Please refresh the page and try again.')
