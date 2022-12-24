@extends('emails.layout')

@section('content')
    Hi there,

    Thank you for selecting {{ $serviceName }} as your Customs Broker on {{ $companyname }} for shipment #{{ $shipmentNumber }}.

    Simply click <a href="{{ $url }}">here</a> to complete the registration process and fill in your Power of Attorney (POA).

    Please do so as soon as possible to avoid any delays in your shipment.

    For any questions regarding customs, please reply to this email and we will be happy to assist you further.
@endsection



