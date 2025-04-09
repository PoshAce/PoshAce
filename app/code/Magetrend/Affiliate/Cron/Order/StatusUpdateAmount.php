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

namespace Magetrend\Affiliate\Cron\Order;

/**
 * Send emails cron class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class StatusUpdateAmount
{
    public $orderCollectionFactory;

    public $recordManager;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Record\Order\CollectionFactory $orderCollectionFactory,
        \Magetrend\Affiliate\Model\RecordManager $recordManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->recordManager = $recordManager;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', \Magetrend\Affiliate\Model\Record\Order::STATUS_UPDATE_AMOUNT);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $order) {
            $this->recordManager->updateOrderAmount($order);
            $order->setStatus(\Magetrend\Affiliate\Model\Record\Order::STATUS_FINISHED);
        }

        $collection->walk('save');

        return true;
    }
}
