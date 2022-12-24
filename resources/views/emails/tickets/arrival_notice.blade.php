@extends('emails.layout')

@section('content')
    Dear Customer,

    Please note in order for your cargo to be released from it's warehouse, regardless of the customs status, all Forwarder & Arrival notice fees* must be paid in full. If you would like Clearit to handle the payments on your behalf, please notify your agent.
@endsection

@section('thankyou')
@include('emails.thankyou')
<tr>
    <td style="padding:20px;padding-top:0px">
        <p>*handling fees apply</p>
    </td>
</tr>
@endsection
