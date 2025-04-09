/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

;;;define([
    'jquery',
    'Magento_Ui/js/modal/modal'

], function ($, modal) {
    'use strict';

    $.widget('mage.entityEditForm', {
        options: {
            openSelector: '.open-modal-edit',
            modalSelector: '#modal-container',
            modalContext: '#modal-context',
            spinnerSelector: '*[data-role="spinner"]',
            formSubmitSelector: '.submit-form',
            title: '',
            primary_button_title: '',
            formSelector: '#edit_form',
        },

        _create: function () {
            this._setupEvents();
            this._setupModal();
        },

        _setupModal: function () {

            console.log(this.options);
            var mainObject = this;
            var primaryButton = {};

            if (this.options.primary_button_title != '') {
                var primaryButton = {
                    text: $.mage.__(this.options.primary_button_title),
                    class: 'primary submit-form',
                    click: function () {
                        mainObject._submitForm();
                    }
                };
            }


            var options = {
                type: 'slide',
                responsive: true,
                innerScroll: true,
                title: this.options.title,
                buttons: [
                    primaryButton,
                    {
                        text: $.mage.__('Close'),
                        class: '',
                        click: function () {
                            this.closeModal();
                        }
                    }
                ]
            };

            var popup = modal(options, $(this.options.modalSelector));
        },

        _setupEvents: function () {
            $(this.options.openSelector).on('click', this, this._initModalConfigure);
        },

        _setupFormEvents: function () {
            $(this.options.formSubmitSelector).unbind('click').on('click', this, this._initFormSubmit);
        },

        _initModalConfigure: function(event) {
            event.preventDefault();
            event.data._initModal(event);
        },

        _initFormSubmit: function(event) {
            event.preventDefault();
            event.data._submitForm(event);
        },

        _initModal: function (event) {
            var mainObject = this;
            this._showLoadingMask();
            $(mainObject.options.modalContext).html(' ');
            $(this.options.modalSelector).modal('openModal');
            var url = $(event.target).data('form');
            $.ajax({
                showLoader: false,
                url: url,
                data: 'isAjax=true',
                type: "GET"
            }).done(function (data) {
                $(mainObject.options.modalContext).html(data);
                mainObject._prepareContent();
                mainObject._setupFormEvents();
                mainObject._hideLoadingMask();
            });
        },

        _prepareContent: function () {
            $('.accordion').appendTo(this.options.formSelector);
        },

        _showLoadingMask: function () {
            $(this.options.spinnerSelector).show();
        },

        _hideLoadingMask: function () {
            $(this.options.spinnerSelector).hide();
        },

        _submitForm: function (event) {
            $(this.options.formSelector).submit();
        }
    });

    return $.mage.entityEditForm;
});
