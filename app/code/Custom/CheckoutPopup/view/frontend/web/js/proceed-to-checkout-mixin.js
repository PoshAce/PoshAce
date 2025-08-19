define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'text!Custom_CheckoutPopup/template/popup.html'
], function ($, modal, popupTpl) {
    'use strict';

    return function (originalInit) {
        return function (config, element) {
            var CLICK_NAMESPACE = '.customCheckoutPopup';

            // Intercept click before original handler is bound
            $(element).off('click' + CLICK_NAMESPACE).on('click' + CLICK_NAMESPACE, function (event) {
                var $button = $(this);

                if ($button.data('checkoutPopupConfirmed') === true) {
                    $button.data('checkoutPopupConfirmed', false);
                    return; // allow original handler to proceed
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                var $container = $('#custom-checkout-popup');

                if ($container.length === 0) {
                    $container = $('<div id="custom-checkout-popup" style="display:none;"></div>');
                    $container.html(popupTpl);
                    $('body').append($container);
                }

                var modalInstance = modal({
                    type: 'popup',
                    title: (config && config.popupTitle) ? config.popupTitle : 'Before you proceed',
                    modalClass: 'custom-checkout-popup',
                    responsive: true,
                    innerScroll: true,
                    buttons: [
                        {
                            text: (config && config.confirmText) ? config.confirmText : 'Continue to checkout',
                            class: 'action primary',
                            click: function () {
                                $button.data('checkoutPopupConfirmed', true);
                                this.closeModal();
                                $button.trigger('click');
                            }
                        },
                        {
                            text: (config && config.cancelText) ? config.cancelText : 'Cancel',
                            class: 'action secondary',
                            click: function () {
                                this.closeModal();
                            }
                        }
                    ]
                }, $container);

                $container.modal('openModal');
            });

            return originalInit(config, element);
        };
    };
});

