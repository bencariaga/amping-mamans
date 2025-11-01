@extends('errors.errors')

@section('code', '431')
@section('title', 'Request Header Fields Too Large')
@section('message', 'Your request contains too much header information. This technical error happens when the additional information sent with your request exceeds our system limits. This is usually caused by too many cookies or other data stored in your browser. Try clearing your browser cookies and cache, then try again. Contact support if the problem continues.')
