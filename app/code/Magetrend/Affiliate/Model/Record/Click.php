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

namespace Magetrend\Affiliate\Model\Record;

use Magetrend\Affiliate\Model\ResourceModel\Record\Click\Collection;

class Click extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_NEW = 'new';

    const STATUS_FINISHED = 'finished';

    public $accountHelper;

    public $balanceCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Click\CollectionFactory $balanceCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Record\Click');
    }

    public function isUnique()
    {
        $date = date('Y-m-d', strtotime($this->getDate()));
        $balanceRecordCollection = $this->balanceCollectionFactory->create()
            ->addFieldToFilter('referral_id', $this->getReferralId())
            ->addFieldToFilter('ip', $this->getIp())
            ->addFieldToFilter('date', ['gteq' => $date.' 00:00:00'])
            ->addFieldToFilter('date', ['lt' => $this->getDate()]);

        if ($balanceRecordCollection->getSize() > 0) {
            return false;
        }

        return true;
    }
}
