@extends('emails.layout')

@section('content')
    <h4>Bună, {{ $user->name }}</h4>

    <p>
        Felicitări, comanda Testării Nutriționale și Fiziologice a fost procesată.

        <strong>{!! link_to($download, 'Descarcă testarea ta') !!}.</strong>
    </p>
@endsection

