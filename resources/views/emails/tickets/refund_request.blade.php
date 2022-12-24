@extends('emails.layout')

@section('content')
    Refund requested.

    <strong>Ticket:</strong> #{{ $ticketNumber }} <br />
    <strong>Amount:</strong> {{ ($amount < 0 ? "- $" : "$") . number_format(abs($amount), 2) }}<br/>
    <strong>Description:</strong> {{ $specialNotes }}
@endsection
