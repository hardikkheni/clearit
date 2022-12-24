@extends('emails.layout')

@section('content')
    Dear Customer,
                            
    Please note an Additional charge has been processed on your account. Your detailed invoice is attached.
    <strong>Shipment ID:</strong> #{{ $transactionNumber }} <br />
    <strong>Description:</strong> {{ $specialNotes }} <br />
    <strong>Amount charged:</strong> {{ ($amount < 0 ? "- $" : "$") . number_format(abs($amount), 2) }}
@endsection



