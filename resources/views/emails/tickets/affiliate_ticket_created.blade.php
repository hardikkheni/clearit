@extends('emails.layout')

@section('content')
    Hi {{ $firstname }} {{ $lastname }},

    Thank you for completing your account registration and for selecting {{ $serviceName }} as your Customs Broker. {{ $companyname }} has partnered with {{ $serviceName }} to offer a frictionless digital customs experience. Please be sure to monitor your emails for further communications.

    Your first shipment ticket has been created and you can access it via your dashboard in the {{ $serviceName }} portal.  You can communicate with our agents using the chat located inside your ticket.
@endsection
