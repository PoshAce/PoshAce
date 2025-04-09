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
use Magetrend\Affiliate\Model\Program;

class CommissionManager
{
    public $accountFactory;

    public $clickFactory;

    public $orderFactory;

    public $date;

    public $transactionCollectionFactory;

    public $balanceCollectionFactory;

    public $moduleHelper;

    public $withdrawalCollectionFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\Record\ClickFactory $clickFactory,
        \Magetrend\Affiliate\Model\Record\TransactionFactory $orderFactory,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Balance\CollectionFactory $balanceCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\Withdrawal\CollectionFactory $withdrawalCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->accountFactory = $accountFactory;
        $this->clickFactory = $clickFactory;
        $this->date = $date;
        $this->orderFactory = $orderFactory;
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->withdrawalCollectionFactory = $withdrawalCollectionFactory;
    }

    public function processTransaction($transaction)
    {
        $affiliateProgram = $this->getProgram($transaction->getReferralId(), Program::TYPE_PAY_PER_SALE);
        if (!$affiliateProgram) {
            return false;
        }

        $commissionTier = $affiliateProgram->getCommissionTier($transaction->getOrderCounter());
        if (!$commissionTier) {
            return false;
        }

        $baseTransactionCurrency = $transaction->getBaseCurrency();
        $transactionCurrency = $transaction->getCurrency();

        $amount = $transaction->getAmount();
        $baseAmount = $transaction->getBaseAmount();

        if ($commissionTier['type'] == \Magetrend\Affiliate\Model\Config\CommissionType::TYPE_PERCENTAGE_FROM_TOTAL) {
            $rate = $commissionTier['rate'];
            $commission = number_format($rate * $amount / 100, 2);
            $baseCommission = number_format($rate * $baseAmount / 100, 2);
        }

        if ($commissionTier['type'] == \Magetrend\Affiliate\Model\Config\CommissionType::TYPE_FIXED_AMOUNT) {
            $fixedAmount = $commissionTier['rate'];
            $programCurrency = $affiliateProgram->getCurrency();
            $commission = $this->moduleHelper->currencyConvert(
                $fixedAmount,
                $programCurrency,
                $transactionCurrency,
                true,
                2
            );

            $baseCommission = $this->moduleHelper->currencyConvert(
                $fixedAmount,
                $programCurrency,
                $baseTransactionCurrency,
                true,
                2
            );
        }

        if ($transaction->getType() == 'creditmemo') {
            $commission = $commission * -1;
            $baseCommission = $baseCommission * -1;
        }

        $transaction->setProgramId($affiliateProgram->getId())
            ->setCommission($commission)
            ->setBaseCommission($baseCommission)
            ->setStatus('update_balance')
            ->save();
    }

    public function getProgram($referralId, $type)
    {
        $referralAccount = $this->accountFactory->create();
        $referralAccount->load($referralId);
        if (!$referralAccount->getId()) {
            return false;
        }

        $programCollection = $referralAccount->getProgramCollection();
        if ($programCollection->getSize() == 0) {
            return false;
        }

        foreach ($programCollection as $program) {
            if ($program->getType() == $type) {
                return $program;
            }
        }

        return false;
    }
}
