// JavaScript Document
var config = {
    deps: ['Magecomp_Mobilelogin/js/login'],
    map: {
        '*': {
            'Magento_Checkout/template/authentication':
                'Magecomp_Mobilelogin/template/authentication'
        },
         '*': {
            'Magento_Customer/js/model/authentication-popup':'Magecomp_Mobilelogin/js/model/authentication-popup'
        }
    },
        'config': {
        'mixins': {
            'Magento_Checkout/js/view/authentication': {
                'Magecomp_Mobilelogin/js/view/authentication': true
            }
        }
    }


};
