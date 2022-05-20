var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.styles([
        '../bower_components/font-awesome/css/font-awesome.css',
        '../bower_components/angular-material/angular-material1sa.css',
    ], 'public/css/vendor.css');

    mix.styles([
        '../landing/css/fonts.css',
        '../landing/css/main.css',
        '../landing/css/reset.css',
        '../landing/css/media.css',
        '../bower_components/font-awesome/css/font-awesome.css'
    ], 'public/css/landing.css');

    mix.less('app.less');

    mix.scripts([
            'jquery/dist/jquery.js',
            'angular/angular.js',
            'angular-animate/angular-animate.js',
            'angular-aria/angular-aria.js',
            'angular-storage/dist/angular-storage.js',
            'angular-material/angular-material.js'
        ], 'public/js/vendor.js', 'resources/assets/bower_components')
        .scripts([
            'app.js',
            'helpers.js',
            'templates.js',
            'services/*.js',
            'controllers/*.js',
            'directives/*.js'
        ]);

    mix.copy('resources/assets/landing/img', 'public/landing/img');
    mix.copy('resources/assets/landing/fonts', 'public/build/fonts');
    mix.copy('resources/assets/bower_components/font-awesome/fonts', 'public/build/fonts');

    mix.version([
        'css/app.css',
        'css/vendor.css',
        'css/landing.css',
        'js/all.js'
    ]);
});
