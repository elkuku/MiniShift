// require jQuery normally
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

require('../../node_modules/bootstrap/dist/css/bootstrap.min.css')
require('../../node_modules/bootstrap/dist/js/bootstrap.min')

require('../css/app.css');
