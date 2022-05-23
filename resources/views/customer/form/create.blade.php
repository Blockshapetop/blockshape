@extends('user')

@section('content')

<!-- Slider main container -->
@inject('figureTypes', 'App\Repositories\FigureTypesRepository')
@inject('diseases', 'App\Repositories\DiseasesRepository')
@inject('allergies', 'App\Repositories\AllergiesRepository')
@inject('excludes', 'App\Repositories\ExcludesRepository')
@inject('targets', 'App\Repositories\TargetsRepository')
@inject('offers', 'App\Repositories\OffersRepository')

<?php
$lists = [
    'bones'             => range(11, 19, 1),
    'figureTypes'       => $figureTypes->all()->toArray(),
    'diseases'          => $diseases->all(),
    'allergies'         => $allergies->all()->toArray(),
    'excludes'          => $excludes->all()->toArray(),
    'offers'            => $offers = $offers->all('online')->toArray(),
];//setForm('department');

$payment = ['gateway' => 'qiwi'];
if ($offerId = Session::get('offer_id')) {
    $payment['offer'] = $offerId;
}
?>
<div ng-controller="UserFormularController" ng-cloak flex>
    <div ng-init="lists = {{ json_encode($lists) }}; payment = {{ json_encode($payment) }}; {{--setForm('setup'); --}}"></div>

    <md-tabs class="no-tab-data" md-no-ink="true" md-no-ink-bar="true" md-enable-disconnect="true" md-selected="currentSlide" md-no-pagination="true" md-swipe-content="false" md-dynamic-height="true">
        <md-tab label="Setup">
            @include('customer.form.slides.setup')
        </md-tab>
        <md-tab label="Record">
            @include('customer.form.slides.record')
        </md-tab>
        <md-tab label="Cardio">
            @include('customer.form.slides.cardio')
        </md-tab>
        {{-- <md-tab label="Schedule">
            @include('customer.form.slides.schedule')
        </md-tab> --}}
        <md-tab label="Diseases">
            @include('customer.form.slides.diseases')
        </md-tab>
        <md-tab label="Nutrition">
            @include('customer.form.slides.nutrition')
        </md-tab>
        {{-- <md-tab label="Order">
            @include('customer.form.slides.order')
        </md-tab> --}}
        <md-tab label="Payment">
            @include('customer.form.slides.payment')
        </md-tab>

    </md-tabs>
</div>
@append

@section('scripts')
    <script>
        window.onbeforeunload = function() {
            var message = '{!! trans('forms.messages.unload') !!}';

            return message;
        }
    </script>
@append

