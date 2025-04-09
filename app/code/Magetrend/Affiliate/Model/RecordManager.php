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

class RecordManager
{
    public $accountFactory;

    public $clickFactory;

    public $transactionFactory;

    public $orderFactory;

    public $transactionCollectionFactory;

    public $orderCollectionFactory;

    public $commissionManager;

    public $balanceManager;

    public $date;

    public $moduleHelper;

    public $storeManager;

    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\Record\ClickFactory $clickFactory,
        \Magetrend\Affiliate\Model\Record\TransactionFactory $transactionFactory,
        \Magetrend\Affiliate\Model\Record\OrderFactory $orderFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Order\CollectionFactory $orderCollectionFactory,
        \Magetrend\Affiliate\Model\CommissionManager $commissionManager,
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->accountFactory = $accountFactory;
        $this->clickFactory = $clickFactory;
        $this->date = $date;
        $this->transactionFactory = $transactionFactory;
        $this->orderFactory = $orderFactory;
        $this->commissionManager = $commissionManager;
        $this->balanceManager = $balanceManager;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function recordClick($data)
    {
        if (!$this->isRecordClickDataValid($data)) {
            return false;
        }

        $referralCode = $data['referral_code'];
        $account = $this->accountFactory->create()
            ->load($referralCode, 'referral_code');

        if (!$account->getId()) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($data['store_id'])->getWebsiteId();
        if ($account->getWebsiteId() != $websiteId) {
            return false;
        }

        $url = rtrim(str_replace([
            \Magetrend\Affiliate\Helper\Data::REFERRAL_PARAM_KEY.'='.$referralCode.'&',
            \Magetrend\Affiliate\Helper\Data::REFERRAL_PARAM_KEY.'='.$referralCode,
        ], '', $data['url']), '?');

        $this->clickFactory->create()
            ->setData([
                'date' => $this->date->gmtDate('Y-m-d H:i:s', $data['time']),
                'ip' => $data['ip'],
                'referral_id' => $account->getId(),
                'url' => $url,
                'status' => \Magetrend\Affiliate\Model\Record\Click::STATUS_NEW
            ])->save();

        return true;
    }

    public function recordTransaction($data)
    {
        if (!$this->isRecordOrderDataValid($data)) {
            return false;
        }

        $referralCode = $data['referral_code'];
        
        /**
         * @var \Magetrend\Affiliate\Model\Account $account
         */
        $account = $this->accountFactory->create()
            ->load($referralCode, 'referral_code');

        if (!$account->getId()) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($data['store_id'])->getWebsiteId();
        if ($account->getWebsiteId() != $websiteId) {
            return false;
        }

        if (!$this->isTransactionRecordNew($data['object_id'], $data['object_type'])) {
            return false;
        }

        $data['order_counter'] = $this->getNthOfOrder($account->getId(), $data['customer_id'], $data['order_id']);

        $data['referral_id'] = $account->getId();
        $data['status'] = 'new';

        if ($data['amount'] > 0 && $data['object_type'] =='creditmemo') {
            $data['amount'] *=-1;
        }

        $orderCurrency = $data['currency'];
        $orderAmount = $data['amount'];
        $baseCurrency = $account->getBaseCurrency();
        $currency = $account->getCurrency();

        $data['base_amount'] = $this->moduleHelper->currencyConvert(
            $orderAmount,
            $orderCurrency,
            $baseCurrency,
            true,
            2
        );

        $data['amount'] = $this->moduleHelper->currencyConvert(
            $orderAmount,
            $orderCurrency,
            $currency,
            true,
            2
        );

        $data['currency'] = $currency;
        $data['base_currency'] = $baseCurrency;

        $this->transactionFactory->create()
            ->addData($data)
            ->save();
    }

    public function getNthOfOrder($referralId, $customerId, $orderId)
    {
        $transactionCollection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('referral_id', $referralId)
            ->addFieldToFilter('customer_id', $customerId);

        if ($transactionCollection->getSize() < 1) {
            return 1;
        }

        $counterArray = [];
        foreach ($transactionCollection as $transaction) {
            if ($transaction->getOrderId() == $orderId) {
                if ($transaction->getOrderCounter() > 0) {
                    return $transaction->getOrderCounter();
                }
            } else {
                $counterArray[$transaction->getOrderId()] = 1;
            }
        }

        return count($counterArray) + 1;
    }

    public function isTransactionRecordNew($objectId, $objectType)
    {
        $transactionCollection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('object_type', $objectType)
            ->addFieldToFilter('object_id', $objectId);

        return $transactionCollection->getSize() == 0;
    }

    public function recordOrder($data)
    {
        if (!$this->isRecordOrderDataValid($data)) {
            return false;
        }

        $referralCode = $data['referral_code'];
        /**
         * @var \Magetrend\Affiliate\Model\Account $account
         */
        $account = $this->accountFactory->create()
            ->load($referralCode, 'referral_code');

        if (!$account->getId()) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($data['store_id'])->getWebsiteId();
        if ($account->getWebsiteId() != $websiteId) {
            return false;
        }

        $order = $this->orderFactory->create();
        $order->load($data['order_id'], 'order_id');
        $data['referral_id'] = $account->getId();
        $data['status'] = \Magetrend\Affiliate\Model\Record\Order::STATUS_UPDATE_AMOUNT;

        $orderCurrency = $data['currency'];
        $orderAmount = $data['amount'];

        $baseCurrency = $account->getBaseCurrency();
        $currency = $account->getCurrency();

        $data['base_amount'] = $this->moduleHelper->currencyConvert(
            $orderAmount,
            $orderCurrency,
            $baseCurrency,
            true,
            2
        );
        $data['amount'] = $this->moduleHelper->currencyConvert(
            $orderAmount,
            $orderCurrency,
            $currency,
            true,
            2
        );
        $data['currency'] = $currency;
        $data['base_currency'] = $baseCurrency;

        $order->addData($data)->save();
    }

    public function isRecordClickDataValid($data)
    {
        if (!isset($data['referral_code']) || empty($data['referral_code'])) {
            return false;
        }

        if (!isset($data['ip']) || empty($data['ip']) || strlen($data['ip']) > 15) {
            return false;
        }

        if (!isset($data['url']) || empty($data['url'])) {
            return false;
        }

        if (!isset($data['store_id']) || empty($data['store_id'])) {
            return false;
        }

        return true;
    }

    public function isRecordOrderDataValid($data)
    {
        if (!isset($data['referral_code']) || empty($data['referral_code'])) {
            return false;
        }

        if (!isset($data['order_id']) || empty($data['order_id'])) {
            return false;
        }

        if (!isset($data['store_id']) || empty($data['store_id'])) {
            return false;
        }

        return true;
    }

    public function updateOrderAmount($orderRecord)
    {
        $transactionCollection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderRecord->getOrderId())
            ->addFieldToFilter('object_type', ['in' => ['invoice', 'creditmemo']]);

        $baseInvoiced = 0;
        $invoiced = 0;
        $baseRefunded = 0;
        $refunded = 0;
        $baseCommission = 0;
        $commission = 0;
        $orderCounter = 0;

        if ($transactionCollection->getSize() > 0) {
            foreach ($transactionCollection as $transaction) {
                if ($transaction->getObjectType() == 'invoice') {
                    $invoiced += $transaction->getAmount();
                    $baseInvoiced += $transaction->getBaseAmount();
                    $orderCounter = $transaction->getOrderCounter();
                } elseif ($transaction->getObjectType() == 'creditmemo') {
                    $refunded += $transaction->getAmount();
                    $baseRefunded += $transaction->getBaseAmount();
                }

                $commission += $transaction->getCommission();
                $baseCommission += $transaction->getBaseCommission();
            }
        }

        /**
         * Mark it visible then invoice is created and commissions is calculated.
         */
        if ($invoiced > 0 && $commission > 0) {
            $orderRecord->setIsVisible(1);
        }

        if ($orderCounter > 0) {
            $orderRecord->setOrderCounter($orderCounter);
        }

        $orderRecord
            ->setBaseInvoicedAmount($baseInvoiced)
            ->setInvoicedAmount($invoiced)
            ->setBaseRefundedAmount($baseRefunded)
            ->setRefundedAmount($refunded)
            ->setBaseCommissions($baseCommission)
            ->setCommissions($commission)
            ->save();
    }
}
