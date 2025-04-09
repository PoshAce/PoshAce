define([
    'Magecomp_Chatgptaicontentpro/js/button',
    'jquery'
], function (Button, $) {
    'use strict';
    return Button.extend({
        onRender: function () {
            // XXX: Intrusively change the position of the button in DOM
            const dataIndex = 'magecomp-ai-button-' + this.settings.type;
            const elem = $('div[data-index=' + dataIndex + ']');
            elem.appendTo(elem.prev());
        },
    });
});
