@extends('front.app')

@section('content')

@include('front.partials.account-tabs')

<div class="container">
    <div class="row"><br><br>
        <div class="goto-block">
            <h4>Customised meal and exercises plan</h4>
            <a  href="{{ route('order-offer', ['offer' => 5]) }}">Fill the application form</a>
            <p>Our app will generate customised meal and exercises plan, based on Your body parameters and desired goals</p>
        </div>
    </div>
</div>
<br><br>
@include('front.partials.prefooter')

@include('front.partials.footer-line')

@stop

