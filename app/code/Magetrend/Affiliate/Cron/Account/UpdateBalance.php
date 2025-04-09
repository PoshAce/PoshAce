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

namespace Magetrend\Affiliate\Cron\Account;

/**
 * Send emails cron class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class UpdateBalance
{
    public $balanceManager;

    public $collectionFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager,
        \Magetrend\Affiliate\Model\ResourceModel\Account\CollectionFactory $collectionFactory
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
            ->addFieldToFilter('require_recalculate', 1);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $account) {
            $this->balanceManager->updateAccountBalance($account);
            $account->setRequireRecalculate(0);
        }
        $collection->walk('save');

        return true;
    }
}
