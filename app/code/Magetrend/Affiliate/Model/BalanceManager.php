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
use Magetrend\Affiliate\Model\Withdrawal;
use Magetrend\Affiliate\Model\Record\Balance;

class BalanceManager
{
    public $accountFactory;

    public $clickFactory;

    public $orderFactory;

    public $date;

    public $transactionCollectionFactory;

    public $balanceCollectionFactory;

    public $orderCollectionFactory;

    public $moduleHelper;

    public $withdrawalCollectionFactory;

    public $balanceFactory;

    public $commissionManager;

    public $currencyHelper;

    public function __construct(
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\Record\ClickFactory $clickFactory,
        \Magetrend\Affiliate\Model\Record\TransactionFactory $orderFactory,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Order\CollectionFactory $orderCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Balance\CollectionFactory $balanceCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Withdrawal\CollectionFactory $withdrawalCollectionFactory,
        \Magetrend\Affiliate\Model\Record\BalanceFactory $balanceFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magetrend\Affiliate\Model\CommissionManager $commissionManager,
        \Magento\Directory\Helper\Data $currencyHelper
    ) {
        $this->accountFactory = $accountFactory;
        $this->clickFactory = $clickFactory;
        $this->date = $date;
        $this->orderFactory = $orderFactory;
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->withdrawalCollectionFactory = $withdrawalCollectionFactory;
        $this->balanceFactory = $balanceFactory;
        $this->commissionManager = $commissionManager;
        $this->currencyHelper = $currencyHelper;
    }

    public function addBalanceRecord(
        $referralId,
        $baseAmount,
        $code,
        $entityType = null,
        $entityId = null,
        $comment = ''
    ) {
        if ($baseAmount == 0) {
            return false;
        }

        $referral = $this->accountFactory->create()
            ->load($referralId);

        if ($referral->getId()) {
            $referral->setData('require_recalculate', 1)
                ->save();
        }

        if ($entityId != null && $entityType != null) {
            $recordCollection = $this->balanceCollectionFactory->create()
                ->addFieldToFilter('referral_id', $referralId)
                ->addFieldToFilter('related_entity_type', $entityType)
                ->addFieldToFilter('related_entity_id', $entityId);

            if ($recordCollection->getSize() != 0) {
                return false;
            }
        }

        $latestRecord = $this->balanceCollectionFactory->create()
            ->addFieldToFilter('referral_id', $referralId)
            ->setPageSize(1)
            ->setCurPage(1)
            ->setOrder('entity_id', 'DESC');

        if ($latestRecord->getSize() == 0) {
            $currentBalance = 0;
            $currentBaseBalance = 0;
        } else {
            $latestRecord = $latestRecord->getData();
            $latestRecord = end($latestRecord);
            $currentBalance = $latestRecord['balance'];
            $currentBaseBalance = $latestRecord['base_balance'];
        }

        $baseCurrency = $referral->getBaseCurrency();
        $currency = $referral->getCurrency();
        $amount = $this->moduleHelper->currencyConvert($baseAmount, $baseCurrency, $currency, true, 2);

        $balanceRecord = $this->balanceFactory->create()
            ->setData([
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'code' => $code,
                'referral_id' => $referral->getId(),
                'base_currency' => $baseCurrency,
                'currency' => $currency,
                'base_amount' => $baseAmount,
                'amount' => $amount,
                'base_balance' => ($currentBaseBalance + $baseAmount),
                'balance' => ($currentBalance + $amount),
                'comment' => $comment,
                'created_at' => $this->date->gmtDate('Y-m-d H:i:s')
            ])->save();

        return $balanceRecord;
    }

    public function addBalanceForTransaction($transaction)
    {
        $operationCode = 'unknown';
        $comment = 'unknown';
        if ($transaction->getObjectType() == 'invoice') {
            $operationCode = Balance::OPERATION_CODE_ADD_COMMISION_FOR_ORDER;
            $comment = __('Commissions for order #%1', $transaction->getOrderIncrementId());
        }

        if ($transaction->getObjectType() == 'creditmemo') {
            $operationCode = Balance::OPERATION_CODE_REMOVE_COMMISION_FOR_ORDER;
            $comment = __('The order #%1 was refunded', $transaction->getOrderIncrementId());
        }

        $this->addBalanceRecord(
            $transaction->getReferralId(),
            $transaction->getBaseCommission(),
            $operationCode,
            $transaction->getObjectType(),
            $transaction->getObjectId(),
            $comment
        );
    }

    public function addBalanceForClick($click)
    {
        $operationCode = Balance::OPERATION_CODE_ADD_COMMISION_FOR_CLICK;
        $comment = __('Commissions for click. From IP: #%1 Date: %2', $click->getIp(), $click->getDate());

        if (!$click->isUnique()) {
            return;
        }

        $program = $this->commissionManager->getProgram(
            $click->getReferralId(),
            \Magetrend\Affiliate\Model\Program::TYPE_PAY_PER_CLICK
        );

        if (!$program) {
            return;
        }

        $commission = $program->getFixedCommission();
        if ($commission <= 0) {
            return;
        }

        $account = $this->accountFactory->create();
        $account->load($click->getReferralId());

        if (!$account->getId()) {
            return;
        }

        $currency = $program->getCurrency();
        $baseCurrency = $account->getBaseCurrency();
        $baseCommissionAmount = $this->moduleHelper->currencyConvert($commission, $currency, $baseCurrency);

        $this->addBalanceRecord(
            $click->getReferralId(),
            $baseCommissionAmount,
            $operationCode,
            'click',
            $click->getId(),
            $comment
        );
    }

    public function updateAccountBalance($account)
    {
        $lastBalanceRecord = $this->balanceCollectionFactory->create()
            ->addFieldToFilter('referral_id', $account->getId())
            ->setPageSize(1)
            ->setCurPage(1)
            ->setOrder('entity_id', 'desc');

        if ($lastBalanceRecord->getSize() == 0) {
            $balance = 0;
            $baseBalance = 0;
            $availableBalance = 0;
            $baseAvailableBalance = 0;
        } else {
            $lastBalanceRecord = $lastBalanceRecord->getData();
            $lastBalanceRecord = end($lastBalanceRecord);
            $balance = $lastBalanceRecord['balance'];
            $baseBalance = $lastBalanceRecord['base_balance'];
        }

        $baseReservedAmount = $this->calculateReserve($account);
        $reservedAmount = $this->moduleHelper->currencyConvert(
            $baseReservedAmount,
            $account->getBaseCurrency(),
            $account->getCurrency()
        );

        $availableBalance = $balance - $reservedAmount;
        $baseAvailableBalance = $baseBalance - $baseReservedAmount;

        $account->setBalance($balance)
            ->setBaseBalance($baseBalance)
            ->setBaseAvailableBalance($baseAvailableBalance>0?$baseAvailableBalance:0)
            ->setAvailableBalance($availableBalance>0?$availableBalance:0)
            ->setBaseReservedBalance($baseReservedAmount)
            ->setReservedBalance($reservedAmount)
            ->save();
    }

    /**
     * Calculate reserve in base currency
     * @param $account
     * @return int
     */
    public function calculateReserve($account)
    {
        $holdForDays = $this->moduleHelper->getTimeOnHold();
        $diff = $holdForDays * 24 * 60 * 60;

        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('created_at', ['gt' => $this->date->date('Y-m-d H:i:s', time() - $diff)])
            ->addFieldToFilter('referral_id', $account->getId())
            ->addFieldToFilter('is_visible', 1);

        $amount = 0;
        if ($orderCollection->getSize() > 0) {
            foreach ($orderCollection as $order) {
                $amount+= $order->getBaseCommissions();
            }
        }

        $withdrawalRequestCollection = $this->withdrawalCollectionFactory->create()
            ->addFieldToFilter('referral_id', $account->getId())
            ->addFieldToFilter('status', ['in' => [Withdrawal::STATUS_NEW]]);

        if ($withdrawalRequestCollection->getSize() > 0) {
            foreach ($withdrawalRequestCollection as $withdrawal) {
                $amount += $withdrawal->getBaseAmountRequest();
            }
        }

        return $amount;
    }
}
