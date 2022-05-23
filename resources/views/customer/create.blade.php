@extends('app')

@section('content')
    <div class="search" ng-controller="SignupController" ng-cloak>
        <div layout="row" layout-align="center">
            <div flex="80" flex-sm="100">
                <div layout="column">
                    <md-whiteframe class="md-whiteframe-z3" style="padding: 60px;">
                        <div layout="row" flex layout-align="center center" class="logo">
                            <a href="{{ url('home') }}"><img src="/images/logo.png" alt=""/></a>
                        </div>

                        @if ($errors)
                            <ul class="errors">
                                @foreach ($errors->all() as $key => $value)
                                    <li>{{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif


                        <?php
                        foreach (['email', 'phone', 'name', 'birth_date'] as $key) {
                            if ($value = old($key)) {
                                $form[$key] = $value;
                            }
                        }
                        ?>

                        <form method="post" ng-init="customer = {{ json_encode($form) }}" name="customerForm"
                              action="{{ isset($user) ? route('customer.update', ['customer' => $user]) :  route('customer.store') }}"
                              enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @if (isset($user))
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                            <input type="hidden" name="birth_date[day]" value="@{{ customer.birth_date.day }}"
                                   required/>
                            <input type="hidden" name="birth_date[month]" value="@{{ customer.birth_date.month }}"
                                   required/>
                            <input type="hidden" name="birth_date[year]" value="@{{ customer.birth_date.year }}"
                                   required/>
                            <md-content>
                                <div layout="row" layout-sm="column" layout-md="column">
                                    <div layout="column" flex="30" flex-sm="100" flex-md="100" layout-align="end center"
                                         layout-padding>
                                        <div class="image-placeholder {{ ($hasImage = isset($user) && $user->hasImage() ? 'has-image' : '') }}">
                                            @if ($hasImage)
                                                <img src="{{ $user->imageUrl(App\User::IMAGE_SIZE_MEDIUM) }}" alt="">
                                            @endif
                                            {!! Form::file('image', ['']) !!}
                                        </div>
                                    </div>
                                    <div layout="column" flex layout-padding flex-sm="100" flex-md="100">
                                        <div layout="row" layout-md="column" layout-sm="column">
                                            <div flex>
                                                <md-input-container>
                                                    <label>{{ trans('forms.customer.name') }}</label>
                                                    {!! Form::text('name', isset($user) ? $user->name : null, ['required' => true, 'ng-model' => 'customer.name']) !!}
                                                </md-input-container>
                                            </div>

                                            <div flex>
                                                <md-input-container>
                                                    <label>{{ trans('forms.customer.phone') }}</label>
                                                    {!! Form::input('tel', 'phone', isset($user) ? $user->phone : null, ['required' => true, 'ng-model' => 'customer.phone']) !!}
                                                </md-input-container>
                                            </div>
                                        </div>

                                        <div layout="row" layout-md="column" layout-sm="column">
                                            <div flex>
                                                <md-input-container>
                                                    <label>{{ trans('forms.customer.email') }}</label>
                                                    {!! Form::email('email', isset($user) ? $user->email : null, ['ng-model' => 'customer.email']) !!}
                                                </md-input-container>
                                            </div>

                                            <div flex="10"></div>

                                            <div layout="row">
                                                <md-input-container>
                                                    {{--{!! Form::input('date', 'birth_date', isset($user) ? $user->birth_date : null, ['required' => true, 'ng-model' => 'customer.birth_date']) !!}--}}
                                                    <label>{{ trans('forms.customer.birth_date_day') }}</label>
                                                    <md-select ng-model="customer.birth_date.day" required>
                                                        <md-option ng-value="day"
                                                                   ng-repeat="day in [{{ join(',', range(1, 31, 1)) }}]">
                                                            @{{ day }}
                                                        </md-option>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container>

                                                    <label>{{ trans('forms.customer.birth_date_month') }}</label>
                                                    <md-select ng-model="customer.birth_date.month" required>
                                                        <md-option ng-value="month.month"
                                                                   ng-repeat="month in {{ json_encode(months_translated_list()) }}">
                                                            @{{ month.name }}
                                                        </md-option>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container>
                                                    <label>{{ trans('forms.customer.birth_date_year') }}</label>
                                                    <md-select ng-model="customer.birth_date.year" required>
                                                        <md-option ng-value="year"
                                                                   ng-repeat="year in [{{ join(',', range(date('Y', strtotime("-15 year")), date('Y', strtotime("-100 year")), -1)) }}]">
                                                            @{{ year }}
                                                        </md-option>
                                                    </md-select>
                                                </md-input-container>

                                            </div>
                                        </div>


                                        <md-button ng-disabled="customerForm.$invalid"
                                                   class="md-raised md-primary">{{ (isset($user) ? trans('forms.buttons.edit_customer') : trans('forms.buttons.add_customer')) }}</md-button>
                                    </div>
                                </div>
                            </md-content>
                        </form>
                    </md-whiteframe>
                </div>
            </div>
        </div>
    </div>
@endsection


