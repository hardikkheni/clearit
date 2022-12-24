@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note your customs entry has been successfully processed, details below:
    <strong>ID:</strong> #{{ $ticketNumber }}<br />
    <strong>Invoice Number:</strong> {{ $transactionNumber }}<br />
    <strong>Amount:</strong> {{ ($amount < 0 ? "- $" : "$") . number_format(abs($amount), 2) }} <br />

    Please find the associated invoice and 7501 for this transaction attached.

    If you would like to make a one-time payment for this transaction, please follow the link below and use the invoice
    number posted above.

    <a href="{{ $url }}" style="color:#555555">Payment link</a>
@endsection
