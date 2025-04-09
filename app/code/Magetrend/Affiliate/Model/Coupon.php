<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Model;

use Magento\Framework\Exception\LocalizedException;
use Magetrend\Affiliate\Model\ResourceModel\Withdrawal\Collection;

class Coupon extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Coupon');
    }
}
