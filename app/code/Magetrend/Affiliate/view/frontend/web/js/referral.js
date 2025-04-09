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
    $.widget('mage.mtafReferral', {
        options: {},
        _create: function() {
            var url = window.location.href.replace('?', '&');
            if (url.indexOf('&' + this.options.paramKey + '=') === -1) {
                return;
            }
            this._sendRequest();
        },
        
        _sendRequest: function (postData) {
            $.ajax({
                url: this.options.actionUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    url: window.location.href,
                },
                success: function(resp) {
                    console.log('OK');
                }
            });
        }
    });
    return $.mage.mtafReferral;
});
