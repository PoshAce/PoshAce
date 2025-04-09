define([], function (Component) { 'use strict';

return function (Component) {
    return Component.extend({

        initialize: function () {
            this._super();
        },

        checkModuleIsEnable: function () {
            return window.checkoutConfig.moduleStatus;
        }
    });
}
});
