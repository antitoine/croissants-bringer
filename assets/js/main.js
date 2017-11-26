require('../scss/global.scss');

var $ = require('jquery');

require('bootstrap-sass');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});