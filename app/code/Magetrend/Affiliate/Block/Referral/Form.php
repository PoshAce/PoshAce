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

namespace Magetrend\Affiliate\Block\Referral;

class Form extends \Magento\Framework\View\Element\Template
{
    public $accountHelper;

    public $jsonHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getReferralCode()
    {
        return $this->accountHelper->getReferralCode();
    }

    public function getJsonConfig()
    {
        return $this->jsonHelper->jsonEncode($this->getConfig());
    }

    public function getConfig()
    {
        return [
            'referral_code' => $this->getReferralCode(),
            'referral_key' => \Magetrend\Affiliate\Helper\Account::REFERRAL_PARAM_KEY
        ];
    }
}
