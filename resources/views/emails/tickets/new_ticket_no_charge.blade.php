@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note your customs entry has been successfully processed for Ticket:<br/>
    ID: {{ $ticketNumber }} <br />
    Transaction: #{{ $transactionNumber }}<br />
    Amount: {{ ($amount < 0 ? "- $" : "$") . number_format(abs($amount), 2) }}<br/>
    Please find the associated invoice and B3 for this transaction attached.
    If you would like to make a one-time payment for this transaction please follow the link below and use the transaction
    number posted above.
    <a href="#" style="color:#555555">Payment link</a>
@endsection
