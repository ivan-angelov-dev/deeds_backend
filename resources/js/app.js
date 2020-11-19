import {LoginPage} from "./pages/auth/login";

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
import Noty from 'noty';
window.Noty = Noty;
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('./libs/blockui.min');
    require('./libs/perfect_scrollbar.min');
    require('./libs/datatables/datatables.min');
    require('./libs/datatables/responsive.min');
    require('./libs/select2.min');
    require('./libs/uniform.min');
    require('./libs/switchery.min');
    require('./libs/switch.min');
    require('./libs/duallistbox.min');
    require('./libs/typeahead.bundle.min');
    require('./libs/anytime.min');
    require('./libs/spectrum');
    require('./libs/pace.min');
    require('./libs/jgrowl.min');
    require('./libs/sweet_alert.min');
    require('./libs/handlebars-v4.0.11');
    require('./libs/limitless');
} catch (e) {}


App.pages = {
    LoginPage: LoginPage

};