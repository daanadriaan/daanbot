const mix = require('laravel-mix');

mix.setPublicPath('public/');


mix.options({
    processCssUrls: false,
}).version();

mix
    //.extract(['vue', 'axios'])
    .js('resources/js/daanbot.js', 'public/js')
    .js('resources/js/botmaker.js', 'public/js')
    .js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]).version()
    .vue();
