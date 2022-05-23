@extends('emails.layout')

@section('content')
    <h1>{{ trans('emails.instructor.password.hello', ['name' => $name]) }}</h1>

    {!! auto_p(trans('emails.instructor.password.body', ['password' => $password])) !!}
@endsection

