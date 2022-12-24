@extends('emails.layout')

@section('content')
    Dear {{ $firstname }},

    A new {{ $event }} document has been uploaded to ticket #{{ $ticketId }}.

    Link to ticket:
    <a href="{{ $url }}">{{ $url }}</a>

    This is an automated system message, please do not reply to it.
@endsection
