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
 * Settings block class
 */
class Settings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magetrend\Affiliate\Helper\Account
     */
    public $accountHelper;

    /**
     * Settings constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magetrend\Affiliate\Helper\Account $accountHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns current referral account
     * @return \Magetrend\Affiliate\Model\Account
     */
    public function getAccount()
    {
        return $this->accountHelper->getCurrentAccount();
    }
}
