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

/**
 * Send emails cron class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class StatusUpdateBalance
{
    public $balanceManager;

    public $collectionFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Transaction\CollectionFactory $collectionFactory
    ) {
        $this->balanceManager = $balanceManager;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 'update_balance');

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $item) {
            $this->balanceManager->addBalanceForTransaction($item);
            $item->setStatus('finished');
        }

        $collection->walk('save');

        return true;
    }
}
