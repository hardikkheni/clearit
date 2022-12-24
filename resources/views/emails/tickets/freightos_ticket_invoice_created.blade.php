@extends('emails.layout')

@section('content')
    A new invoice in the amount of ${{ number_format($amount, 2) }} has been generated for {{ $customerName }}, FreightOS reference #{{ $affiliateReferenceNumber }}. {{ config('constants.config.SERVICE_NAME') }} ticket #{{ $ticketNumber }}.

    You can view the details of this invoice by accessing ticket #{{ $ticketNumber }} from your affiliate dashboard on {{ config('constants.config.SERVICE_NAME') }}.
@endsection
