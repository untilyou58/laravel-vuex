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

mix.browserSync({
  proxy: '0.0.0.0:8081', // アプリの起動アドレス
  open: false, // ブラウザを自動で開かない
  port: 3002
  })
  .js('resources/js/app.js', 'public/js')
  .version()
