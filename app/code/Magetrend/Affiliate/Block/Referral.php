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

namespace Magetrend\Affiliate\Block;

use Magento\Framework\View\Element\Template;

class Referral extends \Magento\Framework\View\Element\Template
{
    public $jsonHelper;

    public $moduleHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return [
            'paramKey' => \Magetrend\Affiliate\Helper\Data::REFERRAL_PARAM_KEY,
            'actionUrl' => $this->getUrl('affiliate/referral/save', ['ajax' => 1])
        ];
    }

    public function getJsonConfig()
    {
        return $this->jsonHelper->jsonEncode($this->getConfig());
    }

    public function isActive()
    {
        return $this->moduleHelper->isActive();
    }
}
