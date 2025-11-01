@extends('errors.errors')

@section('code', '408')
@section('title', 'Request Timeout')
@section('message', 'Your request took too long to complete. This happens when your connection to our server was idle for too long or your internet connection was too slow. The server closed the connection to free up resources. Please try your request again. If you have a slow internet connection, this might happen more often.')
