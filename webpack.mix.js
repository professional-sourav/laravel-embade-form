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

mix.js('resources/js/app.js', 'public/js/app.js')
   .js('resources/js/embed-widget.js', 'public') // emits public/embed-widget.js
   .react()
   .sass('resources/sass/app.scss', 'public/css')
   .version();

// Explicitly configure Terser for production to ensure true minification
mix.webpackConfig({
  optimization: {
    minimize: mix.inProduction(),
    minimizer: [
      new (require('terser-webpack-plugin'))({
        extractComments: false,
        terserOptions: {
          compress: {
            // Drop console logs in production bundles
            drop_console: true,
          },
          format: {
            comments: false,
          },
        },
      }),
    ],
  },
});

// Also emit a .min.js distribution file for the widget in production
// This reads the already-built public/embed-widget.js and writes public/embed-widget.min.js
mix.when(mix.inProduction(), () => {
  mix.minify('public/embed-widget.js');
});
