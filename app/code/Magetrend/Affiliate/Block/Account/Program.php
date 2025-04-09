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

namespace Magetrend\Affiliate\Block\Account;

/**
 * Referral program list block class
 */
class Program extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magetrend\Affiliate\Helper\Account
     */
    public $accountHelper;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magetrend\Affiliate\Model\Config\CommissionType
     */
    public $commissionType;

    /**
     * @var \Magetrend\Affiliate\Model\Config\ProgramType
     */
    public $programType;

    /**
     * @var \Magetrend\Affiliate\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Program\Collection|null
     */
    private $collection = null;

    /**
     * Program constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magetrend\Affiliate\Helper\Account $accountHelper
     * @param \Magetrend\Affiliate\Helper\Data $moduleHelper
     * @param \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory $collectionFactory
     * @param \Magetrend\Affiliate\Model\Config\CommissionType $commissionType
     * @param \Magetrend\Affiliate\Model\Config\ProgramType $programType
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory $collectionFactory,
        \Magetrend\Affiliate\Model\Config\CommissionType $commissionType,
        \Magetrend\Affiliate\Model\Config\ProgramType $programType,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->accountHelper = $accountHelper;
        $this->commissionType = $commissionType;
        $this->programType = $programType;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns current referral account
     * @return null
     */
    public function getAccount()
    {
        return $this->accountHelper->getCurrentAccount();
    }

    /**
     * Returns available referral programs
     * @return \Magetrend\Affiliate\Model\ResourceModel\Program\Collection
     */
    public function getCollection()
    {
        if ($this->collection == null) {
            $this->collection = $this->collectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $this->getAccount()->getProgram()]);
        }

        return $this->collection;
    }

    /**
     * Returns comissions of program
     * @param $referralProgram
     * @return array|double
     */
    public function getCommission($referralProgram)
    {
        $referralProgram->afterLoad();
        $commission = $referralProgram->getCommission();
        return $commission;
    }

    /**
     * Returns commission type label
     * @param $type
     * @return mixed
     */
    public function getCommissionTypeLabel($type)
    {
        $options = $this->commissionType->toArray();
        if (isset($options[$type])) {
            return $options[$type];
        }
        return $type;
    }

    /**
     * Returns program type label
     * @param $type
     * @return mixed
     */
    public function getProgramTypeLabel($type)
    {
        $options = $this->programType->toArray();
        if (isset($options[$type])) {
            return $options[$type];
        }
        return $type;
    }

    /**
     * Format amount
     * @param $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        $account = $this->getAccount();
        $formatedAmmount = $this->moduleHelper->formatPrice($amount, $account->getCurrency());
        $formatedAmmount = explode('.', $formatedAmmount);
        $amount = explode('.', $amount);
        return $formatedAmmount[0].'.'.$amount[1];
    }
}
