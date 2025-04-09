/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

define(['jquery'], function ($) {
        'use strict';
    $.widget('mage.mtafReferralForm', {
        options: {},
        _create: function() {
            console.log(this.options);

            var main = this;
            $("#affiliate_url").bind('input propertychange', function(e) {
                main.generateLink(e);
            });

            $( "#affiliate_url" ).keyup(function(e) {
                main.generateLink(e);
            });

            $('.copy-to-clipboard').click(function () {
                var copyText = document.getElementById("generated_link");
                copyText.select();
                document.execCommand("copy");
                $('.copy-to-clipboard').text('Copied');
                setTimeout(function () {
                    $('.copy-to-clipboard').text('Copy');
                }, 1000);
            });
        },

        generateLink: function (e) {
            var referralCode = this.options.referral_code;
            var referralKey = this.options.referral_key;
            var url = $('#affiliate_url').val();
            var resultContainter = $('#affiliate_link');
            if (url.length > 0 && url.indexOf(".") > -1) {
                var referralLink = '';
                if (url.indexOf("http") == -1) {
                    referralLink = 'https://';
                }

                if (url.indexOf("?") == -1) {
                    //if (url.slice(-1) != '/') {
                        //url = url + '/';
                    //}
                    referralLink = referralLink + url + '?';
                } else {
                    referralLink = referralLink + url + '&'
                }

                referralLink = referralLink + referralKey + '=' + referralCode;
                resultContainter.find('#generated_link').val(referralLink);
                resultContainter.show();
            } else {
                resultContainter.hide();
            }
        }


    });
    return $.mage.mtafReferralForm;
});
