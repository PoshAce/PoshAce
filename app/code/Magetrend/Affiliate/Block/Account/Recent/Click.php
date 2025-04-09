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

namespace Magetrend\Affiliate\Block\Account\Recent;

/**
 * Clicks report block class
 */
class Click extends \Magetrend\Affiliate\Block\Report\Click
{
    /**
     * Returns items per page limit
     * @return int
     */
    public function getPageLimit()
    {
        return 10;
    }
}
