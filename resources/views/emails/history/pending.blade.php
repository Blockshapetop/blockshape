@extends('emails.layout')

@section('content')
    <h1>Hello {{ $admin->name }}</h1>

    <p>
        There is an user history record pending your approval.
    </p>

    @if ($record->instructor && $record->office)
    <p>
        Record created by {{ $record->instructor->name }} at {{ $record->office->name }}.
    </p>
    @endif
@endsection
