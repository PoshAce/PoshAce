define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/components/button',
    'uiRegistry'
], function ($, _, Button, Registry) {
    return Button.extend({

        action: function () {
            var form = Registry.get("mst_feed_import_form.areas");
            var source = Registry.get("mst_feed_import_form.mst_feed_import_form_data_source")
            source.client.urls.save = this.actions[0].params.path;
            form.save();
        }
    });
});
