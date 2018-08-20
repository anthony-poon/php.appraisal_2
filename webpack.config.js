var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .addEntry("base", [
        "./assets/base.js",
        "./assets/base.scss",
        "./assets/share/form.js"
    ])
    .addEntry("admin/list_user", [
        "./assets/admin/list_user.js",
    ])
    .addEntry("admin/list_user_group", [
        "./assets/admin/list_user_group.js",
    ])
    .createSharedEntry('vendor', [
        'jquery',
        'bootstrap',
        'datatables.net-bs4/css/dataTables.bootstrap4.css',
        'datatables.net-buttons-bs4/css/buttons.bootstrap4.css',
        'datatables.net-select-bs4/css/select.bootstrap4.css'
    ])
    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader()
    .autoProvidejQuery()
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    // .addEntry('js/app', './assets/js/app.js')
    // .addStyleEntry('css/app', './assets/css/app.scss')
;

module.exports = Encore.getWebpackConfig();
