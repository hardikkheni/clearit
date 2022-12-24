@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note you have an outstanding amount owing on your account. Your detailed invoice is attached.
    <strong>Shipment ID:</strong> #{{ $transactionNumber }}<br />
    <strong>Description:</strong> {{ $specialNotes }} <br />
    <strong>Amount charged:</strong> {{ ($amount < 0 ? "- $" : "$") . number_format(abs($amount), 2) }} <br />

    Please complete payment of this amount by following the link below and filling out your payment information:
    <a href="{{ $url }}" style="color:#555555">{{ $url }}</a>
@endsection



