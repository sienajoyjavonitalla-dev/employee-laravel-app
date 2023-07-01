const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/signaturePad.js', 'public/js')
    .js('resources/js/sigPad.js', 'public/js')
    .css('resources/css/app.css', 'public/css')
    .css('resources/css/custom.css', 'public/css')
    .css('resources/css/sidebar-menu.css', 'public/css')
    .css('resources/css/pusherchat.css', 'public/css')
    .css('resources/css/signaturePad.css', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();