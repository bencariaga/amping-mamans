@extends('errors.errors')

@section('code', '429')
@section('title', 'Too Many Requests')
@section('message', 'You have made too many requests in a short period of time. To protect our system and ensure fair access for everyone, we limit how many requests can be made within a certain timeframe. Please wait a few minutes before trying again. If you continue to see this error, you may need to wait longer or contact support.')
