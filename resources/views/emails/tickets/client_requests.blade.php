@extends('emails.layout')

@section('content')
    @if (!$isCustomerConsumer) 
        Dear Customer,<br /><br />Additional information is needed regarding your ticket #<?php echo $ticketNumber; ?> at ClearitUSA.<br />Please provide the following at your earliest convenience:{{ config('constants.config.SERVICE_NAME') }}:<br />
    @else
        Dear {{ $firstname }} {{ $lastname }},<br /> <br />Additional information is needed regarding your account at ClearitUSA.<br />Please provide the following at your earliest convenience:<br /><br />
    @endif

    @foreach ($clientRequests as $clientRequest)
        @if ($clientRequest['document'] && $clientRequest['description'])
            <span><strong>Document : </strong>{{ $clientRequest['document'] }}</span><br /><span>{{ $clientRequest['description'] }}</span><br />
            @if ( $clientRequest['sampleDocumentURL'] )
                <span>You can find a blank copy of this document at the link below:</span><br />
                <a href="{{ $clientRequest['sampleDocumentURL'] }}"> 
                    {{ $clientRequest['sampleDocumentURL'] }}
                </a>
                <br />
            @endif
            <br />
        @elseif ( !$clientRequest['document'] && $clientRequest['description'] )
            <b>Note: </b> {{ $clientRequest['description'] }} <br/><br />
        @elseif ( $clientRequest['document'] && !$clientRequest['description'] )
            <span><strong>Document : </strong> {{ $clientRequest['document'] }} </span><br />
            @if ( $clientRequest['sampleDocumentURL'] )
                <span>You can find a blank copy of this document at the link below:</span><br />
                <a href="{{ $clientRequest['sampleDocumentURL'] }}"> 
                    {{ $clientRequest['sampleDocumentURL'] }}
                </a>
                <br />
            @endif
            <br />
        @endif
    @endforeach
    <p>
        <span style="color:red;font-weight: bold">IMPORTANT NOTE:</span>  
        Failure to respond within 48 hours can lead to delays in the shipment process, and can potentially lead to storage and exam fees, and fines from Customs.
    </p>
    <br/>
    <p>
        Please do not reply to this email, but rather <a href='{{ $signUrl }}'>login</a> to your Clearit account <br/> and submit the requested information via your customer dashboard.
    </p>
    
@endsection

@section('thankyou')
@include('emails.thankyou2')
@endsection
