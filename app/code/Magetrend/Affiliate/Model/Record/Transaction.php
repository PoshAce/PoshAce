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

class Transaction extends \Magento\Framework\Model\AbstractModel
{
    const ENTITY_TYPE = 'order';

    const STATUS_NEW = 'new';

    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'complete';

    const STATUS_CANCELED = 'canceled';

    const STATUS_CLOSED = 'closed';

    const STATUS_HOLDED = 'holded';

    public $moduleHelper;

    public $orderStatus;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\Config\OrderStatus $orderStatus,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->orderStatus = $orderStatus;
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
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Record\Transaction');
    }

    public function getFormattedCommission()
    {
        return $this->moduleHelper->formatPrice($this->getCommission(), $this->getCurrency());
    }

    public function getFormattedAmount()
    {
        return $this->moduleHelper->formatPrice($this->getAmount(), $this->getCurrency());
    }

    public function getFormattedInvoicedAmount()
    {
        return $this->moduleHelper->formatPrice($this->getInvoicedAmount(), $this->getCurrency());
    }

    public function getFormattedRefundedAmount()
    {
        return $this->moduleHelper->formatPrice($this->getRefundedAmount(), $this->getCurrency());
    }

    public function getStatusLabel()
    {
        $options = $this->orderStatus->toArray();
        return isset($options[$this->getStatus()])?$options[$this->getStatus()]:$this->getStatus();
    }
}
