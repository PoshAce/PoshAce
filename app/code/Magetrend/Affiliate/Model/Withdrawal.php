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
use Magetrend\Affiliate\Model\ResourceModel\Withdrawal\Collection;

class Withdrawal extends \Magento\Framework\Model\AbstractModel
{
    const FIRST_INCREMENT_ID = '10000001';

    const STATUS_NEW = 'new';

    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELED = 'canceled';

    /**
     * Event prefix
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_eventPrefix = 'affiliate_withdrawal';

    public $accountHelper;

    public $moduleHelper;

    public $accountFactory;

    public $date;

    public $withdrawalStatus;

    public $withdrawalCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magetrend\Affiliate\Model\Config\WithdrawalStatus $withdrawalStatus,
        \Magetrend\Affiliate\Model\ResourceModel\Withdrawal\CollectionFactory $withdrawalCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->accountFactory = $accountFactory;
        $this->accountHelper = $accountHelper;
        $this->moduleHelper = $moduleHelper;
        $this->date = $date;
        $this->withdrawalStatus = $withdrawalStatus;
        $this->withdrawalCollectionFactory = $withdrawalCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Withdrawal');
    }

    public function initRequest($amount, $referralId)
    {
        $referral = $this->accountFactory->create()
            ->load($referralId);

        $baseCurrency = $referral->getBaseCurrency();
        $currency = $referral->getCurrency();
        $baseAmount = $this->moduleHelper->currencyConvert($amount, $currency, $baseCurrency, true, 2);

        $this->setData([
            'increment_id' => $this->getNextIncrementId(),
            'referral_id' => $referral->getId(),
            'created_at' => $this->date->gmtDate(),
            'base_amount_request' => $baseAmount,
            'amount_request' => $amount,
            'currency' => $currency,
            'base_currency' => $baseCurrency,
            'status' => self::STATUS_NEW
        ])->save();

        $referral->recalculateBalance();
    }

    public function cancel($withdrawalId, $referralId)
    {
        $referral = $this->accountFactory->create()
            ->load($referralId);

        $this->load($withdrawalId);

        if ($this->getReferralId() == $referral->getId()) {
            $this->setStatus(self::STATUS_CANCELED)
                ->setFinishedAt($this->date->gmtDate('Y-m-d H:i:s'))
                ->save();
        }
        $referral->recalculateBalance();
    }

    public function validateRequest($amount, $referralId)
    {
        $referral = $this->accountFactory->create()
            ->load($referralId);
        if (!$referral->getId()) {
            throw new LocalizedException(__('Unable to load identify referral account'));
        }

        $minimumAmount = $this->moduleHelper->getMinimumWithdrawAmount();
        if ($amount < $minimumAmount) {
            throw new LocalizedException(
                __('Minimum withdrawl amount is: %1', [$this->moduleHelper->getMinimumWithdrawAmount(true)])
            );
        }

        if ($referral->getAvailableBalance() < $amount) {
            throw new LocalizedException(__('Maximum available amount: %1', [
                $referral->getFormattedAvailableBalance()
            ]));
        }
    }

    public function getStatusLabel()
    {
        $options = $this->withdrawalStatus->toArray();
        return $options[$this->getStatus()];
    }

    public function getFormattedAmountRequest()
    {

        return $this->moduleHelper->formatPrice(
            $this->getAmountRequest(),
            $this->getCurrency()
        );
    }

    public function payout()
    {
        if ($this->getStatus() == self::STATUS_COMPLETED && $this->getFinishedAt() == null) {
            $this->setFinishedAt($this->date->gmtDate('Y-m-d H:i:s'));
        }
    }

    public function getNextIncrementId()
    {
        $collection = $this->withdrawalCollectionFactory->create()
            ->setPageSize(1)
            ->setCurPage(1)
            ->setOrder('entity_id', 'desc');

        if ($collection->getSize() == 0) {
            return self::FIRST_INCREMENT_ID;
        }

        $lastIncrementId = $collection->getFirstItem()->getIncrementId();
        if (empty($lastIncrementId)) {
            return self::FIRST_INCREMENT_ID;
        }

        $lastIncrementId = str_replace('#', '', $lastIncrementId);
        $nextIncrementId = $lastIncrementId + 1;

        return $nextIncrementId;
    }
}
