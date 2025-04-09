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
use Magetrend\Affiliate\Model\Record\Balance;
use Magetrend\Affiliate\Model\Withdrawal;

class WithdrawalManager
{
    public $accountFactory;

    public $withdrawalFactory;

    public $balanceManager;

    public $date;

    public $moduleHelper;

    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\WithdrawalFactory $withdrawalFactory,
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->accountFactory = $accountFactory;
        $this->withdrawalFactory = $withdrawalFactory;
        $this->balanceManager = $balanceManager;
        $this->date = $date;
        $this->moduleHelper = $moduleHelper;
    }

    public function payout($withdrawalId, $amount, $comment = '')
    {
        $withdrawal = $this->withdrawalFactory->create();
        $withdrawal->load($withdrawalId);

        if (!$withdrawal->getId()) {
            throw new LocalizedException(__('The withdrawal data record is not found'));
        }

        $account = $this->accountFactory->create();
        $account->load($withdrawal->getReferralId());

        if (!$account->getId()) {
            throw new LocalizedException(__('The account data record is not found'));
        }

        if ($withdrawal->getStatus() != Withdrawal::STATUS_NEW) {
            throw new LocalizedException(__('Withdrawal was already processed.'));
        }

        if ($account->getBalance() < $amount) {
            throw new LocalizedException(__('The account balance is not enough for this payout.'));
        }

        $baseCurrency = $account->getBaseCurrency();
        $currency = $withdrawal->getCurrency();
        $baseAmount = $this->moduleHelper->currencyConvert($amount, $currency, $baseCurrency, true, 2);

        $this->balanceManager->addBalanceRecord(
            $withdrawal->getReferralId(),
            -$baseAmount,
            Balance::OPERATION_CODE_PAYOUT,
            null,
            null,
            __('Withdrawal #%1', $withdrawal->getIncrementId())
        );

        $withdrawal->setStatus(Withdrawal::STATUS_COMPLETED)
            ->setAmountPaid($amount)
            ->setBaseAmountPaid($baseAmount)
            ->setComment($comment)
            ->setFinishedAt($this->date->gmtDate('Y-m-d H:i:s'))
            ->save();

        $account->setBasePayoutAmount($account->getBasePayoutAmount() + $baseAmount)
            ->setPayoutAmount($account->getPayoutAmount() + $amount)
            ->save();

        return true;
    }

    public function cancel($withdrawalId, $comment = '')
    {
        $withdrawal = $this->withdrawalFactory->create();
        $withdrawal->load($withdrawalId);

        if (!$withdrawal->getId()) {
            throw new LocalizedException(__('The withdrawal data record is not found'));
        }

        $withdrawal->setStatus(Withdrawal::STATUS_CANCELED)
            ->setComment($comment)
            ->setFinishedAt($this->date->gmtDate('Y-m-d H:i:s'))
            ->save();
        return true;
    }
}
