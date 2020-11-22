const mix = require('laravel-mix');

mix.js([
   'resources/js/app.js',
   'resources/js/utility.js',
   'resources/js/repository.js',
   'resources/js/message.js',
   ], 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
      processCssUrls: false
   }
);