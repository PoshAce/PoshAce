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

namespace Magetrend\Affiliate\Api;

/**
 * API Record Interface
 */
interface RecordInterface
{
    /**
     * @return mixed
     */
    public function recordClick();

    /**
     * @return mixed
     */
    public function recordInvoice();

    /**
     * @return mixed
     */
    public function recordCreditmemo();

    /**
     * @return mixed
     */
    public function recordOrder();
}
