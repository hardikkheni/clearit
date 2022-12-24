@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note your Importer Security Filing (ISF) is now complete. Please be sure to upload (to your ticket) or forward (<a href="mailto:{{ $infoEmail }}">{{ $infoEmail }}</a>) your Arrival Notice once received from your freight forwarder. You can expect to receive the Arrival Notice several days prior to your ship arriving at port.

    **What is an Arrival Notice?**

    The Arrival Notice is a document issued by the Ocean Freight Carrier or Agent to the consignee (recipient) informing them of the arrival of their vessel. The Arrival Notice provides shipment details, charges, and documents required for the freight to be customs cleared and released for delivery.  The Arrival notice's details include but are not limited to: Ocean Freight Carrier's release charges, Cargo location, etc.

    Thank you for choosing {{ $serviceName }}!
@endsection

