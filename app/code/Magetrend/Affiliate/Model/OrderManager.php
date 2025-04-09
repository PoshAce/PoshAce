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

class OrderManager
{
    public $orderFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\Record\OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    public function updateStatus($orderId, $recordStatus)
    {
        $order = $this->orderFactory->create();
        $order->load($orderId, 'order_id');

        if (!$order->getId()) {
            return;
        }

        $order->setStatus($recordStatus)
            ->save();
    }
}
