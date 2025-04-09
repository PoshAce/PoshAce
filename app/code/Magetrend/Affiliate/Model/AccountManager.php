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
use Magetrend\Affiliate\Model\ResourceModel\Record\Balance\Collection;

class AccountManager
{
    public $accountFactory;

    public $date;

    public $registry;

    public function __construct(
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $registry
    ) {
        $this->accountFactory = $accountFactory;
        $this->date = $date;
        $this->registry = $registry;
    }

    public function updateAccount($data, $id = null)
    {
        $account = $this->accountFactory->create();
        if ($id) {
            $account->load($id);
        }

        $account->addData($data)
            ->save();
    }

    public function cancelAccount($affiliateAccountId)
    {
        $this->updateAccount(['status' => \Magetrend\Affiliate\Model\Account::STATUS_CANCELED], $affiliateAccountId);
        return true;
    }

    public function deleteAccount($affiliateAccountId)
    {
        $this->updateAccount(['status' => \Magetrend\Affiliate\Model\Account::STATUS_DELETED], $affiliateAccountId);
        return true;
    }

    public function initAccount($accountId = null)
    {
        $account = $this->accountFactory->create();
        if ($accountId) {
            $account->load($accountId);
        }

        $this->registry->register('current_model', $account);
    }
}
