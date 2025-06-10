/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
    './shipping-rates-validation-rules',
    '../model/address-converter',
    '../action/select-shipping-address',
    './postcode-validator',
    './default-validator',
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/quote'
], function (
    $,
    ko,
    shippingRatesValidationRules,
    addressConverter,
    selectShippingAddress,
    postcodeValidator,
    defaultValidator,
    $t,
    uiRegistry,
    formPopUpState,
    shippingService
) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        validators = [],
        observedElements = [],
        postcodeElements = [],
        postcodeElementName = 'postcode';

    validators.push(defaultValidator);

    return {
        validateAddressTimeout: 0,
        validateZipCodeTimeout: 0,
        validateDelay: 2000,

        /**
         * @param {String} carrier
         * @param {Object} validator
         */
        registerValidator: function (carrier, validator) {
            if (checkoutConfig.activeCarriers.indexOf(carrier) !== -1) {
                validators.push(validator);
            }
        },

        /**
         * @param {Object} address
         * @return {Boolean}
         */
        validateAddressData: function (address) {
            return validators.some(function (validator) {
                return validator.validate(address);
            });
        },

        /**
         * Perform postponed binding for fieldset elements
         *
         * @param {String} formPath
         */
        initFields: function (formPath) {
            var self = this,
                elements = shippingRatesValidationRules.getObservableFields();

            if ($.inArray(postcodeElementName, elements) === -1) {
                // Add postcode field to observables if not exist for zip code validation support
                elements.push(postcodeElementName);
            }

            $.each(elements, function (index, field) {
                uiRegistry.async(formPath + '.' + field)(self.doElementBinding.bind(self));
            });
        },

        /**
         * Bind shipping rates request to form element
         *
         * @param {Object} element
         * @param {Boolean} force
         * @param {Number} delay
         */
        doElementBinding: function (element, force, delay) {
            var observableFields = shippingRatesValidationRules.getObservableFields();

            if (element && (observableFields.indexOf(element.index) !== -1 || force)) {
                if (element.index !== postcodeElementName) {
                    this.bindHandler(element, delay);
                }
            }

            if (element.index === postcodeElementName) {
                this.bindHandler(element, delay);
                postcodeElements.push(element);
            }
        },

        /**
         * @param {*} elements
         * @param {Boolean} force
         * @param {Number} delay
         */
        bindChangeHandlers: function (elements, force, delay) {
            var self = this;

            $.each(elements, function (index, elem) {
                self.doElementBinding(elem, force, delay);
            });
        },

        /**
         * @param {Object} element
         * @param {Number} delay
         */
        bindHandler: function (element, delay) {
            var self = this;

            delay = typeof delay === 'undefined' ? self.validateDelay : delay;

            if (element.component.indexOf('/group') !== -1) {
                $.each(element.elems(), function (index, elem) {
                    self.bindHandler(elem);
                });
            } else {
                element.on('value', function () {
                    clearTimeout(self.validateZipCodeTimeout);
                    self.validateZipCodeTimeout = setTimeout(function () {
                        if (element.index === postcodeElementName) {
                            self.postcodeValidation(element);
                        } else {
                            $.each(postcodeElements, function (index, elem) {
                                self.postcodeValidation(elem);
                            });
                        }
                    }, delay);

                    if (!formPopUpState.isVisible()) {
                        // Prevent shipping methods showing none available whilst we resolve
                        shippingService.isLoading(true);
                        clearTimeout(self.validateAddressTimeout);
                        self.validateAddressTimeout = setTimeout(function () {
                            self.validateFields();
                        }, delay);
                    }
                });
                observedElements.push(element);
            }
        },

        /**
         * @return {*}
         */
        postcodeValidation: function (postcodeElement) {
    var countryId = $('select[name="country_id"]:visible').val(),
        validationResult,
        errorMessage;

    if (postcodeElement == null || postcodeElement.value() == null) {
        return true;
    }

    postcodeElement.error(null); // Clear any previous error messages

    var postcodeValue = postcodeElement.value();

    // Check for exactly 6 digits
    if (!/^\d{6}$/.test(postcodeValue)) {
        errorMessage = $t('Please enter a valid 6-digit Postal Code.');
        postcodeElement.error(errorMessage);
        return false;
    }

    validationResult = postcodeValidator.validate(postcodeValue, countryId);

    if (!validationResult) {
        errorMessage = $t('Provided Zip/Postal Code seems to be invalid.');

        if (postcodeValidator.validatedPostCodeExample.length) {
            errorMessage += $t(' Example: ') + postcodeValidator.validatedPostCodeExample.join('; ') + '. ';
        }
        errorMessage += $t('If you believe it is the right one, you can ignore this notice.');
        postcodeElement.error(errorMessage); // Use error instead of warn
        return false;
    }

    return true;
},


        /**
         * Convert form data to quote address and validate fields for shipping rates
         */
        validateFields: function () {
            var addressFlat = addressConverter.formDataProviderToFlatData(
                this.collectObservedData(),
                'shippingAddress'
                ),
                address;

            if (this.validateAddressData(addressFlat)) {
                addressFlat = uiRegistry.get('checkoutProvider').shippingAddress;
                address = addressConverter.formAddressDataToQuoteAddress(addressFlat);
                selectShippingAddress(address);
            }
        },

        /**
         * Collect observed fields data to object
         *
         * @returns {*}
         */
        collectObservedData: function () {
            var observedValues = {};

            $.each(observedElements, function (index, field) {
                observedValues[field.dataScope] = field.value();
            });

            return observedValues;
        }
    };
});
