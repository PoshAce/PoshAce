define([
    'jquery',
    'intlTelInput'
], function ($) {
    'use strict';

    var initIntl = function(config, node) {
        $(node).intlTelInput(config);
    };

    var assignCustomIdAndValidate = function() {
        // Assign custom id once the DOM is ready
        $('.intl-tel-input.allow-dropdown input[type="text"]').attr('id', 'custom_id');

        // Validate input for digits only and max 10 digits
        $('#custom_id').on('input', function() {
            let val = $(this).val().replace(/\D/g, '');
            if (val.length > 10) {
                val = val.slice(0, 10);
            }
            $(this).val(val);
        });
    };

    return {
        initIntl: initIntl,
        assignCustomIdAndValidate: assignCustomIdAndValidate
    };
});
