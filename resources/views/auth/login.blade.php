@extends('user')

@section('content')

    <md-card flex="33">
        <md-card-content>
            <form action="{{ url('auth/login') }}" method="post" autocomplete="off">
                <md-content>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <md-input-container>
                        <label>{{ trans('forms.email') }}</label>
                        <input type="email" name="email" class="md-large" required autocomplete="off"/>
                    </md-input-container>
                    <md-input-container>
                        <label>{{ trans('forms.password') }}</label>
                        <input type="password" name="password" required autocomplete="off"/>
                    </md-input-container>
                    <div layout="row" layout-align="center center">
                        <md-button class="md-raised md-warn">{{ trans('forms.buttons.login') }}</md-button>

                        {!! link_to_route('customer.signup', 'Sunt utilizator') !!}
                    </div>
                </md-content>
            </form>
        </md-card-content>
    </md-card>

@endsection


