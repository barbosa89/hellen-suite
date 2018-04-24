let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .copy('node_modules/font-awesome/fonts', 'public/fonts')
    .styles([
        'public/css/style.css',
        'node_modules/toastr/build/toastr.min.css',
        'node_modules/pretty-checkbox/src/pretty.min.css',
        'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
    ], 'public/css/all.css')
    .scripts([
        'node_modules/toastr/build/toastr.min.js',
        'node_modules/bootstrap-select/dist/js/bootstrap-select.js',
        'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    ], 'public/js/all.js')
    .sourceMaps()
    .browserSync({ proxy: "welkome.dev" });