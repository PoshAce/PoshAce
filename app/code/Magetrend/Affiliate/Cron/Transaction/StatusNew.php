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

namespace Magetrend\Affiliate\Cron\Transaction;

use \Magetrend\Affiliate\Model\Record\Order;

/**
 * Send emails cron class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class StatusNew
{
    public $commissionManager;

    public $orderManager;

    public $collectionFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\CommissionManager $commissionManager,
        \Magetrend\Affiliate\Model\OrderManager $orderManager,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Transaction\CollectionFactory $collectionFactory
    ) {
        $this->orderManager = $orderManager;
        $this->commissionManager = $commissionManager;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 'new');

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $item) {
            $this->commissionManager->processTransaction($item);
            $this->orderManager->updateStatus($item->getOrderId(), Order::STATUS_UPDATE_AMOUNT);
            $item->setStatus('update_balance');
        }

        $collection->walk('save');
        return true;
    }
}
