let mix = require('laravel-mix')

mix.js('resources/js/field.js', 'dist/js').vue()
   .sass('resources/sass/field.scss', 'dist/css')
    .webpackConfig({
        resolve: {
            symlinks: false
        }
    })

    