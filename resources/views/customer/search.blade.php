@extends('app')

@section('content')
<div class="search" ng-controller="SearchController">
    <div layout="row" layout-align="center" layout-padding>
        <div flex="66" flex-sm="95">
            <div layout="column" layout-padding>
                <md-whiteframe class="md-whiteframe-z3" layout-padding>
                    <div layout="row" flex layout-align="center center" class="logo">
                        <a href="{{ url('home') }}"><img src="/images/logo.png" alt=""/></a>
                    </div>
                    <div layout="row" flex>
                        <div layout="column" flex>
                            <md-content layout-padding>
                                <form>
                                    <md-autocomplete
                                            md-delay="500"
                                            md-min-length="3"
                                            md-autofocus="true"
                                            ng-disabled="isDisabled"
                                            md-selected-item="selectedItem"
                                            md-search-text-change="searchTextChange(searchText)"
                                            md-search-text="searchText"
                                            md-selected-item-change="selectedItemChange(item)"
                                            md-items="item in querySearch(searchText)"
                                            md-item-text="item.name"
                                            placeholder="{{ trans('forms.customer.search') }}">

                                        <md-item-template>
                                            <md-list-item class="md-3-line">
                                                <img ng-src="@{{ item.images.data.small }}" ng-if="item.images.data.small" class="md-avatar" alt="" />
                                                <div class="md-list-item-text">
                                                    <a ng-href="/customer/@{{ item.id }}">
                                                        <h3>@{{ item.name }}</h3>
                                                        <p>@{{ item.phone }}</p>
                                                    </a>
                                                </div>
                                            </md-list-item>
                                        </md-item-template>

                                        <md-not-found>
                                            {{ trans('forms.customer.not_found') }}: <a href="{{ url('customer/register') }}" class="md-button md-primary md-nolnk">{{ trans('forms.buttons.add_customer') }}</a>
                                        </md-not-found>
                                    </md-autocomplete>

                                </form>
                            </md-content>
                        </div>
                    </div>
                </md-whiteframe>
            </div>
        </div>
    </div>
</div>
@append

