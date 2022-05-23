@extends('emails.layout')

@section('content')
    <h3>{{ trans('emails.admin.record.hello') }}</h3>

    {!! auto_p(trans('emails.admin.record.body', ['instructor' => $instructor, 'url' => $url])) !!}
@endsection


