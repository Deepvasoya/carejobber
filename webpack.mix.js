const mix = require('laravel-mix');
const CompressionPlugin = require('compression-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

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
   .vue()
   .sass('resources/assets/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false,
       postCss: [
           require('autoprefixer')(),
           require('cssnano')({
               preset: ['default', {
                   discardComments: {
                       removeAll: true
                   }
               }]
           })
       ]
   })
   .webpackConfig({
       optimization: {
           minimize: true,
           minimizer: [
               new TerserPlugin({
                   terserOptions: {
                       compress: {
                           drop_console: true,
                           drop_debugger: true
                       },
                       output: {
                           comments: false
                       }
                   }
               })
           ],
           splitChunks: {
               chunks: 'all',
               minSize: 20000,
               maxSize: 244000,
               minChunks: 1,
               maxAsyncRequests: 30,
               maxInitialRequests: 30,
               automaticNameDelimiter: '~',
               enforceSizeThreshold: 50000,
               cacheGroups: {
                   defaultVendors: {
                       test: /[\\/]node_modules[\\/]/,
                       priority: -10
                   },
                   default: {
                       minChunks: 2,
                       priority: -20,
                       reuseExistingChunk: true
                   }
               }
           }
       },
       plugins: [
           new CompressionPlugin({
               filename: '[path][base].gz',
               algorithm: 'gzip',
               test: /\.js$|\.css$|\.html$|\.svg$/,
               threshold: 10240,
               minRatio: 0.8
           })
       ]
   });

if (mix.inProduction()) {
    mix.version();
}
