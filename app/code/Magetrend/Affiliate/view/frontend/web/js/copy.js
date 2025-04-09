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
    $.widget('mage.mtafCopy', {
        options: {},
        _create: function() {
            console.log(this.options);
            var referralCode = this.options.referral_code;
            var referralKey = this.options.referral_key;

            $('.mtaf-copy').click(function (e) {
                e.preventDefault();
                var link = $(this);
                var text = link.parent().find('span').text();
                var tmpElm = $('<input id="tempcopy"/>').val(text).hide();
                $('body').append(tmpElm);
                var copyText = document.getElementById("tempcopy");
                copyText.select();
                document.execCommand("copy");
                link.text('Copied');
                setTimeout(function () {
                    link.text('Copy');
                }, 1000);
                $('#tempcopy').remove();
            });
        }
    });
    return $.mage.mtafCopy;
});
