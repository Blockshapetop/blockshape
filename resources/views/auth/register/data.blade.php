<form name="customerForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="hidden" name="birth_date[day]" value="@{{ user.birth_date.day }}" required/>
    <input type="hidden" name="birth_date[month]" value="@{{ user.birth_date.month }}" required/>
    <input type="hidden" name="birth_date[year]" value="@{{ user.birth_date.year }}" required/>
    <md-content>
        <div layout="row" layout-align="center center" ng-if="errors">
            <ul class="errors" style="list-style: none;">
                <li ng-repeat="error in errors">
                    @{{ error[0] }}
                </li>
            </ul>
        </div>

        <div layout="row" layout-sm="column" layout-md="column" layout-align="center center">
            <div layout="column" flex="66" layout-padding flex-sm="100" flex-md="100">
                <div layout="row" layout-md="column" layout-sm="column">
                    <md-input-container flex>
                        <label>{{ trans('forms.customer.name') }}</label>
                        {!! Form::text('name', isset($user) ? $user->name : null, ['required' => true, 'ng-model' => 'user.name']) !!}
                    </md-input-container>

                    <span flex="10"></span>

                    <div layout="row" flex>
                        <md-input-container flex>
                            <label>{{ trans('forms.customer.phone_user') }}</label>
                            {!! Form::input('tel', 'phone', isset($user) ? $user->phone : null, ['ng-model' => 'user.phone']) !!}
                        </md-input-container>
                    </div>
                </div>

                <div layout="row" layout-md="column" layout-sm="column">
                    <md-input-container flex>
                        <label>{{ trans('forms.customer.birth_date_day') }}</label>
                        <md-select ng-model="user.birth_date.day" required>
                            <md-option ng-value="day" ng-repeat="day in [{{ join(',', range(1, 31, 1)) }}]">
                                @{{ day }}
                            </md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container flex>
                        <label>{{ trans('forms.customer.birth_date_month') }}</label>
                        <md-select ng-model="user.birth_date.month" required>
                            <md-option ng-value="month.month"
                                       ng-repeat="month in {{ json_encode(months_translated_list()) }}">
                                @{{ month.name }}
                            </md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container flex>
                        <label>{{ trans('forms.customer.birth_date_year') }}</label>
                        <md-select ng-model="user.birth_date.year" required>
                            <md-option ng-value="year"
                                       ng-repeat="year in [{{ join(',', range(date('Y', strtotime("-15 year")), date('Y', strtotime("-100 year")), -1)) }}]">
                                @{{ year }}
                            </md-option>
                        </md-select>
                    </md-input-container>
                </div>

                <md-button ng-disabled="customerForm.$invalid" class="md-raised md-primary"
                           ng-click="register()">{{ trans('user.buttons.register') }}</md-button>
            </div>
        </div>
    </md-content>
</form>
