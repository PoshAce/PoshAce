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

namespace Magetrend\Affiliate\Block\Withdrawal;

class Balance extends \Magento\Framework\View\Element\Template
{
    public $accountHelper;

    public $moduleHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->accountHelper = $accountHelper;
        parent::__construct($context, $data);
    }

    public function getAccount()
    {
        return $this->accountHelper->getCurrentAccount();
    }

    public function getMinimumWithdrawAmount()
    {
        return $this->moduleHelper->getMinimumWithdrawAmount(true, $this->getAccount());
    }
}
