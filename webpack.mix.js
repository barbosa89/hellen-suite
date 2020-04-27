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
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/pdf.scss', 'public/css')
    .copy('node_modules/font-awesome/fonts', 'public/fonts')
    .styles([
        'public/css/sb-admin.css',
        'node_modules/toastr/build/toastr.min.css',
        'node_modules/pretty-checkbox/dist/pretty-checkbox.min.css',
        'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
        'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'node_modules/sweetalert2/dist/sweetalert2.css'
    ], 'public/css/welkome.css')
    .scripts([
        'node_modules/moment/min/moment.min.js',
        'node_modules/toastr/build/toastr.min.js',
        'node_modules/bootstrap-select/dist/js/bootstrap-select.js',
        'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        'node_modules/jquery.easing/jquery.easing.js',
        'node_modules/sweetalert2/dist/sweetalert2.all.js',
        'public/js/sb-admin.js',
        'node_modules/chart.js/dist/Chart.bundle.js',
        'public/js/common.js'
    ], 'public/js/welkome.js')
    .styles([
        'public/css/animation-aos.css',
        'public/css/aos.css',
        'public/css/style.css',
    ], 'public/css/landing.css')
    .scripts([
        'public/js/aos.js',
        'public/js/responsiveslides.min.js',
        'public/js/move-top.js',
        'node_modules/jquery.easing/jquery.easing.js'
    ], 'public/js/landing.js')
    .sourceMaps()
    .browserSync({
        proxy: 'welkome.app'
    });