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

namespace Magetrend\Affiliate\Block\Customer;

use Magetrend\Affiliate\Model\Account;

/**
 * Class for delimiter.
 */

class Delimiter extends \Magento\Framework\View\Element\Template
{
    public $accountHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     * @since 100.2.0
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    //@codingStandardsIgnoreLine
    protected function _toHtml()
    {
        if (!$this->accountHelper->getCurrentAccount()->getId()) {
            return '';
        }

        if ($this->accountHelper->getCurrentAccount()->getStatus() != Account::STATUS_ACTIVE) {
            return '';
        }

        return parent::_toHtml();
    }
}
