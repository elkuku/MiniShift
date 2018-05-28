const fs = require("fs");
const Encore = require('@symfony/webpack-encore');

const CopyWebpackPlugin = require('copy-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './assets/js/app.js')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    //.enableBuildNotifications()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()

// allow sass/scss files to be processed
// .enableSassLoader()

    .addPlugin(new CopyWebpackPlugin([
        {
            from: 'assets/images/',
            to: 'images/'
        }
    ]))

    .addPlugin(new ImageminPlugin({ test: /\.(jpe?g|png|gif|svg)$/i }))

;

// export the final configuration
//module.exports = Encore.getWebpackConfig();

let config = Encore.getWebpackConfig();

if(!Encore.isProduction()) {
    fs.writeFile("fakewebpack.config.js", "module.exports = "+JSON.stringify(config), function(err) {
        if(err) {
            return console.log(err);
        }
        console.log("fakewebpack.config.js written");
    });
}

module.exports = config;
