@extends('emails.layout')

@section('content')
    Dear {{ $firstname }},

    There has been an update to the Estimated Time of Arrival or Last Free Day on your ticket #{{ $ticketNumber }}.

    Please find the most recent information below:

    @if($etaDate) <strong>ETA:</strong> {{ $etaDate }} @endif

    @if($lastFreeDay) <strong>Last Free Day:</strong> {{ $lastFreeDay }} @endif

    @if($etaComment) <strong>Comments:</strong> {{ $etaComment }} @endif


    If you have any questions or concerns, please contact your Clearit agent.
@endsection