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

namespace Magetrend\Affiliate\Cron\Click;

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
    public $clickCollectionFactory;

    public $balanceManager;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Record\Click\CollectionFactory $clickCollectionFactory,
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager
    ) {
        $this->clickCollectionFactory = $clickCollectionFactory;
        $this->balanceManager = $balanceManager;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->clickCollectionFactory->create()
            ->addFieldToFilter('status', \Magetrend\Affiliate\Model\Record\Click::STATUS_NEW);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $item) {
            $this->balanceManager->addBalanceForClick($item);
            $item->setStatus(\Magetrend\Affiliate\Model\Record\Click::STATUS_FINISHED);
        }

        $collection->walk('save');
        return true;
    }
}
