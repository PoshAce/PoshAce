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

namespace Magetrend\Affiliate\Plugin\Customer\Model;

class EmailNotification
{
    public $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Disable default magento new account email
     * @param \Magento\Customer\Model\EmailNotification $subject
     * @param callable $proceed
     * @param $customer
     * @param string $type
     * @param string $backUrl
     * @param int $storeId
     * @param null $sendemailStoreId
     */
    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotification $subject,
        callable $proceed,
        $customer,
        $type = \Magento\Customer\Model\EmailNotification::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {
        if ($this->registry->registry('affiliate_disable_new_account_email')) {
            return;
        }

        return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
    }
}
