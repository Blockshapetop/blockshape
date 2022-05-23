<div layout="column">
    <div layout="row" layout-align="center start">
        <div layout="column" flex="66">
            <p>{{ trans('user.register.requirements.header') }}</p>
            <ol style="margin: 10px 0 20px 40px;">
                <li>{{ trans('user.register.requirements.current_weight') }}</li>
                <li>{{ trans('user.register.requirements.measuring_tape') }}</li>
                <li>{{ trans('user.register.requirements.tonometer') }}</li>
            </ol>
        </div>
    </div>

    <div layout="row" layout-align="center center">
        <md-button
                ng-repeat="gender in data.genders"
                ng-class="{'md-primary': user.gender == gender.value}"
                class="md-raised gender-btn"
                ng-click="setGender(gender)">
            <i class="fa fa-3x"
               ng-class="{ 'fa-female' : gender.value == 'female', 'fa-male' : gender.value == 'male' }"></i>
            <br/>@{{ gender.label }}
        </md-button>
    </div>

    <md-button class="md-primary md-raised" ng-disabled="! user.gender" ng-click="slideNext()">{{ trans('pagination.next') }}</md-button>

</div>

