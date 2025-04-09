define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data' 
], function ($, modal, customerData) {
    'use strict';
    customerData.reload(customerData.get('customer'));

    return {
        modalWindow: null,    

        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         */
        createPopUp: function (element) {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-authentication',
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout',
                'buttons': []
            };

            this.modalWindow = element;
            modal(options, $(this.modalWindow));
        },

        /** Show login popup window */
        showModal: function () {
            if($("#layout").val() != undefined) {            
                var layout = $("#layout").val();
                var options = {
                    'type': 'popup',
                    'modalClass' : 'customer-login-popup '+ layout,
                    'focus': '[name=username]',
                    'responsive': true,
                    'innerScroll': true,
                    'trigger': '.proceed-to-checkout',
                    'buttons': []
                };
                modal(options, $('#customer-popup-login'));
                $("#customer-popup-login").modal('openModal');
                $("#customer-popup-login").addClass("ultimate")
                $(".registratio-section").hide();
                $(".forgot-password-section").hide();
                $(".login-section").show();
                $(".forgotimage").hide();
                $(".regimage").hide();
                $(".loginimage").show();
            }else {
                $(this.modalWindow).modal('openModal').trigger('contentUpdated');
            }
        }
    };
});