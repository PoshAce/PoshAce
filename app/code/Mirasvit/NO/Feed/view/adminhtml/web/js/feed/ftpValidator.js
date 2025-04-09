define([
    'jquery',
    'uiRegistry',
    'prototype'
], function ($,uiRegistry, ui) {
    'use strict';

    $.widget('mirasvit.ftpValidator', {
        options: {
            url: null
        },

        _create: function () {
            this._bind();

            this._super();
        },

        _bind: function () {

            var self = this;
            this.element
                .off('click.button')
                .on('click.button', $.proxy(this['validate'], this));
        },

        validate: function () {
            var self = this;
            var formData = uiRegistry.get("mst_feed_feed_form.mst_feed_feed_form_data_source").data;
            formData.form_key = $('[name="form_key"]').val();
            $.ajax(this.options.url, {
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    self.element.trigger('processStart');
                },
                complete: function (response) {
                    if (response.responseText.isJSON()) {
                        var json = response.responseText.evalJSON();

                        $('[data-role=ftp-message]').remove();
                        var message = $('<div>')
                            .addClass('message')
                            .addClass('message-' + json.status)
                            .attr('data-role', 'ftp-message')
                            .html(json.message);

                        message.insertAfter(self.element);
                    } else {
                        $('[data-role=ftp-message]').remove();
                        var message = $('<div>')
                            .addClass('message')
                            .addClass('message-error')
                            .attr('data-role', 'ftp-message')
                            .html(response.responseText);

                        message.insertAfter(self.element);
                    }
                    self.element.trigger('processStop');
                }
            });
        },
    });

    return $.mirasvit.ftpValidator;
});
