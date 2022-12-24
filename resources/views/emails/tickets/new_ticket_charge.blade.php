@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note your shipment has been processed for customs and payment has been processed. You will received your receipt
    shortly via email.
    To view your invoice + 7501, please log into your account as it has been posted to your ticket.
    {{ $specialNotes }}
@endsection
