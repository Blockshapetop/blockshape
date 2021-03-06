<?php

// added Main Routes

Route::get('/', [
    'as' => 'home',
    'uses' => 'PagesController@index'
]);

Route::get('free-week', [
    'as' => 'free.week',
    'uses' => 'PagesController@freeWeek'
]);

Route::get('water', [
    'as' => 'water',
    'uses' => 'PagesController@water'
]);

Route::group(["middleware" => "auth"], function () {

    Route::get('/account/consultation', [
        'as' => 'consultation',
        'uses' => 'AccountController@getConsultationPage'
    ]);

    Route::get('/account/calendar', [
        'as' => 'calendar',
        'uses' => 'AccountController@getCalendarPage'
    ]);

    Route::get('/account/messages', [
        'as' => 'calendar',
        'uses' => 'AccountController@getMessagesPage'
    ]);

    Route::get('/account/current-shoping', [
        'as' => 'current-shoping',
        'uses' => 'AccountController@getCurrentShopingPage'
    ]);

    Route::get('/account/graph', [
        'as' => 'graph',
        'uses' => 'AccountController@getGraphPage'
    ]);

    Route::post('/post/graph', [
        'as' => 'post.graph',
        'uses' => 'AccountController@postGraph'
    ]);

    Route::get('/account/motivational', [
        'as' => 'motivational',
        'uses' => 'AccountController@motivationListPage'
    ]);

    Route::get('/account', [
        'as' => 'account',
        'uses' => 'AccountController@index'
    ]);

    Route::get('/account/{page?}', [
        'as' => 'account.page',
        'uses' => 'AccountController@pages'
    ]);

    // change user data(account)
    Route::post('/edit', [
        'as' => 'edit.account',
        'uses' => 'AccountController@editAccount'
    ]);

    // change traning date(ajax)
    Route::post('/traning-chnage', [
        'as' => 'traning-change.account',
        'uses' => 'FormsController@changeTraning'
    ]);

    Route::get('test-page', [
        'uses' => '\App\Week\Manager@daysByRecipes'
    ]);

    Route::get('history/{date}', [
        'as' => 'history.date',
        'uses' => 'FormsController@changeHistory'
    ]);

    Route::post('history/date', [
        'as' => 'history.date.post',
        'uses' => 'FormsController@changeHistoryPost'
    ]);

    //carts routes
    Route::get('cart/', [
        'as' => 'cart',
        'uses' => 'CartController@index'
    ]);

    Route::get('cart/subscriptions/{id}', [
        'as' => 'cart.subscriptions',
        'uses' => 'CartController@subscriptions'
    ]);

    Route::get('cart/events/{id}', [
        'as' => 'cart.events',
        'uses' => 'CartController@events'
    ]);

    Route::get('cart/seminars/{id}', [
        'as' => 'cart.seminars',
        'uses' => 'CartController@seminars'
    ]);

    Route::get('cart/consults/{id}', [
        'as' => 'cart.consults',
        'uses' => 'CartController@consults'
    ]);

    Route::get('cart/delete/{id}', [
        'as' => 'cart.delete',
        'uses' => 'CartController@delete'
    ]);

    Route::get('pay', [
        'as' => 'pay.get',
        'uses' => 'CartController@getPay',
    ]);

    Route::post('accont/diary/set', [
        'as' => 'diary.set',
        'uses' => 'AccountController@setDiary'
    ]);

    Route::post('accont/motivation/add', [
        'as' => 'motivation.add',
        'uses' => 'AccountController@motivationAdd'
    ]);

    Route::get('test/payment', [
        'as' => 'test.payment',
        'uses' => 'MainController@testPayment'
    ]);

    Route::get('add-free-week', [
        'as' => 'add.free.week',
        'uses' => 'AccountController@addFreeWeek'
    ]);

    Route::get('pdf', [
        'as' => 'show.pdf',
        'uses' => 'MainController@showPdf'
    ]);

    Route::get('rebuild-ration', [
        'as' => 'rebuild.ration',
        'uses' => 'FormsController@rebuildRation'
    ]);
});

Route::get('/page/archive/{year}', [
    'as' => 'archive',
    'uses' => 'PagesController@getArchive'
]);

Route::get('search', [
    'as' => 'search',
    'uses' => 'SearchController@index'
]);

Route::get('page/{page}', [
    'as' => 'page',
    'uses' => 'PagesController@getPages'
]);

Route::get('seminars/{seminar}', [
    'as' => 'seminars.single',
    'uses' => 'PagesController@getSingleSeminar'
]);

Route::get('events/{event}', [
    'as' => 'events.single',
    'uses' => 'PagesController@getSingleEvent'
]);

Route::get('projects/{project}', [
    'as' => 'project.single',
    'uses' => 'PagesController@getSingleProject'
]);

// Route::get('/some', [
//     'as' => 'some',
//     'uses' => 'CustomerController@download'
// ]);

Route::get('/test', [
    'as' => 'test',
    'uses' => 'MainController@foo'
]);

Route::get('/getPdf/{id}', [
    'as' => 'getPdf',
    'uses' => 'MainController@getPdf'
]);

Route::get('/pdf/{id}', [
    'as' => 'pdf',
    'uses' => 'MainController@pdf'
]);

Route::get('/login', [
    'as' => 'login',
    'uses' => 'Auth\AuthController@login'
]);

// Route::post('/login', [
//     'as' => 'post.login',
//     'uses' => 'MainController@postLogin'
// ]);

Route::post('/login', [
    'as' => 'post.login',
    'uses' => 'Auth\AuthController@loginPost'
]);

Route::get('/register', [
    'as' => 'register',
    'uses' => 'Auth\AuthController@register'
]);

Route::post('/register', [
    'as' => 'post.register',
    'uses' => 'Auth\AuthController@registerPost'
]);

Route::get('/record', [
    'as' => 'main.record',
    'uses' => 'MainController@getrecord'
]);


// end of main routes

