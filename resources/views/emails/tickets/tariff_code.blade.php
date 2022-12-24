@extends('emails.layout')

@section('content')
    Dear Customer,<br /><br />
    Please be advised that the following tariff codes have been attached to your ticket #{{ $ticketNumber }}<br />
    <table border="0" width="100%" align="top">
        @foreach ($tariffCodes as $tariffCode)
            <tr>
                <td style="vertical-align: top;font-weight: bold;width: 15%;">
                    <span style="margin-right:15px; ">{{ $tariffCode['code'] }}</span>
                </td>
                <td>
                    @if (!empty($tariffCode['BasicDutyRateString']))
                    <div>
                        <b> Current duty rate: {{ $tariffCode['BasicDutyRateString'] }} </b>
                        @if ($tariffCode['USTR301'] == 'Y') <span style="color: red"> *</span> @endif
                    </div>
                    @endif
                    <div style="margin: 0px">
                        <div>{{ $tariffCode['description'] }}</div>
                        <div>{{ $tariffCode['mergedDescription'] }}</div>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr>
            <td width="15%"></td>
            <td>
                <div style="margin-top: 15px">
                    <span style="color: red;"> * </span>
                    Indicates that this commodity may be subject to additional duty of up to 25% for goods manufactured in China
                </div>
            </td>
        </tr>
    </table>
    <p>
        If you disagree with these codes, please <a href='{{ $url }}'>sign in</a> to your account and leave me a message in the ticket chat.
    </p>
    <p>
        The above Tariff/Valuation/Technical advice is being tendered to you based on the information provided to us in our discussion and/or your correspondence. This advice must be interpreted as an opinion and is not binding in any way.
    </p>
@endsection

@section('thankyou')
@include('emails.thankyou2')
@endsection



