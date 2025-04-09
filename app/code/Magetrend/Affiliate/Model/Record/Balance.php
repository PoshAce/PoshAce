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

class Balance extends \Magento\Framework\Model\AbstractModel
{
    const OPERATION_CODE_ADD_COMMISION_FOR_ORDER = 'PCFO'; //plus commision for order

    const OPERATION_CODE_REMOVE_COMMISION_FOR_ORDER = 'MCFO';//minus commisions for order

    const OPERATION_CODE_PAYOUT = 'MP'; //minus payout

    const OPERATION_CODE_ADD_COMMISION_FOR_CLICK = 'PCFC'; //plus commission for click

    public $accountHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
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
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Record\Balance');
    }

    public function getFormattedBalance()
    {
        return $this->accountHelper->formatPrice(
            $this->getBalance(),
            $this->getCurrency()
        );
    }

    public function getFormattedAmount()
    {
        return $this->accountHelper->formatPrice(
            $this->getAmount(),
            $this->getCurrency()
        );
    }
}
