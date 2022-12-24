@extends('emails.layout')

@section('content')
    Dear customer,

    Your (clearance or Car Import) is now complete. A total amount of ${{ number_format($amount, 2) }} has been charged to
    your card on file. Your good are now ready for you to pick up.
    {{ $specialNotes }}
@endsection
